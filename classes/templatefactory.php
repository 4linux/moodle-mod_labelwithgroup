<?php
namespace mod_labelwithgroup\classes;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/labelwithgroup/classes/collapse.php');
require_once($CFG->dirroot . '/mod/labelwithgroup/classes/slide.php');
require_once($CFG->dirroot . '/mod/labelwithgroup/classes/collapseslide.php');
require_once($CFG->dirroot . '/mod/labelwithgroup/classes/none.php');

use mod_labelwithgroup\interfaces\labeltemplateinterface;
use mod_labelwithgroup\classes\collapse;
use mod_labelwithgroup\classes\slide;
use mod_labelwithgroup\classes\none;
use mod_labelwithgroup\classes\collapseslide;

/**
 * Template Factory
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class templatefactory
{

    public static $templatetypenone = 'none';
    public static $templatetypecollapse = 'collapse';
    public static $templatetypeslide = 'slide';
    public static $templatetypecollapseslide = 'collapse-slide';

    /**
     * Retrieve a template object
     *
     * @param string $type Template type
     *
     * @return labeltemplateinterface
     *
     */
    static function get_template_by_type($type) {
        switch ($type) {
            case self::$templatetypenone:
                return new none();
            case self::$templatetypecollapse:
                return new collapse();
            case self::$templatetypecollapseslide:
                return new collapseslide();
            case self::$templatetypeslide:
                return new slide();
        }
    }
}