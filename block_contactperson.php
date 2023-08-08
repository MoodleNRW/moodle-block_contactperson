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
            $text = '
            <img src="https://moodlenrw.de/draftfile.php/22/user/draft/696634506/zahnraeder.png" alt="" width="40" height="44" role="presentation" class="img-fluid atto_image_button_left">
            <span><strong>Kontakt:&nbsp;<br></strong>' . 
            $this->get_html_for_contactperson($usedcontactperson1) .
            $this->get_html_for_contactperson($usedcontactperson2) .
            $this->get_html_for_contactperson($usedcontactperson3);
            $this->content->text = $text;
        }

        return $this->content;
    }

    private function get_html_for_contactperson($usedcontactperson) {
        $htmloutput = "";
        if (!str_contains($usedcontactperson, 'empty')) {
            $config = get_config('block_contactperson');
            $courselink = "bla";
            $fieldofaction = "Moodle.NRW | RUB";
            $email = "eassessment@moodlenrw.de";
            
            $htmloutput = "<a href='{$courselink}' target='_blank'>{$usedcontactperson}</a><br></span>". 
            '<div style="padding-bottom: 5px;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'.
             "<span><a href='mailto: {$email}'>{$fieldofaction}</a></span>".
             '<br></div><div>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';
        }
        return $htmloutput;
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
