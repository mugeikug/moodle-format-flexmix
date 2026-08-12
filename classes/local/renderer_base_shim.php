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

namespace format_flexmix\local;

/**
 * Empty placeholder base class, used by renderer.php as the parent of
 * format_flexmix_renderer on Moodle 4.0+, where that class is never
 * actually instantiated (classes/output/renderer.php's namespaced renderer
 * wins instead). Kept in its own file because PSR1 does not allow more
 * than one class declaration per file, and renderer.php already declares
 * format_flexmix_renderer itself.
 *
 * @package     format_flexmix
 * @copyright   2026 Hiroki Maezawa
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer_base_shim {
}
