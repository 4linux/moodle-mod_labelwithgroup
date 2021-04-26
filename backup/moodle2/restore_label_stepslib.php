<?php
/**
 *
 * Activity structure step restore
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_labelwithgroup_activity_structure_step extends restore_activity_structure_step {

    /**
     * Prepare activity structure
     *
     * @return mixed
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('labelwithgroup', '/activity/labelwithgroup');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process instance
     *
     * @param $data
     */
    protected function process_labelwithgroup($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.

        // insert the labelwithgroup record
        $newitemid = $DB->insert_record('labelwithgroup', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Add label with group instance related files
     */
    protected function after_execute() {
        $this->add_related_files('mod_labelwithgroup', 'intro', null);
    }

}
