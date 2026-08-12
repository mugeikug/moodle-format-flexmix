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
 * Moodle 4.0+ looks up classes/output/renderer.php instead and never loads
 * this file, so it is only relevant on Moodle 3.x branches where the core
 * weeks format still ships its renderer here. All of the type-aware section
 * naming lives in lib.php, so nothing needs to be overridden here beyond
 * reusing the core weeks renderer.
 *
 * @package     format_flexmix
 * @copyright   2026 Hiroki Maezawa
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (file_exists($CFG->dirroot . '/course/format/weeks/renderer.php')) {
    require_once($CFG->dirroot . '/course/format/weeks/renderer.php');
}

if (class_exists('format_weeks_renderer')) {
    /**
     * Reuses the core weeks renderer unmodified.
     */
    class format_flexmix_renderer extends format_weeks_renderer {
    }
}
