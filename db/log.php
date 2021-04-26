<?php

/**
 * Definition of log events
 *
 * @package    mod_labelwithgroup
 * @category   Log
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'labelwithgroup', 'action'=>'add', 'mtable'=>'labelwithgroup', 'field'=>'name'),
    array('module'=>'labelwithgroup', 'action'=>'update', 'mtable'=>'labelwithgroup', 'field'=>'name'),
);