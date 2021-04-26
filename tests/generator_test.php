<?php
/**
 * PHPUnit label generator tests
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit label generator tests
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_labelwithgroup_generator_testcase extends advanced_testcase {
    public function test_generator() {
        global $DB;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('labelwithgroup'));

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_labelwithgroup_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_labelwithgroup');
        $this->assertInstanceOf('mod_labelwithgroup_generator', $generator);
        $this->assertEquals('labelwithgroup', $generator->get_modulename());

        $generator->create_instance(array('course' => $course->id));
        $generator->create_instance(array('course' => $course->id));
        $labelwithgroup = $generator->create_instance(array('course' => $course->id));
        $this->assertEquals(3, $DB->count_records('labelwithgroup'));

        $contents = $DB->get_records('labelwithgroup_content', [ 'labelwithgroup_id' => $labelwithgroup->id ]);
        $this->assertEquals(3, count($contents));

        foreach ($contents as $content) {
            $this->assertEquals($labelwithgroup->id, $content->labelwithgroup_id);
            $this->assertContains("Label with group test content", $content->content);
        }

        $cm = get_coursemodule_from_instance('labelwithgroup', $labelwithgroup->id);
        $this->assertEquals($labelwithgroup->id, $cm->instance);
        $this->assertEquals('labelwithgroup', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = context_module::instance($cm->id);
        $this->assertEquals($labelwithgroup->cmid, $context->instanceid);
    }
}
