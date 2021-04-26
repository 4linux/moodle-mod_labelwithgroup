@mod @mod_labelwithgroup

Feature: create slide template
  In order to create a slide template
  As a teacher
  I should create label with group activity and set a slide template

  @javascript
  Scenario: label ID number input box should be shown.
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
      | Template | Slide |
      | Slide 1 | Label with group with slide template slide 1 |
      | Slide 2 | Label with group with slide template slide 2 |
      | Group | All participants |
    Then "Label with group with slide template slide 1" activity should be visible
    And I should not see "Label with group with slide template slide 2"
    And I turn editing mode off
    And "Label with group with slide template slide 1" activity should be visible
    When I click on ".mod-labelwithgroup-carousel-control-next" "css_element"
    And I should see "Label with group with slide template slide 2"
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And I should see "Label with group with slide template slide 1"
    And I should not see "Label with group with slide template slide 2"
    When I click on ".mod-labelwithgroup-carousel-control-next" "css_element"
    And I should see "Label with group with slide template slide 2"
