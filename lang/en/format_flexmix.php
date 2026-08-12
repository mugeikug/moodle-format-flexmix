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
 * English strings for format_flexmix.
 *
 * @package     format_flexmix
 * @copyright   2026 Hiroki Maezawa
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Weeks / irregular sessions / topics (mixed)';
$string['section0name'] = 'General';
$string['sectionoutline'] = 'Course sections';

// Standard course-format strings expected by core (course/format/classes/output/local/content/section/controlmenu.php
// and friends). Kept generic since a section can be a week, a topic or an irregular session.
$string['addsections'] = 'Add section';
$string['currentsection'] = 'This section';
$string['editsection'] = 'Edit section';
$string['editsectionname'] = 'Edit section name';
$string['deletesection'] = 'Delete section';
$string['newsectionname'] = 'New name for section {$a}';
$string['sectionname'] = 'Section';
$string['hidefromothers'] = 'Hide section';
$string['showfromothers'] = 'Show section';

$string['sectiontype'] = 'Section type';
$string['sectiontype_help'] = 'Choose how this section behaves:

* **Week** – dated automatically, like the Weekly format. Its date is worked out by counting only the other "Week" sections before it, so inserting a Topic or Irregular session section elsewhere does not shift the dates of the weeks that follow.
* **Topic** – no date, like the Topics format. Give it its own name.
* **Irregular session** – a one-off section (e.g. a makeup class, or a class moved to a different day) that does not belong to the regular weekly sequence. Optionally give it its own date, and give it its own name.';
$string['sectiontype_week'] = 'Week (dated)';
$string['sectiontype_topic'] = 'Topic (undated)';
$string['sectiontype_adhoc'] = 'Irregular session (makeup class, rescheduled date, etc.)';

$string['sectiondate'] = 'Section date';
$string['sectiondate_help'] = 'For a Week section, overrides the automatically calculated date. For an Irregular session, sets the date shown in its default name. Leave empty to use the automatic date (Week) or no date (Irregular session). Not used for Topic sections.';

$string['topicsection'] = 'Topic {$a}';
$string['adhocsection'] = 'Irregular session';

$string['privacy:metadata'] = 'The Weeks / irregular sessions / topics (mixed) course format only stores section-structure metadata (section type and an optional session date); it does not store any personal data.';
