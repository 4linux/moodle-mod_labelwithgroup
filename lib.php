<?php
/**
 * Library of functions and constants for module labelwithgroup
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/** LABELWITHGROUP_MAX_NAME_LENGTH = 50 */
define("LABELWITHGROUP_MAX_NAME_LENGTH", 50);

/**
 * @uses LABELWITHGROUP_MAX_NAME_LENGTH
 * @param object $labelwithgroup
 * @return string
 */
function get_labelwithgroup_name($labelwithgroup) {
    $name = strip_tags(format_string($labelwithgroup->intro, true));
    if (core_text::strlen($name) > LABELWITHGROUP_MAX_NAME_LENGTH) {
        $name = core_text::substr($name, 0, LABELWITHGROUP_MAX_NAME_LENGTH)."...";
    }

    if (empty($name)) {
        $name = get_string('modulename', 'labelwithgroup');
    }

    return $name;
}
/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $labelwithgroup
 * @return bool|int
 */
function labelwithgroup_add_instance($labelwithgroup) {
    global $DB;

    $labelwithgroup->name = get_labelwithgroup_name($labelwithgroup);
    $labelwithgroup->timemodified = time();

    $id = $DB->insert_record("labelwithgroup", $labelwithgroup);

    foreach ($labelwithgroup->content as $content) {

        $stdclass = new stdClass();
        $stdclass->content = $content;
        $stdclass->labelwithgroup_id = $id;

        $DB->insert_record("labelwithgroup_content", $stdclass);
    }

    $completiontimeexpected = !empty($labelwithgroup->completionexpected) ?
        $labelwithgroup->completionexpected :
        null;
    \core_completion\api::update_completion_date_event(
        $labelwithgroup->coursemodule,
        'labelwithgroup',
        $id,
        $completiontimeexpected
    );

    return $id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $labelwithgroup
 * @return bool
 */
function labelwithgroup_update_instance($labelwithgroup) {
    global $DB;

    $labelwithgroup->name = get_labelwithgroup_name($labelwithgroup);
    $labelwithgroup->timemodified = time();
    $labelwithgroup->id = $labelwithgroup->instance;

    $completiontimeexpected = !empty($labelwithgroup->completionexpected) ?
        $labelwithgroup->completionexpected :
        null;
    \core_completion\api::update_completion_date_event(
        $labelwithgroup->coursemodule,
        'labelwithgroup',
        $labelwithgroup->id,
        $completiontimeexpected
    );

    $id = $DB->update_record("labelwithgroup", $labelwithgroup);

    labelwithgroup_update_labelcontent($labelwithgroup->content, $labelwithgroup->id);

    return $id;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function labelwithgroup_delete_instance($id) {
    global $DB;

    if (! $labelwithgroup = $DB->get_record("labelwithgroup", array("id" => $id))) {
        return false;
    }

    $result = true;

    $cm = get_coursemodule_from_instance('labelwithgroup', $id);
    \core_completion\api::update_completion_date_event(
        $cm->id,
        'labelwithgroup',
        $labelwithgroup->id,
        null
    );

    if (! $DB->delete_records("labelwithgroup", array("id" => $labelwithgroup->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return cached_cm_info|null
 */
function labelwithgroup_get_coursemodule_info($coursemodule) {
    global $DB;

    if ($labelwithgroup = $DB->get_record(
        'labelwithgroup',
        array('id' => $coursemodule->instance),
        'id, name, intro, introformat'
    )) {
        if (empty($labelwithgroup->name)) {
            $labelwithgroup->name = "labelwithgroup{$labelwithgroup->id}";
            $DB->set_field('labelwithgroup', 'name', $labelwithgroup->name, array('id' => $labelwithgroup->id));
        }
        $info = new cached_cm_info();
        $info->content = format_module_intro('labelwithgroup', $labelwithgroup, $coursemodule->id, false);
        $info->name  = $labelwithgroup->name;
        return $info;
    } else {
        return null;
    }
}

/**
 *
 * Update content from a labelwithgroup resource
 *
 * @param array $newcontents New contents to update
 * @param int $labelwithgroupid Label with group id
 * @throws dml_exception
 */
function labelwithgroup_update_labelcontent($newcontents, $labelwithgroupid) {

    global $DB;

    $oldcontents = $DB->get_records('labelwithgroup_content', ['labelwithgroup_id' => $labelwithgroupid]);

    $toupdate = array_map(function ($oldcontent, $newcontent) use ($labelwithgroupid) {

        $stdclass = new stdClass();

        $stdclass->id = $oldcontent ? $oldcontent->id : null;

        $stdclass->content = $newcontent ? $newcontent : null;

        $stdclass->labelwithgroup_id = $labelwithgroupid;

        return $stdclass;

    }, $oldcontents, $newcontents);

    foreach ($toupdate as $stdclass) {

        if (empty($stdclass->id)) {
            $DB->insert_record("labelwithgroup_content", $stdclass);
            continue;
        }

        if (!empty($stdclass->content)) {
            $DB->update_record("labelwithgroup_content", $stdclass);
            continue;
        }

        $DB->delete_records("labelwithgroup_content", array("id" => $stdclass->id));

    }

}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function labelwithgroup_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return array();
}

/**
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 */
function labelwithgroup_supports($feature) {
    switch($feature) {
        case FEATURE_IDNUMBER:
            return true;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_NO_VIEW_LINK:
            return true;
        default:
            return null;
    }
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function labelwithgroup_dndupload_register() {
    $strdnd = get_string('dnduploadlabelwithgroup', 'mod_labelwithgroup');
    if (get_config('labelwithgroup', 'dndmedia')) {
        $mediaextensions = file_get_typegroup('extension', ['web_image', 'web_video', 'web_audio']);
        $files = array();
        foreach ($mediaextensions as $extn) {
            $extn = trim($extn, '.');
            $files[] = array('extension' => $extn, 'message' => $strdnd);
        }
        $ret = array('files' => $files);
    } else {
        $ret = array();
    }

    $strdndtext = get_string('dnduploadlabelwithgrouptext', 'mod_labelwithgroup');
    return array_merge($ret, array('types' => array(
        array('identifier' => 'text/html', 'message' => $strdndtext, 'noname' => true),
        array('identifier' => 'text', 'message' => $strdndtext, 'noname' => true)
    )));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function labelwithgroup_dndupload_handle($uploadinfo) {
    global $USER;

    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '';
    $data->introformat = FORMAT_HTML;
    $data->coursemodule = $uploadinfo->coursemodule;

    // Extract the first (and only) file from the file area and add it to the labelwithgroup as an img tag.
    if (!empty($uploadinfo->draftitemid)) {
        $fs = get_file_storage();
        $draftcontext = context_user::instance($USER->id);
        $context = context_module::instance($uploadinfo->coursemodule);
        $files = $fs->get_area_files($draftcontext->id, 'user', 'draft', $uploadinfo->draftitemid, '', false);
        if ($file = reset($files)) {
            if (file_mimetype_in_typegroup($file->get_mimetype(), 'web_image')) {
                // It is an image - resize it, if too big, then insert the img tag.
                $config = get_config('labelwithgroup');
                $data->intro = labelwithgroup_generate_resized_image($file, $config->dndresizewidth, $config->dndresizeheight);
            } else {
                // We aren't supposed to be supporting non-image types here, but fallback to adding a link, just in case.
                $url = moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename());
                $data->intro = html_writer::link($url, $file->get_filename());
            }
            $data->intro = file_save_draft_area_files($uploadinfo->draftitemid, $context->id, 'mod_labelwithgroup', 'intro', 0,
                                                      null, $data->intro);
        }
    } else if (!empty($uploadinfo->content)) {
        $data->intro = $uploadinfo->content;
        if ($uploadinfo->type != 'text/html') {
            $data->introformat = FORMAT_PLAIN;
        }
    }

    return labelwithgroup_add_instance($data, null);
}

/**
 * Resize the image, if required, then generate an img tag and, if required, a link to the full-size image
 * @param stored_file $file the image file to process
 * @param int $maxwidth the maximum width allowed for the image
 * @param int $maxheight the maximum height allowed for the image
 * @return string HTML fragment to add to the labelwithgroup
 */
function labelwithgroup_generate_resized_image(stored_file $file, $maxwidth, $maxheight) {
    global $CFG;

    $fullurl = moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename());
    $link = null;
    $attrib = array('alt' => $file->get_filename(), 'src' => $fullurl);

    if ($imginfo = $file->get_imageinfo()) {
        $width = $imginfo['width'];
        $height = $imginfo['height'];
        if (!empty($maxwidth) && $width > $maxwidth) {
            $height *= (float)$maxwidth / $width;
            $width = $maxwidth;
        }
        if (!empty($maxheight) && $height > $maxheight) {
            $width *= (float)$maxheight / $height;
            $height = $maxheight;
        }

        $attrib['width'] = $width;
        $attrib['height'] = $height;

        if ($width != $imginfo['width']) {
            $mimetype = $file->get_mimetype();
            if ($mimetype === 'image/gif' or $mimetype === 'image/jpeg' or $mimetype === 'image/png') {
                require_once($CFG->libdir.'/gdlib.php');
                $data = $file->generate_image_thumbnail($width, $height);

                if (!empty($data)) {
                    $fs = get_file_storage();
                    $record = array(
                        'contextid' => $file->get_contextid(),
                        'component' => $file->get_component(),
                        'filearea'  => $file->get_filearea(),
                        'itemid'    => $file->get_itemid(),
                        'filepath'  => '/',
                        'filename'  => 's_'.$file->get_filename(),
                    );
                    $smallfile = $fs->create_file_from_string($record, $data);

                    $attrib['src'] = moodle_url::make_draftfile_url(
                        $smallfile->get_itemid(),
                        $smallfile->get_filepath(),
                        $smallfile->get_filename()
                    );
                    $link = $fullurl;
                }
            }
        }

    } else {
        $attrib['width'] = $maxwidth;
    }

    $img = html_writer::empty_tag('img', $attrib);
    if ($link) {
        return html_writer::link($link, $img);
    } else {
        return $img;
    }
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function labelwithgroup_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array(), $filter);
    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_labelwithgroup_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory,
                                                      int $userid = 0) {
    $cm = get_fast_modinfo($event->courseid, $userid)->instances['labelwithgroup'][$event->instance];

    if (!$cm->uservisible) {
        // The module is not visible to the user for any reason.
        return null;
    }

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/labelwithgroup/view.php', ['id' => $cm->id]),
        1,
        true
    );
}
