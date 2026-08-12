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

namespace format_flexmix\output;

// Moodle's classes/ autoloader is filesystem-convention based, not
// version-aware: on Moodle 3.x, something resolving the renderer factory
// can still trigger class_exists('format_flexmix\output\renderer'), which
// autoloads and executes this file even though this output-class API is a
// Moodle 4.0+ concept. On 3.x neither of the classes below exists, so this
// must not blindly class_alias() to something that isn't there - leaving
// flexmix_renderer_base_shim (and therefore `renderer`) undefined below is
// the correct outcome: Moodle 3.x's renderer factory then falls back to
// the legacy top-level renderer.php instead.
if (!class_exists(__NAMESPACE__ . '\\flexmix_renderer_base_shim')) {
    if (class_exists('format_weeks\\output\\renderer')) {
        class_alias('format_weeks\\output\\renderer', __NAMESPACE__ . '\\flexmix_renderer_base_shim');
    } else if (class_exists('core_courseformat\\output\\section_renderer')) {
        class_alias('core_courseformat\\output\\section_renderer', __NAMESPACE__ . '\\flexmix_renderer_base_shim');
    }
}

if (class_exists(__NAMESPACE__ . '\\flexmix_renderer_base_shim')) {
    /**
     * Renderer for format_flexmix (Moodle 4.0+).
     *
     * All type-aware section naming lives in lib.php, so nothing needs to be
     * overridden here beyond reusing the core weeks renderer.
     *
     * @package     format_flexmix
     * @copyright   2026 Hiroki Maezawa
     * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class renderer extends flexmix_renderer_base_shim {
    }
}
