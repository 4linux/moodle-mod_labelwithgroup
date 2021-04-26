<?php
/**
 * Label with group module admin settings and defaults
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox('labelwithgroup/dndmedia',
        get_string('dndmedia', 'mod_labelwithgroup'), get_string('configdndmedia', 'mod_labelwithgroup'), 1));

    $settings->add(new admin_setting_configtext('label/dndresizewidth',
        get_string('dndresizewidth', 'mod_label'), get_string('configdndresizewidth', 'mod_labelwithgroup'), 400, PARAM_INT, 6));

    $settings->add(new admin_setting_configtext('label/dndresizeheight',
        get_string('dndresizeheight', 'mod_label'), get_string('configdndresizeheight', 'mod_labelwithgroup'), 400, PARAM_INT, 6));
}
