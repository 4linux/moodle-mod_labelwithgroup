<?php

/**
 * Defines backup_labelwithgroup_activity_task class
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/labelwithgroup/backup/moodle2/backup_labelwithgroup_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the Label instance
 */
class backup_labelwithgroup_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the labelwithgroup.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_labelwithgroup_activity_structure_step('labelwithgroup_structure', 'labelwithgroup.xml'));
    }

    /**
     * No content encoding needed for this activity
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the same content with no changes
     */
    static public function encode_content_links($content) {
        return $content;
    }
}
