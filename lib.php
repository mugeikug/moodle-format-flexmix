<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Main library for format_flexmix: a course format that lets individual
 * sections be typed as "week" (dated, sequential), "topic" (undated) or
 * "adhoc" (a one-off makeup/extra session with its own date), all within
 * the same course.
 *
 * @package     format_flexmix
 * @copyright   2026 Hiroki Maezawa
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/lib.php');

// Moodle 4.0+ moved the course format base class to \core_courseformat\base and
// no longer defines the legacy global format_base class. Moodle 3.11 (and
// earlier) only has the legacy format_base class. Alias whichever one exists
// to a single name so the same lib.php can extend it on either branch.
if (!class_exists('format_flexmix_base_shim')) {
    if (class_exists('core_courseformat\\base')) {
        class_alias('core_courseformat\\base', 'format_flexmix_base_shim');
    } else {
        class_alias('format_base', 'format_flexmix_base_shim');
    }
}

/**
 * Course format that mixes weekly, topic and ad-hoc (makeup class) sections.
 */
class format_flexmix extends format_flexmix_base_shim {
    /** @var string Section behaves like a weeks-format section (dated, sequential). */
    const SECTIONTYPE_WEEK = 'week';

    /** @var string Section behaves like a topics-format section (undated). */
    const SECTIONTYPE_TOPIC = 'topic';

    /** @var string Section is a one-off extra/makeup session with its own date. */
    const SECTIONTYPE_ADHOC = 'adhoc';

    /**
     * Returns true as this format uses sections.
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Returns true so new courses get the standard announcements forum in
     * section 0, same as weeks/topics.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
    }

    /**
     * Declares AJAX support (Moodle 3.x), same as weeks/topics. Without
     * this, course/lib.php's include_course_ajax() never runs, which also
     * disables the "how many sections do you want to add?" prompt on the
     * "Add section" link.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Declares compatibility with the Moodle 4.0+ reactive/component-based
     * course page (drag-and-drop reordering without a full page reload,
     * etc.), same as weeks. Ignored on Moodle 3.x, which has no such
     * concept.
     *
     * @return bool
     */
    public function supports_components() {
        return true;
    }

    /**
     * Returns the display name of the given section: the custom name if the
     * teacher set one, otherwise the type-aware default computed below.
     *
     * Moodle 4.0+'s core_courseformat\base::get_section_name() no longer
     * does this "custom name, else default" check itself (its own default
     * only appends a section number to a generic "sectionname" string), so
     * this needs to be restored explicitly here, the same way core weeks
     * and topics do it on both branches.
     *
     * @param int|stdClass $section Section object or field course_sections.section
     * @return string
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if ((string) $section->name !== '') {
            return format_string($section->name, true, ['context' => context_course::instance($this->courseid)]);
        }
        return $this->get_default_section_name($section);
    }

    /**
     * Returns the default section name based on its configured type.
     *
     * Week numbers and dates are computed by counting only the sections
     * that are themselves typed as "week" up to and including this one, so
     * inserting a topic or adhoc section in between does not shift the
     * dates of the weeks that follow it.
     *
     * @param int|stdClass $section Section object or field course_sections.section
     * @return string
     */
    public function get_default_section_name($section) {
        $sectionnum = is_object($section) ? $section->section : $section;

        if ($sectionnum == 0) {
            return get_string('section0name', 'format_flexmix');
        }

        $modinfo = get_fast_modinfo($this->get_course());
        $sectioninfo = $modinfo->get_section_info($sectionnum);
        $type = $this->get_section_type($sectioninfo);
        $dateformat = get_string('strftimedateshort', 'langconfig');

        switch ($type) {
            case self::SECTIONTYPE_TOPIC:
                $topicindex = $this->get_typed_section_index($sectioninfo, self::SECTIONTYPE_TOPIC);
                return get_string('topicsection', 'format_flexmix', $topicindex);

            case self::SECTIONTYPE_ADHOC:
                if (!empty($sectioninfo->sectiondate)) {
                    return userdate($sectioninfo->sectiondate, $dateformat);
                }
                return get_string('adhocsection', 'format_flexmix');

            case self::SECTIONTYPE_WEEK:
            default:
                [$start, $end] = $this->get_week_display_dates($sectioninfo);
                return userdate($start, $dateformat) . ' - ' . userdate($end, $dateformat);
        }
    }

