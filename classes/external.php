<?php

/**
 * Label with group external API
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

class mod_labelwithgroup_external extends external_api {

    /**
     * Describes the parameters for get_labelswithgroup_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_labelswithgroup_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of labels in a provided list of courses.
     * If no list is provided all labels that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and labels
     * @since Moodle 3.3
     */
    public static function get_labelswithgroup_by_courses($courseids = array()) {

        $warnings = array();
        $returnedlabels = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_labelswithgroup_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the labels in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $labelswithgroup = get_all_instances_in_courses("labelwithgroup", $courses);
            foreach ($labelswithgroup as $labelwithgroup) {
                $context = context_module::instance($labelwithgroup->coursemodule);
                // Entry to return.
                $labelwithgroup->name = external_format_string($labelwithgroup->name, $context->id);
                $options = array('noclean' => true);
                list($labelwithgroup->intro, $labelwithgroup->introformat) =
                    external_format_text($labelwithgroup->intro, $labelwithgroup->introformat, $context->id, 'mod_labelwithgroup', 'intro', null, $options);
                $labelwithgroup->introfiles = external_util::get_area_files($context->id, 'mod_labelwithgroup', 'intro', false, false);

                $returnedlabels[] = $labelwithgroup;
            }
        }

        $result = array(
            'labelswithgroup' => $returnedlabels,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_labels_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_labelswithgroup_by_courses_returns() {
        return new external_single_structure(
            array(
                'labelswithgroup' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Module id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'labelwithgroup name'),
                            'intro' => new external_value(PARAM_RAW, 'labelwithgroup contents'),
                            'introformat' => new external_format_value('intro', 'Content format'),
                            'introfiles' => new external_files('Files in the introduction text'),
                            'timemodified' => new external_value(PARAM_INT, 'Last time the labelwithgroup was modified'),
                            'section' => new external_value(PARAM_INT, 'Course section id'),
                            'visible' => new external_value(PARAM_INT, 'Module visibility'),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode'),
                            'groupingid' => new external_value(PARAM_INT, 'Grouping id'),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_labelswithgroup_by_user.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_labelswithgroup_by_user_parameters() {
        return new external_function_parameters (
            array(
                'groupid' => new external_value(PARAM_INT, 'Allowed group id'),
                'courseid' => new external_value(PARAM_INT, 'Course id')
            )
        );
    }

    /**
     * Describes the get_labels_by_user return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_labelswithgroup_by_user_returns() {
        return new external_single_structure(
            array (
                'groupid' => new external_value(PARAM_INT, 'Group id'),
                'allowed' => new external_value(PARAM_BOOL, 'User are allowed to see this content')
            )
        );
    }

    /**
     * Returns if a user is allowed in a group
     *
     * @param int $groupid Group id
     * @param int $courseid Course id
     * @return array of warnings and labels
     * @since Moodle 3.3
     */
    public static function get_labelswithgroup_by_user($groupid, $courseid) {

        global $DB, $USER;

        $params = array(
            'groupid' => $groupid,
            'courseid' => $courseid,
        );

        $params = self::validate_parameters(self::get_labelswithgroup_by_user_parameters(), $params);

        $result = [
            'groupid' => $params['groupid'],
            'allowed' => true
        ];
        
        $context = context_course::instance($params['courseid']);

        if (has_capability('mod/labelwithgroup:seeallactivities', $context)) {
            return $result;
        }

        if ($params['groupid'] !== -1) {
            $allowed = $DB->record_exists('groups_members', [ 'groupid' => $params['groupid'], 'userid' => $USER->id ]);

            $result['allowed'] = $allowed;
        }

        return $result;
    }
}
