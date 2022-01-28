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
 * Collapse template
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_labelwithgroup;

use mod_labelwithgroup\interfaces\labeltemplateinterface;

/**
 * Collapse template
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collapse implements labeltemplateinterface
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

        $newcontent = <<<EOT
            <div
                class="card mod-labelwithgroup-card"
                id="mod_labelwithgroup_element_{$identifier}"
                data-group-id="{$group}"
                data-course-id="{$courseid}"
            >
              <div class="card-header mod-labelwithgroup-card-header">
                  <a
                    class="btn btn-link mod-labelwithgroup-btn"
                    data-toggle="collapse"
                    href="#mod_labelwithgroup_{$identifier}"
                    role="button"
                    aria-expanded="false"
                    aria-controls="mod_labelwithgroup_{$identifier}"
                    >
                    <span class="mod-labelwithgroup-arrow-icon-button">
                        <img
                            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 0 24 24' width='24px' fill='%23000000'%3E%3Cpath d='M24 24H0V0h24v24z' fill='none' opacity='.87'/%3E%3Cpath d='M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41z'/%3E%3C/svg%3E"
                        />
                    </span>
                    <span class="mod-labelwithgroup-title">{$title}</span>
                  </a>
              </div>
                <div class="collapse" id="mod_labelwithgroup_{$identifier}">
                  <div class="card card-body mod-labelwithgroup-card-body">
                    {$content}
                  </div>
                </div>
            </div>
EOT;
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
    public function add_script($content, $identifier) {

        return $content . <<<EOF
            <script type="module" >
                
                if (parseInt(
                    document.querySelector(
                        "#mod_labelwithgroup_element_${identifier}"
                    ).getAttribute('data-group-id')
                ) !== -1) {
                
                    fetch("/lib/ajax/service.php?sesskey={$_SESSION['USER']->sesskey}&info=mod_labelwithgroup_get_labelswithgroup_by_user", {
                        method: "POST",
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            methodname: 'mod_labelwithgroup_get_labelswithgroup_by_user',
                            args: {
                                groupid: parseInt(
                                    document.querySelector(
                                        "#mod_labelwithgroup_element_${identifier}"
                                    ).getAttribute('data-group-id')),
                                courseid: parseInt(
                                    document.querySelector(
                                        "#mod_labelwithgroup_element_${identifier}"
                                    ).getAttribute('data-course-id'))
                            },
                            index: 0
                        })
                    }).then(res => {
                        res.json().then(response => {
                            if (!response.allowed) {
                                document.querySelector("#mod_labelwithgroup_element_${identifier}")
                                    .closest('.labelwithgroup').classList.add('d-none');
                            }
                        })
                
                    });
                }

            </script>
EOF;

    }
}