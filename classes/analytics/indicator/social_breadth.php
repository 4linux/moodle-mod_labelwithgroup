<?php
/**
 * Social breadth indicator - label with group.
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_labelwithgroup\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Social breadth indicator - labelwithgroup.
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class social_breadth extends activity_base {

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('indicator:socialbreadth', 'mod_labelwithgroup');
    }

    public function get_indicator_type() {
        return self::INDICATOR_SOCIAL;
    }

    public function get_social_breadth_level(\cm_info $cm) {
        return self::SOCIAL_LEVEL_1;
    }
}
