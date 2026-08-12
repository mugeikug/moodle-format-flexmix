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
 * Legacy (pre-4.0) renderer for format_flexmix.
 *
 * This file is include_once'd unconditionally by Moodle's renderer factory
 * on every branch (lib/outputfactories.php::standard_renderer_classnames()),
 * and moodle-plugin-ci's "validate" check expects to find the
 * format_flexmix_renderer class declared unconditionally at the top level
 * here (wrapping the whole class in an `if` made validate fail to find it).
 *
 * At the same time, on Moodle 4.0+ format_section_renderer_base is only a
 * deprecated autoload alias for \core_courseformat\output\section_renderer,
 * and merely referencing that deprecated name (even via class_exists())
 * raises a debugging() notice, which Behat treats as a test failure - so it
 * must never be looked up at all on that branch.
 *
 * Both requirements are satisfied by keeping the class declaration itself
 * unconditional, but resolving what it extends beforehand: an alias to the
 * real format_section_renderer_base on Moodle 3.x (checked for via
 * \core_courseformat\base, a real, non-deprecated 4.0+ class, never via the
 * deprecated name itself), or an alias to the harmless empty placeholder in
 * classes/local/renderer_base_shim.php on 4.0+, where format_flexmix_renderer
 * is never actually instantiated anyway (classes/output/renderer.php's
 * namespaced renderer wins instead). The placeholder has to live in its own
 * file because PSR1 does not allow more than one class per file.
 *
 * All of the type-aware section naming lives in lib.php; this only supplies
 * the generic markup/title that format_section_renderer_base requires.
 *
 * @package     format_flexmix
 * @copyright   2026 Hiroki Maezawa
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!class_exists('format_flexmix_renderer_base_shim')) {
    if (!class_exists('core_courseformat\\base')) {
        // Moodle 3.x: format_section_renderer_base is real, not deprecated.
        require_once($CFG->dirroot . '/course/format/renderer.php');
        class_alias('format_section_renderer_base', 'format_flexmix_renderer_base_shim');
    } else {
        // Moodle 4.0+: never reference format_section_renderer_base here.
        class_alias('format_flexmix\\local\\renderer_base_shim', 'format_flexmix_renderer_base_shim');
    }
}

/**
 * Renderer for format_flexmix (Moodle 3.x). Never instantiated on 4.0+.
 */
class format_flexmix_renderer extends format_flexmix_renderer_base_shim {
    /**
     * Generate the starting container html for a list of sections.
     *
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', ['class' => 'flexmix']);
    }

    /**
     * Generate the closing container html for a list of sections.
     *
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page.
     *
     * @return string the page title
     */
    protected function page_title() {
        return get_string('sectionoutline', 'format_flexmix');
    }

    /**
     * Generate the section title, wraps it in a link to the section page if
     * the page is to be displayed on a separate page.
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title to be displayed on the section page,
     * without a link.
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section, false));
    }
}
