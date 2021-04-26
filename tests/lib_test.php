<?php

/**
 * Unit tests for the activity labelwithgroup's lib.
 *
 * @package    mod_labelwithgroup
 * @category   test
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class mod_labelwithgroup_lib_testcase extends advanced_testcase {

    /**
     * Prepares things before this test case is initialised
     * @return void
     */
    public static function setUpBeforeClass() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/labelwithgroup/classes/templatefactory.php');
        require_once($CFG->dirroot . '/mod/filewithwatermark/lib.php');
    }

    /**
     * Set up.
     */
    public function setUp() {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    public function test_labelwithgroup_core_calendar_provide_event_action() {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $labelwithgroup = $this->getDataGenerator()->create_module('labelwithgroup', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $labelwithgroup->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_labelwithgroup_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_labelwithgroup_core_calendar_provide_event_action_as_non_user() {
        global $CFG;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $labelwithgroup = $this->getDataGenerator()->create_module('labelwithgroup', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $labelwithgroup->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_labelwithgroup_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_labelwithgroup_core_calendar_provide_event_action_in_hidden_section() {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $labelwithgroup = $this->getDataGenerator()->create_module('labelwithgroup', array('course' => $course->id));

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $labelwithgroup->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Set sections 0 as hidden.
        set_section_visible($course->id, 0, 0);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_labelwithgroup_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_labelwithgroup_core_calendar_provide_event_action_for_user() {
        global $CFG;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $labelwithgroup = $this->getDataGenerator()->create_module('labelwithgroup', array('course' => $course->id));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $labelwithgroup->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_labelwithgroup_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_labelwithgroup_core_calendar_provide_event_action_already_completed() {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $labelwithgroup = $this->getDataGenerator()->create_module('labelwithgroup', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('labelwithgroup', $labelwithgroup->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $labelwithgroup->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_labelwithgroup_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_labelwithgroup_core_calendar_provide_event_action_already_completed_for_user() {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $labelwithgroup = $this->getDataGenerator()->create_module('labelwithgroup', array('course' => $course->id),
                array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('labelwithgroup', $labelwithgroup->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $labelwithgroup->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the student.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_labelwithgroup_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Test none template
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_labelwithgroup_generate_none_template() {
        global $DB;

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $groupid = -1;

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_labelwithgroup');
        $this->assertInstanceOf('mod_labelwithgroup_generator', $generator);
        $this->assertEquals('labelwithgroup', $generator->get_modulename());

        $labelwithgroup = $generator->create_instance(array('course'=>$course->id));

        $contents = $DB->get_records('labelwithgroup_content', [ 'labelwithgroup_id' => $labelwithgroup->id ]);
        $this->assertEquals(3, count($contents));

        $templateentity = \mod_labelwithgroup\classes\templatefactory::get_template_by_type(\mod_labelwithgroup\classes\templatefactory::$TEMPLATE_TYPE_NONE);

        $contentsarr = [];

        for ($i = 1; $i <= 3; $i++) {
            $contentsarr[$i] = array_shift($contents)->content;
        }

        $template = $templateentity->process_content($contentsarr, '', $groupid, $course->id);

        $this->assertContains('data-group-id="'.$groupid.'"', $template);
        $this->assertContains('data-course-id="'.$course->id.'"', $template);
        $this->assertContains('mod_labelwithgroup_content', $template);
        $this->assertNotContains('collapse', $template);
        $this->assertNotContains('carousel', $template);

    }

    /**
     * Test collapse template
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_labelwithgroup_generate_collapse_template() {
        global $DB;

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $groupid = -1;

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_labelwithgroup');
        $this->assertInstanceOf('mod_labelwithgroup_generator', $generator);
        $this->assertEquals('labelwithgroup', $generator->get_modulename());

        $labelwithgroup = $generator->create_instance(array('course'=>$course->id));

        $contents = $DB->get_records('labelwithgroup_content', [ 'labelwithgroup_id' => $labelwithgroup->id ]);
        $this->assertEquals(3, count($contents));

        $templateentity = \mod_labelwithgroup\classes\templatefactory::get_template_by_type(\mod_labelwithgroup\classes\templatefactory::$TEMPLATE_TYPE_COLLAPSE);

        $contentsarr = [];

        for ($i = 1; $i <= 3; $i++) {
            $contentsarr[$i] = array_shift($contents)->content;
        }

        $template = $templateentity->process_content($contentsarr, '', $groupid, $course->id);

        $this->assertContains('data-group-id="'.$groupid.'"', $template);
        $this->assertContains('data-course-id="'.$course->id.'"', $template);
        $this->assertContains('mod_labelwithgroup_content', $template);
        $this->assertContains('collapse', $template);
        $this->assertNotContains('carousel', $template);

        for ($i = 1; $i <= 3; $i++) {
            $this->assertContains("mod_labelwithgroup_content{$i}", $template);
            $this->assertContains($contentsarr[$i], $template);
        }

    }

    /**
     * Test slide template
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_labelwithgroup_generate_slide_template() {
        global $DB;

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $groupid = -1;

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_labelwithgroup');
        $this->assertInstanceOf('mod_labelwithgroup_generator', $generator);
        $this->assertEquals('labelwithgroup', $generator->get_modulename());

        $labelwithgroup = $generator->create_instance(array('course'=>$course->id));

        $contents = $DB->get_records('labelwithgroup_content', [ 'labelwithgroup_id' => $labelwithgroup->id ]);
        $this->assertEquals(3, count($contents));

        $templateentity = \mod_labelwithgroup\classes\templatefactory::get_template_by_type(\mod_labelwithgroup\classes\templatefactory::$TEMPLATE_TYPE_SLIDE);

        $contentsarr = [];

        for ($i = 1; $i <= 3; $i++) {
            $contentsarr[$i] = array_shift($contents)->content;
        }

        $template = $templateentity->process_content($contentsarr, '', $groupid, $course->id);

        $this->assertContains('data-group-id="'.$groupid.'"', $template);
        $this->assertContains('data-course-id="'.$course->id.'"', $template);
        $this->assertContains('mod_labelwithgroup_content', $template);
        $this->assertNotContains('collapse', $template);
        $this->assertContains('carousel', $template);

        for ($i = 1; $i <= 3; $i++) {
            $this->assertContains("mod_labelwithgroup_content{$i}", $template);
            $this->assertContains($contentsarr[$i], $template);
        }

    }

    /**
     * Test collapse and slide template
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_labelwithgroup_generate_collapse_slide_template() {
        global $DB;

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $groupid = -1;

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_labelwithgroup');
        $this->assertInstanceOf('mod_labelwithgroup_generator', $generator);
        $this->assertEquals('labelwithgroup', $generator->get_modulename());

        $labelwithgroup = $generator->create_instance(array('course'=>$course->id));

        $contents = $DB->get_records('labelwithgroup_content', [ 'labelwithgroup_id' => $labelwithgroup->id ]);
        $this->assertEquals(3, count($contents));

        $templateentity = \mod_labelwithgroup\classes\templatefactory::get_template_by_type(\mod_labelwithgroup\classes\templatefactory::$TEMPLATE_TYPE_COLLAPSE_SLIDE);

        $contentsarr = [];

        for ($i = 1; $i <= 3; $i++) {
            $contentsarr[$i] = array_shift($contents)->content;
        }

        $template = $templateentity->process_content($contentsarr, '', $groupid, $course->id);

        $this->assertContains('data-group-id="'.$groupid.'"', $template);
        $this->assertContains('data-course-id="'.$course->id.'"', $template);
        $this->assertContains('mod_labelwithgroup_content', $template);
        $this->assertContains('collapse', $template);
        $this->assertContains('carousel', $template);

        for ($i = 1; $i <= 3; $i++) {
            $this->assertContains("mod_labelwithgroup_content{$i}", $template);
            $this->assertContains($contentsarr[$i], $template);
        }

    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'labelwithgroup';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }
}