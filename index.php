<?php

/**
 * Library of functions and constants for module labelwithgroup
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id',PARAM_INT);   // course

$PAGE->set_url('/mod/labelwithgroup/index.php', array('id'=>$id));

redirect("$CFG->wwwroot/course/view.php?id=$id");


