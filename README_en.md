# format_flexmix

*([日本語版README](README.md))*

A Moodle course format plugin. It lets "Weekly" sections, "Topic" sections, and one-off
"Irregular" sections (makeup classes, rescheduled classes, etc.) **coexist within a single
course**.

The standard Weekly format computes every section's date purely from its position in the
section list. Because of that, inserting a single makeup-class section partway through the
course shifts the displayed date of every week that follows it. This plugin lets you set an
explicit type (Week / Topic / Irregular) on each section, and computes week dates by
**counting only the sections typed as "Week"**, so inserting a Topic or Irregular section in
between does not shift the dates of the weeks that follow.

## Main features

- The section edit panel (via the "Edit section" item in the menu next to a section's name)
  gains two extra fields on top of the standard ones: "Section type" (Week / Topic /
  Irregular) and "Section date".
- **Week**: dated automatically, like the Weekly format (the date can be overridden per
  section via "Section date").
- **Topic**: undated, like the Topics format; defaults to "Topic N".
- **Irregular**: a one-off section that doesn't belong to the regular weekly sequence.
  Setting "Section date" makes that date appear in the section's default name.
- For any type, you can freely rename the section using Moodle's standard renaming feature
  (a custom name always takes priority over the auto-generated default).

## Usage

1. In the course creation/edit screen, choose "Weeks / irregular sessions / topics (mixed)"
   as the course format (you can also switch an existing Weekly-format course to it).
2. Add sections as usual. New sections default to the "Week" type.
3. For a section you want to turn into a Topic, or into a one-off irregular session, open
   "Edit section" from that section's edit menu and change "Section type".
4. To show a date for an Irregular section, set "Section date". For Topic and Irregular
   sections, you're expected to give the section its own name.

## Installation

Copy (or symlink) the contents of this directory into Moodle's `course/format/flexmix/`,
then log in as a site administrator to complete the plugin installation. Alternatively, on
sites where the web server has write access to the codebase, a site administrator can
upload a correctly-named zip via Site administration → Plugins → Install plugins (no
FTP/SSH access needed) - see the "For distribution" note below.

## Verified / supported versions

`version.php`'s `requires`/`supported` declare Moodle 3.11 through 4.1. GitHub Actions
(`.github/workflows/ci.yml`) continuously verifies phplint / phpcs / phpdoc / validate /
savepoints / mustache / PHPUnit / Behat pass on both `MOODLE_311_STABLE` and
`MOODLE_401_STABLE`. It has also been manually verified by installing on real Moodle
environments (Moodle 4.1.12 under Docker, and a live production server) - course creation,
section editing, the automatic Announcements forum, and display styling have all been
checked by hand.

Moodle 4.0 substantially changed both the course format base class (`format_base` →
`\core_courseformat\base`) and the output/rendering layer, so this plugin uses the
following techniques to support both branches from a single codebase:

- `lib.php`: uses `class_alias()` to alias whichever base class actually exists at runtime
  (3.11's `format_base`, or 4.0+'s `\core_courseformat\base`) to a common name, then
  extends that.
- `renderer.php` (for 3.x) / `classes/output/renderer.php` (for 4.0+): each is only ever
  loaded on its respective branch, so the other one sits there unused and harmless. On
  Moodle 4.0+, `format_section_renderer_base` is only a deprecated alias, so that branch is
  written to never reference it at all, using the presence of `\core_courseformat\base` to
  tell the branches apart instead.
- `styles.css`: Boost theme's styling for `ul.topics`/`ul.weeks` (removing the bullet
  marker, spacing, etc.) doesn't apply to `ul.flexmix`, so this plugin ships the equivalent
  rules itself.
- `lang/*/format_flexmix.php`: defines the per-format strings core expects
  (`hidefromothers`/`showfromothers` and others) that have no core fallback - leaving them
  undefined makes them show up literally as `[[hidefromothers]]` on every section's edit
  menu.

## For distribution

If you're handing this plugin to someone who can't copy files onto the server directly
(FTP/SSH), they can instead use Moodle's browser-based "Install plugins" page as a site
administrator, provided the web server process has write access to the codebase. The zip
you give them must have a top-level folder named exactly `flexmix` inside it - a zip
downloaded straight from GitHub ("Code → Download ZIP") extracts to
`moodle-format-flexmix-main` instead and will not be accepted as-is; rename the folder to
`flexmix` and re-zip it first.

## Development

```bash
composer create-project -n --no-dev --prefer-dist moodlehq/moodle-plugin-ci ci ^4
php ci/bin/moodle-plugin-ci install --plugin ./format_flexmix --db-host=127.0.0.1
php ci/bin/moodle-plugin-ci phpunit
php ci/bin/moodle-plugin-ci behat
```

## License

GNU GPL v3 or later
