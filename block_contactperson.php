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
 * Block contactperson is defined here.
 *
 * @package     block_contactperson
 * @copyright   2023 Moodle.NRW <alexander.mikasch@ruhr-uni-bochum.de
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_contactperson extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_contactperson');
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            $usedcontactperson1 = $this->config->{'usedcontactperson1'};           
            $usedcontactperson2 = $this->config->{'usedcontactperson2'};
            $usedcontactperson3 = $this->config->{'usedcontactperson3'};
            $text = 
            $this->get_html_for_contactperson($usedcontactperson1, true, $usedcontactperson2 == 'empty') .
            $this->get_html_for_contactperson($usedcontactperson2, false, $usedcontactperson3 == 'empty') .
            $this->get_html_for_contactperson($usedcontactperson3, false, true);
            $this->content->text = $text;
        }

        return $this->content;
    }

    private function get_html_for_contactperson($usedcontactperson,$first = false, $last = false) {
        global $DB, $OUTPUT;
        $htmloutput = "";
        if (strpos($usedcontactperson, 'empty') === false) {
            $config = get_config('block_contactperson');
            $propertykey = $this->get_index_from_config($config,$usedcontactperson);
            if($propertykey) {
                // Extrahiere die Zahl am Ende des Schlüssels
                $key = substr($propertykey, -1 * (strlen($propertykey) - strlen('name')));
                
                $contactpersonlink = $config->{'contactpersonlink'.$key};
                $email = $config->{'email'.$key};
                $fieldofaction = $config->{'fieldofaction'.$key};
                $userid = $config->{'userid'.$key};
                $userpicturehtml = "";

                $user = core_user::get_user($userid);
                if ($user) {
                    $userpicture = $OUTPUT->user_picture($user); 
                    $userpicturehtml =  $userpicture; 
                }

                $borderstyle = "";
                $paddingtop = $first ? "" : "pt-3";

                if (!$last){
                    $borderstyle= "border-bottom: #dee2e6 1px solid;";
                }
                 
                $htmloutput = 
                "<div class='container d-flex align-items-center' style='{$borderstyle}'>".
                "   <div class='row w-100 {$paddingtop} pb-3'>".
                '       <div class="align-self-center">'.
                            $userpicturehtml.
                '       </div>           
                        <div class="d-flex flex-column justify-content-between">'.
                "           <a href='{$contactpersonlink}' target='_blank'>{$usedcontactperson}</a>".
                "           <a href='mailto: {$email}'>{$fieldofaction}</a>".
                '       </div>
                    </div>
                </div>';
            }
            
        }
        return $htmloutput;
    }

    private function get_index_from_config($config,$usedcontactperson) {
        $properties = get_object_vars($config);
        $key = array_search($usedcontactperson, $properties);
        return $key;
    }

    function has_config() {
        return true;
    }

    public function instance_allow_config() {
        return true;
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
    public function applicable_formats() {
        return array(
            'admin' => false,
            'site-index' => true,
            'course-view' => true,
            'mod' => false,
            'my' => true,
        );
    }
}
