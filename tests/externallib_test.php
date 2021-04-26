<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External mod_labelwithgroup functions unit tests
 *
 * @package    mod_labelwithgroup
 * @category   external
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */
class mod_labelwithgroup_external_testcase extends externallib_advanced_testcase {

    /**
     * Test test_mod_labelwithgroup_get_labelwithgroups_by_courses
     */
    public function test_mod_labelwithgroup_get_labelwithgroups_by_courses() {
        global $DB;

        $this->resetAfterTest(true);

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id);

        // First labelwithgroup.
        $record = new stdClass();
        $record->course = $course1->id;
        $labelwithgroup1 = self::getDataGenerator()->create_module('labelwithgroup', $record);

        // Second labelwithgroup.
        $record = new stdClass();
        $record->course = $course2->id;
        $labelwithgroup2 = self::getDataGenerator()->create_module('labelwithgroup', $record);

        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $student->id, $studentrole->id);

        self::setUser($student);

        $returndescription = mod_labelwithgroup_external::get_labelswithgroup_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'introfiles', 'timemodified',
                                'section', 'visible', 'groupmode', 'groupingid');

        // Add expected coursemodule and data.
        $labelwithgroup1->coursemodule = $labelwithgroup1->cmid;
        $labelwithgroup1->introformat = 1;
        $labelwithgroup1->section = 0;
        $labelwithgroup1->visible = true;
        $labelwithgroup1->groupmode = 0;
        $labelwithgroup1->groupingid = 0;
        $labelwithgroup1->introfiles = [];

        $labelwithgroup2->coursemodule = $labelwithgroup2->cmid;
        $labelwithgroup2->introformat = 1;
        $labelwithgroup2->section = 0;
        $labelwithgroup2->visible = true;
        $labelwithgroup2->groupmode = 0;
        $labelwithgroup2->groupingid = 0;
        $labelwithgroup2->introfiles = [];

        foreach ($expectedfields as $field) {
            $expected1[$field] = $labelwithgroup1->{$field};
            $expected2[$field] = $labelwithgroup2->{$field};
        }

        $expectedlabelwithgroups = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_labelwithgroup_external::get_labelswithgroup_by_courses(array($course2->id, $course1->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedlabelwithgroups, $result['labelswithgroup']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_labelwithgroup_external::get_labelswithgroup_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedlabelwithgroups, $result['labelswithgroup']);
        $this->assertCount(0, $result['warnings']);

        // Add a file to the intro.
        $filename = "file.txt";
        $filerecordinline = array(
            'contextid' => context_module::instance($labelwithgroup2->cmid)->id,
            'component' => 'mod_labelwithgroup',
            'filearea'  => 'intro',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        );
        $fs = get_file_storage();
        $timepost = time();
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        $result = mod_labelwithgroup_external::get_labelswithgroup_by_courses(array($course2->id, $course1->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertCount(1, $result['labelswithgroup'][0]['introfiles']);
        $this->assertEquals($filename, $result['labelswithgroup'][0]['introfiles'][0]['filename']);

        // Unenrol user from second course.
        $enrol->unenrol_user($instance2, $student->id);
        array_shift($expectedlabelwithgroups);

        // Call the external function without passing course id.
        $result = mod_labelwithgroup_external::get_labelswithgroup_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedlabelwithgroups, $result['labelswithgroup']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_labelwithgroup_external::get_labelswithgroup_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);
    }

    public function test_mod_labelwithgroup_get_labelwithgroups_by_user() {

    }
}
