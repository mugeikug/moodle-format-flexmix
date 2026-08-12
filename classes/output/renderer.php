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

defined('MOODLE_INTERNAL') || die();

// Moodle 3.x never loads this file (it looks for the legacy top-level
// renderer.php instead), so this only runs on Moodle 4.0+. Prefer reusing
// core weeks' namespaced renderer (inplace-editable section titles etc.);
// fall back to the generic core_courseformat section renderer if a future
// branch ever removes format_weeks's one.
if (!class_exists(__NAMESPACE__ . '\\flexmix_renderer_base_shim')) {
    if (class_exists('format_weeks\\output\\renderer')) {
        class_alias('format_weeks\\output\\renderer', __NAMESPACE__ . '\\flexmix_renderer_base_shim');
    } else {
        class_alias('core_courseformat\\output\\section_renderer', __NAMESPACE__ . '\\flexmix_renderer_base_shim');
    }
}

/**
 * Renderer for format_flexmix (Moodle 4.0+).
 *
 * All type-aware section naming lives in lib.php, so nothing needs to be
 * overridden here beyond reusing the core weeks renderer.
 */
class renderer extends flexmix_renderer_base_shim {
}
