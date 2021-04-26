<?php
/**
 * None template
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_labelwithgroup\classes;

use mod_labelwithgroup\interfaces\labeltemplateinterface;

/**
 * None template
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class none implements labeltemplateinterface
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
    public function process_content($content, $title, $group, $courseid) {

        $identifier = time() . uniqid();

        $content = $this->build_content($content);

        $newcontent = <<<EOF
            <div id="mod_labelwithgroup_element_{$identifier}" data-group-id="{$group}" data-course-id="{$courseid}">$content</div>
EOF;

        return $this->add_script($newcontent, $identifier);
    }

    /**
     * Build content html
     *
     * @param string $content Html Content
     * @return string
     */
    public function build_content($content) {

        $newcontent = "";
        foreach ($content as $index => $item) {
            $newcontent = $newcontent . <<<EOT
                <div id="mod_labelwithgroup_content{$index}">{$item}</div>
EOT;
        }

        return $newcontent;
    }

    /**
     * Add some script after HTML content
     *
     * @param string $content Html Content
     * @param string $identifier Element identifier
     * @return string
     */
    public function add_script($content, $identifier)
    {

        return $content . <<<EOF
            <script type="module">
            require(['jquery', 'core/ajax'], function($, ajax) {
              if (parseInt(
                  document.querySelector(
                      '#mod_labelwithgroup_element_{$identifier}'
                      ).getAttribute('data-group-id')
                    ) !== -1) {
                ajax.call([
                        {
                            methodname: 'mod_labelwithgroup_get_labelswithgroup_by_user',
                            args: {
                                groupid: parseInt(
                                    document.querySelector(
                                        '#mod_labelwithgroup_element_{$identifier}'
                                        ).getAttribute('data-group-id')),
                                courseid: parseInt(
                                    document.querySelector(
                                        '#mod_labelwithgroup_element_{$identifier}'
                                        ).getAttribute('data-course-id'))
                            }
                        }
                    ])[0].then(function (res) {
                        if (!res.allowed) {
                            document.querySelector('#mod_labelwithgroup_element_{$identifier}')
                                .closest('.labelwithgroup').classList.add('d-none');
                        }
                    });
                }
            })
            </script>
EOF;

    }
}