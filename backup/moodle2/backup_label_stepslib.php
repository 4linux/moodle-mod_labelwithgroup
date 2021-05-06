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
 * Activity structure step backup
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_labelwithgroup_activity_structure_step extends backup_activity_structure_step {

    /**
     * Prepare activity structure
     *
     * @return mixed
     */
    protected function define_structure() {

        $userinfo = $this->get_setting_value('userinfo');

        $labelwithgroup = new backup_nested_element('labelwithgroup', array('id'), array(
            'name', 'intro', 'introformat', 'timemodified'));

        $labelwithgroup->set_source_table('labelwithgroup', array('id' => backup::VAR_ACTIVITYID));

        $labelwithgroup->annotate_files('mod_labelwithgroup', 'intro', null);

        return $this->prepare_activity_structure($labelwithgroup);
    }
}
