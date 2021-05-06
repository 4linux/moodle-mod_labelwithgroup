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
 * Restore label with group activity task
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/labelwithgroup/backup/moodle2/restore_labelwithgroup_stepslib.php'); // Because it exists (must)
/**
 * Labelwithgroup restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_labelwithgroup_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Labelwithgroup only has one structure step
        $this->add_step(new restore_labelwithgroup_activity_structure_step('labelwithgroup_structure', 'labelwithgroup.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    public static function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('labelwithgroup', array('intro'), 'labelwithgroup');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * To the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        return array();
    }

    /**
     * Define the restore log rules that will be applied
     * By the restore_logs_processor when restoring
     * Labelwithgroup logs. It must return one array
     * Of restore_log_rule objects
     */
    public static function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('labelwithgroup', 'add', 'view.php?id={course_module}', '{labelwithgroup}');
        $rules[] = new restore_log_rule('labelwithgroup', 'update', 'view.php?id={course_module}', '{labelwithgroup}');
        $rules[] = new restore_log_rule('labelwithgroup', 'view', 'view.php?id={course_module}', '{labelwithgroup}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the restore_logs_processor when restoring
     * course logs. It must return one array
     * of restore_log_rule objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    public static function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('labelwithgroup', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
