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
 * Template Factory
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_labelwithgroup;

defined('MOODLE_INTERNAL') || die;

use mod_labelwithgroup\interfaces\labeltemplateinterface;

/**
 * Template Factory
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class templatefactory
{

    /** @var string $templatetypenone */
    public static $templatetypenone = 'none';
    /** @var string $templatetypecollapse */
    public static $templatetypecollapse = 'collapse';
    /** @var string $templatetypeslide */
    public static $templatetypeslide = 'slide';
    /** @var string $templatetypecollapseslide */
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