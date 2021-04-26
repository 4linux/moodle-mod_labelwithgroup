<?php
/**
 * Label with group module
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

$id = optional_param('id', 0, PARAM_INT);
$l = optional_param('l', 0, PARAM_INT);

if ($id) {
    $PAGE->set_url('/mod/labelwithgroup/index.php', array('id' => $id));
    if (! $cm = get_coursemodule_from_id('labelwithgroup', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf');
    }

    if (! $labelwithgroup = $DB->get_record("labelwithgroup", array("id" => $cm->instance))) {
        print_error('invalidcoursemodule');
    }

} else {
    $PAGE->set_url('/mod/labelwithgroup/index.php', array('l' => $l));
    if (! $labelwithgroup = $DB->get_record("lablabelwithgroupel", array("id" => $l))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id" => $labelwithgroup->course)) ) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("labelwithgroup", $labelwithgroup->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course, true, $cm);

redirect("$CFG->wwwroot/course/view.php?id=$course->id");


