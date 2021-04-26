@mod @mod_labelwithgroup


Feature: create a label without template
  In order to create a label without template
  As a teacher
  I should create label with group activity and set a label without template

  @javascript
  Scenario: Label with group activity with no template should be shown.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | Teacher | Frist | teacher1@example.com |
      | student | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher | C1 | editingteacher |
      | student | C1 | student |
    Given I log in as "teacher"
    And I am on "Test" course homepage with editing mode on
    When I add a "label with group" to section "1" and I fill the form with:
      | Template | Without a template |
      | Content | Label with group without a template |
      | Group | All participants |
    Then "Label with group without a template" activity should be visible
    And I turn editing mode off
    And "Label with group without a template" activity should be visible
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And I should see "Label with group without a template"