<?php

namespace mod_labelwithgroup\classes;

use mod_labelwithgroup\interfaces\labeltemplateinterface;

/**
 * Slide template
 *
 * @package    mod_labelwithgroup
 * @copyright  2021 4Linux  {@link https://4linux.com.br/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class slide implements labeltemplateinterface
{

    public function process_content($content, $title, $group, $courseid)
    {

        $identifier = time() . uniqid();

        $options = $this->build_options($content, $identifier);

        $content = $this->build_content($content);

        $newcontent = <<<EOT

            <div class="w-100" id="mod_labelwithgroup_element_{$identifier}" data-group-id="{$group}" data-course-id="{$courseid}">
                <div id="mod_labelwithgroup_{$identifier}" class="carousel slide" data-ride="false" data-group-id="{$group}">
                  <ol class="carousel-indicators mod-labelwithgroup-carousel-indicators">
                    {$options}
                  </ol>
                  <div class="carousel-inner mod-labelwithgroup-carousel-inner">
                    {$content}
                  </div>
                  <a class="carousel-control-prev mod-labelwithgroup-carousel-control-prev" href="#mod_labelwithgroup_{$identifier}" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon mod-labelwithgroup-carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                  </a>
                  <a class="carousel-control-next mod-labelwithgroup-carousel-control-next" href="#mod_labelwithgroup_{$identifier}" role="button" data-slide="next">
                    <span class="carousel-control-next-icon mod-labelwithgroup-carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                  </a>
                </div>
            </div>

EOT;

        return $this->add_script($newcontent, $identifier);
    }

    public function build_content($content) {

        $newcontent = "";
        foreach ($content as $index => $item) {
            $active = $index == 0 ? 'active' : '';
            $newcontent = $newcontent . <<<EOT
                <div class="carousel-item {$active}">
                  <div id="mod_labelwithgroup_content{$index}">{$item}</div>
                </div>
                
EOT;
        }

        return $newcontent;
    }

    /**
     * Build slides content
     *
     * @param string $content Content to be added
     * @param string $identifier Unique identifier
     * @return string
     */
    public function build_options($content, $identifier) {

        $options = array_map(function ($key, $item) use($identifier) {
            if ($key == 0) {
                return <<<EOF
                    <li data-target="#mod_labelwithgroup_{$identifier}" data-slide-to="{$key}" class="active"></li>
EOF;
            }

            return <<<EOF
                <li data-target="#mod_labelwithgroup_{$identifier}" data-slide-to="{$key}"></li>
EOF;

        }, array_keys($content), $content);

        return join("", $options);
    }

    public function add_script($content, $identifier)
    {

        return $content . <<<EOF
            <script type="module">
            
            require(['jquery', 'core/ajax'], function($, ajax) {
              if (parseInt(document.querySelector('#mod_labelwithgroup_element_{$identifier}').getAttribute('data-group-id')) !== -1) {
                ajax.call([
                        { 
                            methodname: 'mod_labelwithgroup_get_labelswithgroup_by_user', 
                            args: { 
                                groupid: parseInt(document.querySelector('#mod_labelwithgroup_element_{$identifier}').getAttribute('data-group-id')),
                                courseid: parseInt(document.querySelector('#mod_labelwithgroup_element_{$identifier}').getAttribute('data-course-id')) 
 
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