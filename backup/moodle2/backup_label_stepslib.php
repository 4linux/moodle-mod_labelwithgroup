<?php
/**
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_labelwithgroup_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        $userinfo = $this->get_setting_value('userinfo');

        $labelwithgroup = new backup_nested_element('labelwithgroup', array('id'), array(
            'name', 'intro', 'introformat', 'timemodified'));

        $labelwithgroup->set_source_table('labelwithgroup', array('id' => backup::VAR_ACTIVITYID));

        $labelwithgroup->annotate_files('mod_labelwithgroup', 'intro', null);

        return $this->prepare_activity_structure($labelwithgroup);
    }
}
