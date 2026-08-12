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

namespace format_flexmix;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for the type-aware section naming/date logic in format_flexmix.
 *
 * The key behaviour under test: a "week" section's computed date is based
 * on counting only the other "week" sections before it, so inserting a
 * "topic" or "adhoc" section in between must not shift the dates of the
 * weeks that follow.
 *
 * @package     format_flexmix
 * @copyright   2026 Hiroki Maezawa
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \format_flexmix
 */
class section_naming_test extends \advanced_testcase {

    /**
     * Sets a section's type (and optional date) via the same
     * update_section_format_options() path the "edit section" form uses.
     */
    private function set_section_type(\stdClass $course, int $sectionnum, string $type, int $date = 0): void {
        global $DB;
        $sectionid = $DB->get_field(
            'course_sections',
            'id',
            ['course' => $course->id, 'section' => $sectionnum],
            MUST_EXIST
        );
        course_get_format($course)->update_section_format_options([
            'id' => $sectionid,
            'sectiontype' => $type,
            'sectiondate' => $date,
        ]);
        rebuild_course_cache($course->id, true);
    }

    public function test_week_dates_skip_non_week_sections(): void {
        $this->resetAfterTest();

        // A fixed Monday so the expected dates below are unambiguous.
        $startdate = strtotime('2026-01-05 00:00:00');
        $course = $this->getDataGenerator()->create_course([
            'format' => 'flexmix',
            'numsections' => 5,
            'startdate' => $startdate,
        ]);

        // Section 3 is an extra/adhoc session: it must not consume a "week" slot.
        $this->set_section_type($course, 3, \format_flexmix::SECTIONTYPE_ADHOC, strtotime('2026-01-20 00:00:00'));

        $format = course_get_format($course);
        $dateformat = get_string('strftimedateshort', 'langconfig');

        // Sections 1, 2, 4, 5 are the 1st, 2nd, 3rd and 4th "week" sections
        // respectively (section 4 is 3rd because section 3 is skipped).
        $this->assertStringStartsWith(
            userdate($startdate, $dateformat),
            $format->get_section_name(1)
        );
        $this->assertStringStartsWith(
            userdate($startdate + 7 * DAYSECS, $dateformat),
            $format->get_section_name(2)
        );
        $this->assertStringStartsWith(
            userdate($startdate + 14 * DAYSECS, $dateformat),
            $format->get_section_name(4)
        );
        $this->assertStringStartsWith(
            userdate($startdate + 21 * DAYSECS, $dateformat),
            $format->get_section_name(5)
        );

        // The adhoc section shows its own explicit date, not a week range.
        $this->assertSame(
            userdate(strtotime('2026-01-20 00:00:00'), $dateformat),
            $format->get_section_name(3)
        );
    }

    public function test_topic_and_adhoc_default_names(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'format' => 'flexmix',
            'numsections' => 4,
            'startdate' => strtotime('2026-01-05 00:00:00'),
        ]);

        $this->set_section_type($course, 1, \format_flexmix::SECTIONTYPE_TOPIC);
        $this->set_section_type($course, 2, \format_flexmix::SECTIONTYPE_TOPIC);
        $this->set_section_type($course, 3, \format_flexmix::SECTIONTYPE_ADHOC);
        // Section 4 is left as the default "week" type.

        $format = course_get_format($course);

        $this->assertSame(get_string('topicsection', 'format_flexmix', 1), $format->get_section_name(1));
        $this->assertSame(get_string('topicsection', 'format_flexmix', 2), $format->get_section_name(2));
        $this->assertSame(get_string('adhocsection', 'format_flexmix'), $format->get_section_name(3));
        $this->assertNotSame(get_string('adhocsection', 'format_flexmix'), $format->get_section_name(4));
    }
}
