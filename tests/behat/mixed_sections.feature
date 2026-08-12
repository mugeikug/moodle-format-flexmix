@format @format_flexmix
Feature: Teachers can use a course that mixes weekly, topic and extra-session sections
  In order to run a course whose schedule is not strictly weekly
  As a teacher
  I need to create a course in the "Weeks / topics / extra sessions (mixed)" format and see it render

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format   | numsections | startdate       |
      | Course 1 | C1        | 0        | flexmix  | 5           | ##5 January 2026## |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: A course in the mixed format loads and shows its general section
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    Then I should see "General"

  # NOTE: this feature intentionally stops short of driving the "edit section"
  # panel (to set a section's type/date) via Behat, because that UI changed
  # substantially between Moodle 3.11 and 4.1 and the exact step wording
  # needs to be confirmed against real 3.11 and 4.1 instances before it can
  # be trusted in CI on both branches. See README.md for manual verification
  # steps covering that flow in the meantime.
