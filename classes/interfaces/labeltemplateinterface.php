<?php
namespace mod_labelwithgroup\interfaces;

/**
 * Template commom interface
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

interface labeltemplateinterface
{

    /**
     * Process content to add a template
     *
     * @param string[] $content Content to be displayed on template
     * @param string $title Title to indentify the content
     * @param string $group Allowed group id
     * @param string $courseid Course id
     * @return string
     */
    public function process_content($content, $title, $group, $courseid);

    /**
     * Build content html
     *
     * @param string $content Html Content
     * @return string
     */
    public function build_content($content);

    /**
     * @param string $content Html Content
     * @param string $identifier Element identifier
     * @return string
     */
    public function add_script($content, $identifier);
}