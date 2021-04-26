@mod @mod_labelwithgroup

Feature: Check label group works
  In order to check label group works
  As a teacher
  I should create label with group activity

  @javascript
  Scenario: Label with group activity with no template should not be shown.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
      | student2 | Student | First | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
    And the following "group members" exist:
      | user | group |
      | teacher | G1 |
      | student1 | G1 |
      | student2 | G2 |
    Given I log in as "teacher"
    And I am on "Test" course homepage with editing mode on
    When I add a "label with group" to section "1" and I fill the form with:
      | Template | Without a template |
      | Content | Label with group without a template should not be shown |
      | Group | Group 2 |
    Then "Label with group without a template should not be shown" activity should be visible
    And I turn editing mode off
    And "Label with group without a template should not be shown" activity should be visible
    And I log out
    And I log in as "student2"
    And I am on "Test" course homepage
    And I should see "Label with group without a template should not be shown"
    And I log out
    And I log in as "student1"
    And I am on "Test" course homepage
    And I should not see "Label with group without a template should not be shown"

