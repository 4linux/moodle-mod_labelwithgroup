<?php
/**
 * mod_labelwithgroup data generator
 *
 * @package    mod_labelwithgroup
 * @category   test
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class mod_labelwithgroup_generator extends testing_module_generator {

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
