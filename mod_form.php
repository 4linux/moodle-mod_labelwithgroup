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
 * Add labelwithgroup form
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');

use \mod_labelwithgroup\templatefactory;

/**
 * Add labelwithgroup form
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_labelwithgroup_mod_form extends moodleform_mod {

    /** @var array EDITOROPTIONS */
    const EDITOROPTIONS = [
        'trusttext' => true,
        'subdirs' => true,
        'maxfiles' => EDITOR_UNLIMITED_FILES
    ];
    /**
     * Build the form
     */
    public function definition() {
        global $PAGE, $CFG;

        $PAGE->force_settings_menu();

        $mform = $this->_form;

        // General header.
        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Templates field.
        $templatesinfo = [
            'none' => get_string('templatenone', 'mod_labelwithgroup'),
            'collapse' => get_string('templatecollapse', 'mod_labelwithgroup'),
            'slide' => get_string('templateslide', 'mod_labelwithgroup'),
            'collapse-slide' => get_string('templatecollapseslide', 'mod_labelwithgroup'),
        ];

        $mform->addElement('select', 'templatetype', get_string('template', 'mod_labelwithgroup'), $templatesinfo);

        // Title field.
        $mform->addElement('text', 'title', get_string('title', 'mod_labelwithgroup'), 'maxlength="255" size="100" ');
        $mform->setType('title', PARAM_TEXT);
        $mform->hideIf('title', 'templetype', 'in', [templatefactory::$templatetypeslide, templatefactory::$templatetypenone]);

        // Editor.

        $editoroptions = [
            'trusttext' => true,
            'subdirs' => true,
            'maxfiles' => EDITOR_UNLIMITED_FILES
        ];

        for ($i = 1; $i <= 25; $i++) {
            $editorname = 'content' . $i . '_editor';

            $mform->addElement('editor', $editorname, get_string('content', 'mod_labelwithgroup'), null, $editoroptions);
            $mform->setType($editorname, PARAM_RAW);

            if ($i !== 1) {
                $mform->hideIf(
                    $editorname, 'templatetype',
                    'in',
                    [
                        templatefactory::$templatetypecollapse,
                        templatefactory::$templatetypenone
                    ]
                );
            }
        }

        // Button.
        $mform->addElement('button', 'addslide', get_string("buttonaddslide", 'mod_labelwithgroup'));
        $mform->hideIf('addslide', 'templatetype', 'in', [templatefactory::$templatetypecollapse, templatefactory::$templatetypenone]);

        // Group field.
        $groups = groups_get_all_groups($this->get_course()->id);

        $groupsinfo = [];

        $groupsinfo[-1] = get_string('allparticipants');

        foreach ($groups as $group) {
            $groupsinfo[$group->id] = $group->name;
        }

        $mform->addElement('select', 'groupid', get_string('group'), $groupsinfo);

        $mform->addElement('hidden', 'showdescription', 1);
        $mform->setType('showdescription', PARAM_INT);

        // Standard fields.
        $this->standard_intro_elements(get_string('labelwithgrouptext', 'labelwithgroup'));

        $PAGE->requires->js_call_amd('mod_labelwithgroup/form_handler', 'init', [$CFG->lang]);

        $this->standard_coursemodule_elements();

        $this->add_action_buttons(true, false, null);
    }

    /**
     * Process data before the build
     *
     * @param $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        global $DB;

        parent::data_preprocessing($defaultvalues);

        $id = $defaultvalues['instance'];

        if (!empty($id)) {
            $contents = $DB->get_records('labelwithgroup_content', ['labelwithgroup_id' => $id]);

            $contentsid = array_keys($contents);

            for ($i = 1; $i <= count($contents); $i++) {

                $attrname = 'content' . $i . '_editor';

                $defaultvalues[$attrname] = [ 'text' => $contents[$contentsid[$i - 1]]->content];

            }
        }
    }

    /**
     * Process data after submit
     *
     * @param $data
     */
    function data_postprocessing($data) {
        parent::data_postprocessing($data);

        $templateentity = templatefactory::get_template_by_type($data->templatetype);

        $content = [];
        for ($i = 1; $i <= 25; $i++) {
            $attrname = 'content' . $i . '_editor';
            $attrval = $data->{$attrname}['text'];

            if (!empty($attrval) ) {
                $content[] = $attrval;
            }
        }

        $data->content = $content;

        $data->introeditor['text'] = $templateentity->process_content($content, $data->title, $data->groupid, $data->course);

    }

    /**
     * Load in existing data as form defaults
     *
     * @param array|stdClass $default_values
     * @throws coding_exception
     */
    public function set_data($default_values)
    {
        global $DB;

        if (!is_object($default_values)) {
            $default_values = (object)$default_values;
        }

        $context = context_course::instance($default_values->course);

        $editoroptions = array_merge(self::EDITOROPTIONS, [ 'context' => $context]);

        if ($default_values->id) {
            $contents = $DB->get_records('labelwithgroup_content', ['labelwithgroup_id' => $default_values->id]);

            $contentsid = array_keys($contents);

            for ($i = 1; $i <= count($contents); $i++) {

                $attrname = 'content' . $i;

                $default_values->{$attrname . 'format'} = FORMAT_HTML;
                $default_values->{$attrname} = $contents[$contentsid[$i - 1]]->content;

            }

        }

        for ($i = 1; $i <= 25; $i++) {
            $attrname = 'content' . $i;

            if (property_exists($default_values, $attrname)) {
                $default_values = file_prepare_standard_editor($default_values, $attrname, $editoroptions,
                    $context, 'mod_labelwithgroup', 'section', $default_values->id ?: null);
            }

        }

        parent::set_data($default_values); // TODO: Change the autogenerated stub
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return stdClass|null
     */
    public function get_data()
    {
        $data = parent::get_data(); // TODO: Change the autogenerated stub

        if (is_null($data)) {
            return $data;
        }

        $context = context_course::instance($data->course);

        // O editor está sendo salvo como introformat ao invés de contentformat
        $editoroptions = array_merge(self::EDITOROPTIONS, [ 'context' => $context]);

        if (!property_exists($data, 'id')) {
            $data->id = null;
        }

        for ($i = 1; $i <= 25; $i++) {
            $attrname = 'content' . $i;

            $data = file_postupdate_standard_editor($data, $attrname, $editoroptions,
                $context, 'mod_labelwithgroup', 'section', $data->id);

        }

        return $data;
    }
}
