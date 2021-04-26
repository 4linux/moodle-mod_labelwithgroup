<?php

/**
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_labelwithgroup_activity_task
 */

/**
 * Define the complete labelwithgroup structure for backup, with file and id annotations
 */
class backup_labelwithgroup_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $labelwithgroup = new backup_nested_element('labelwithgroup', array('id'), array(
            'name', 'intro', 'introformat', 'timemodified'));

        // Build the tree
        // (love this)

        // Define sources
        $labelwithgroup->set_source_table('labelwithgroup', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $labelwithgroup->annotate_files('mod_labelwithgroup', 'intro', null); // This file area hasn't itemid

        // Return the root element (labelwithgroup), wrapped into standard activity structure
        return $this->prepare_activity_structure($labelwithgroup);
    }
}
