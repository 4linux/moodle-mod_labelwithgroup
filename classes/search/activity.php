<?php
/**
 * Search area for mod_labelwithgroup activities.
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_labelwithgroup\search;

defined('MOODLE_INTERNAL') || die();

/**
 * Search area for mod_labelwithgroup activities.
 *
 * Although there is no name field the intro value is stored internally, so no need
 * to overwrite self::get_document.
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity extends \core_search\base_activity {

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return true;
    }

    /**
     * Overwritten as labels are displayed in-course.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        // Get correct URL to section that contains label, from course format.
        $cminfo = $this->get_cm($this->get_module_name(), strval($doc->get('itemid')), $doc->get('courseid'));
        $format = course_get_format($cminfo->get_course());
        $url = $format->get_view_url($cminfo->sectionnum);

        // Add the ID of the label to the section URL.
        $url->set_anchor('module-' . $cminfo->id);
        return $url;
    }

    /**
     * Overwritten as labels are displayed in-course. Link to the course.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        return new \moodle_url('/course/view.php', array('id' => $doc->get('courseid')));

    }

}