    /**
     * Whether the given section is "now" for highlighting purposes.
     *
     * Topic sections are never current. Week and adhoc sections are current
     * if today falls within their (explicit or computed) date range.
     *
     * @param int|stdClass $section
     * @return bool
     */
    public function is_section_current($section) {
        $sectionnum = is_object($section) ? $section->section : $section;
        if ($sectionnum < 1) {
            return false;
        }

        $modinfo = get_fast_modinfo($this->get_course());
        $sectioninfo = $modinfo->get_section_info($sectionnum);
        $type = $this->get_section_type($sectioninfo);
        $timenow = time();

        if ($type === self::SECTIONTYPE_TOPIC) {
            return false;
        }

        if ($type === self::SECTIONTYPE_ADHOC) {
            if (empty($sectioninfo->sectiondate)) {
                return false;
            }
            return ($timenow >= $sectioninfo->sectiondate) && ($timenow < $sectioninfo->sectiondate + DAYSECS);
        }

        // Week type: current if today is within the (inclusive) week window.
        $start = !empty($sectioninfo->sectiondate) ? $sectioninfo->sectiondate
            : $this->get_week_section_dates($sectioninfo)->start;
        return ($timenow >= $start) && ($timenow < $start + (7 * DAYSECS));
    }

    /**
     * Course-level format options: same as core weeks/topics formats.
     *
     * @param bool $foreditform
     * @return array
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = [
                'hiddensections' => [
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ],
                'coursedisplay' => [
                    'default' => $courseconfig->coursedisplay,
                    'type' => PARAM_INT,
                ],
            ];
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            $courseformatoptionsedit = [
                'hiddensections' => [
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => [
                        [
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible'),
                        ],
                    ],
                ],
                'coursedisplay' => [
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => [
                        [
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi'),
                        ],
                    ],
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                ],
            ];
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Section-level format options: the type selector and the optional
     * explicit date, exposed via the standard "edit section" panel.
     *
     * @param bool $foreditform
     * @return array
     */
    public function section_format_options($foreditform = false) {
        static $sectionformatoptions = false;
        if ($sectionformatoptions === false) {
            $sectionformatoptions = [
                'sectiontype' => [
                    'default' => self::SECTIONTYPE_WEEK,
                    'type' => PARAM_ALPHA,
                    'cache' => true,
                    'cachedefault' => self::SECTIONTYPE_WEEK,
                ],
                'sectiondate' => [
                    'default' => 0,
                    'type' => PARAM_INT,
                    'cache' => true,
                    'cachedefault' => 0,
                ],
            ];
        }
        if ($foreditform && !isset($sectionformatoptions['sectiontype']['label'])) {
            $sectionformatoptionsedit = [
                'sectiontype' => [
                    'label' => new lang_string('sectiontype', 'format_flexmix'),
                    'element_type' => 'select',
                    'element_attributes' => [
                        [
                            self::SECTIONTYPE_WEEK => new lang_string('sectiontype_week', 'format_flexmix'),
                            self::SECTIONTYPE_TOPIC => new lang_string('sectiontype_topic', 'format_flexmix'),
                            self::SECTIONTYPE_ADHOC => new lang_string('sectiontype_adhoc', 'format_flexmix'),
                        ],
                    ],
                    'help' => 'sectiontype',
                    'help_component' => 'format_flexmix',
                ],
                'sectiondate' => [
                    'label' => new lang_string('sectiondate', 'format_flexmix'),
                    'element_type' => 'date_selector',
                    'element_attributes' => [
                        ['optional' => true],
                    ],
                    'help' => 'sectiondate',
                    'help_component' => 'format_flexmix',
                ],
            ];
            $sectionformatoptions = array_merge_recursive($sectionformatoptions, $sectionformatoptionsedit);
        }
        return $sectionformatoptions;
    }

