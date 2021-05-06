<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Label external with group functions and service definitions.
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_labelwithgroup_get_labelswithgroup_by_courses' => array(
        'classname'     => 'mod_labelwithgroup_external',
        'methodname'    => 'get_labelswithgroup_by_courses',
        'description'   => 'Returns a list of labels in a provided list of courses, if no list is provided all labels that the user
                            can view will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/labelwithgroup:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'mod_labelwithgroup_get_labelswithgroup_by_user' => array(
        'classname'     => 'mod_labelwithgroup_external',
        'methodname'    => 'get_labelswithgroup_by_user',
        'description'   => 'Returns a list of labels by current user.',
        'type'          => 'read',
        'capabilities'  => 'mod/labelwithgroup:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        'ajax'          => true
    )

);
