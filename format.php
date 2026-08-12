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
 * Entry point Moodle includes (from course/view.php) to render the course
 * content area for format_flexmix. $course and $displaysection are already
 * in scope, set up by the including script.
 *
 * Moodle 4.0+ replaced the old print_multiple_section_page()/
 * print_single_section_page() renderer API with a reactive output-class
 * API (get_output_classname()/render()). Both are supported here, branching
 * at runtime on whether the newer method exists, so this one file works
 * unchanged on both Moodle 3.x and 4.0+.
 *
 * @package     format_flexmix
 * @copyright   2026 Hiroki Maezawa
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/completionlib.php');

$format = course_get_format($course);
$course = $format->get_course();

// Make sure section 0 is created.
course_create_sections_if_missing($course, 0);

$renderer = $PAGE->get_renderer('format_flexmix');

if (method_exists($format, 'get_output_classname')) {
    // Moodle 4.0+.
    if (!empty($displaysection)) {
        $format->set_section_number($displaysection);
    }
    $outputclass = $format->get_output_classname('content');
    $output = new $outputclass($format);
    echo $renderer->render($output);
} else {
    // Moodle 3.x.
    if (!empty($displaysection)) {
        $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
    } else {
        $renderer->print_multiple_section_page($course, null, null, null, null);
    }
}