    /**
     * Adds a hideIf() so the date picker is hidden for topic sections, which
     * never use it.
     *
     * @param MoodleQuickForm $mform
     * @param bool $forsection
     * @return array
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        $elements = parent::create_edit_form_elements($mform, $forsection);
        if ($forsection && $mform->elementExists('sectiondate') && $mform->elementExists('sectiontype')) {
            $mform->hideIf('sectiondate', 'sectiontype', 'eq', self::SECTIONTYPE_TOPIC);
        }
        return $elements;
    }

    /**
     * Reads the (validated) section type of a section, defaulting to "week"
     * for anything missing or unrecognised (e.g. sections created before
     * this option existed).
     *
     * @param stdClass $sectioninfo
     * @return string One of the SECTIONTYPE_* constants.
     */
    protected function get_section_type($sectioninfo) {
        $valid = [self::SECTIONTYPE_WEEK, self::SECTIONTYPE_TOPIC, self::SECTIONTYPE_ADHOC];
        if (!empty($sectioninfo->sectiontype) && in_array($sectioninfo->sectiontype, $valid, true)) {
            return $sectioninfo->sectiontype;
        }
        return self::SECTIONTYPE_WEEK;
    }

    /**
     * Counts how many sections of the given type appear at or before the
     * given section (section 0 excluded), i.e. the 1-based ordinal of this
     * section among sections of the same type.
     *
     * @param stdClass $sectioninfo
     * @param string $type One of the SECTIONTYPE_* constants.
     * @return int
     */
    protected function get_typed_section_index($sectioninfo, $type) {
        $modinfo = get_fast_modinfo($this->get_course());
        $count = 0;
        foreach ($modinfo->get_section_info_all() as $candidate) {
            if ($candidate->section == 0 || $candidate->section > $sectioninfo->section) {
                continue;
            }
            if ($this->get_section_type($candidate) === $type) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Computes the start/end timestamps of a week-type section counting
     * only week-type sections (see get_typed_section_index()), using the
     * same DateTime-based, DST-safe arithmetic as the core weeks format.
     *
     * @param stdClass $sectioninfo
     * @return stdClass with ->start and ->end timestamps (end is exclusive).
     */
    protected function get_week_section_dates($sectioninfo) {
        $weekindex = $this->get_typed_section_index($sectioninfo, self::SECTIONTYPE_WEEK);
        $course = $this->get_course();

        $date = new DateTime('@' . $course->startdate);
        $date->setTimezone(core_date::get_server_timezone_object());
        $date->modify('+' . (($weekindex - 1) * 7) . ' days');
        $start = $date->getTimestamp();
        $date->modify('+7 days');
        $end = $date->getTimestamp();

        return (object) ['start' => $start, 'end' => $end];
    }

    /**
     * Start/end dates to *display* for a week section: an explicit
     * sectiondate override if set, otherwise the computed week window
     * (displayed as an inclusive 7-day range, i.e. end minus one day).
     *
     * @param stdClass $sectioninfo
     * @return array [starttimestamp, endtimestamp]
     */
    protected function get_week_display_dates($sectioninfo) {
        if (!empty($sectioninfo->sectiondate)) {
            $start = $sectioninfo->sectiondate;
            $end = $start + (7 * DAYSECS) - DAYSECS;
            return [$start, $end];
        }
        $dates = $this->get_week_section_dates($sectioninfo);
        return [$dates->start, $dates->end - DAYSECS];
    }
}

/**
 * Implements callback to allow the section name to be edited inline (the
 * pencil icon next to a section title), same mechanism as core weeks/topics.
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function format_flexmix_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record('course_sections', ['id' => $itemid], '*', MUST_EXIST);
        $format = course_get_format($section->course);
        $course = $format->get_course();
        $context = context_course::instance($course->id);
        \external_api::validate_context($context);
        if ($itemtype === 'sectionname') {
            require_capability('moodle/course:setcurrentsection', $context);
        } else {
            require_capability('moodle/course:update', $context);
        }
        $newvalue = clean_param($newvalue, PARAM_RAW);
        return $format->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}
