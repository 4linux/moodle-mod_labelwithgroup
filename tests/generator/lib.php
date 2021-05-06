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
 * mod_labelwithgroup data generator
 *
 * @package    mod_labelwithgroup
 * @category   test
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_labelwithgroup data generator
 *
 * @package    mod_labelwithgroup
 * @category   test
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_labelwithgroup_generator extends testing_module_generator {

    /**
     * Create a label with group instance
     *
     * @param null $record
     * @param array|null $options
     * @return mixed
     */
    public function create_instance($record = null, array $options = null) {
        $record = (array)$record;
        $record['showdescription'] = 1;
        $record['content'] = [
            "Label with group test content 1",
            "Label with group test content 2",
            "Label with group test content 3"
        ];
        return parent::create_instance($record, $options);
    }
}
