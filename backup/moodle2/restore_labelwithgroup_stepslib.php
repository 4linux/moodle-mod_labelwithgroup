<?php

/**
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ($CFG->dirroot.'/lib/grouplib.php');

/**
 * Define all the restore steps that will be used by the restore_url_activity_task
 */

/**
 * Structure step to restore one labelwithgroup activity
 */
class restore_labelwithgroup_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('labelwithgroup', '/activity/labelwithgroup');
        $paths[] = new restore_path_element('labelwithgroup_content', '/activity/labelwithgroup/labelwithgroup_content/labelwithgroup_contentitem');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_labelwithgroup($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $oldcourseid = $data->course;

        $data->course = $this->get_courseid();
        $data->groupid = $this->get_new_groupid($data->groupid, $oldcourseid, $data->course);

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.

        // insert the labelwithgroup record
        $newitemid = $DB->insert_record('labelwithgroup', $data);

        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_labelwithgroup_content($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->labelwithgroup_id = $this->get_new_parentid('labelwithgroup');

        $newitemid = $DB->insert_record('labelwithgroup_content', $data);

        $this->set_mapping('labelwithgroup_contentitem', $oldid, $newitemid);
    }

    protected function after_execute() {
        // Add labelwithgroup related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_labelwithgroup', 'intro', null);
    }

    /**
     * Find and retrieve new group id based on old group id and old course id
     *
     * @param $oldgroupid
     * @param $oldcourseid
     * @param $newcourseid
     * @return int
     */
    protected function get_new_groupid($oldgroupid, $oldcourseid, $newcourseid) {
        $oldgroups = groups_get_all_groups($oldcourseid);
        $newgroups = groups_get_all_groups($newcourseid);

        $newgroupid = -1;

        foreach ($oldgroups as $oldgroup) {
            if ($newgroupid > -1) {
                break;
            }

            if ($oldgroup->id == $oldgroupid) {

                $oldgroupname = $oldgroup->name;

                foreach ($newgroups as $newgroup) {

                    if ($newgroupid > -1) {
                        break;
                    }

                    if ($newgroup->name == $oldgroupname) {
                        $newgroupid = $newgroup->id;
                    }
                }
            }
        }

        return $newgroupid > -1 ? $newgroupid : $oldgroupid;

    }

}
