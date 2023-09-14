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

        // Hardcoded between 1 and 5.
        $maxamount = get_config('block_contactperson', 'contactsmaxamount');

        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            $text = "";
            for ($i = 1; $i <= $maxamount; $i++) {
                $contactperson = $this->config->{'usedcontactperson' . $i};
                $text .= $this->get_html_for_contactperson($contactperson);
            }
            $this->content->text = $text;
        }

        return $this->content;
    }
    /**
     * Returns the HTML for a contact person.
     * 
     * @param string $usedcontactperson The used contact person.
     * @return string The HTML for a contact person.
     */
    private function get_html_for_contactperson($usedcontactperson) {
        global $DB, $OUTPUT;

        $htmloutput = "";

        if ($usedcontactperson !== "empty") {
            $config = get_config('block_contactperson');
            $externcontact = $this->get_index_from_config($config, $usedcontactperson);
            $contactpersonlink = null;
            $name = null;
            $email = null;
            $fieldofaction = null;
            $emailfieldofaction = null;
            $linkfieldofaction = null;
            $additionalfieldofaction = null;
            $emailadditionalfieldofaction = null;
            $linkadditionalfieldofaction = null;
            $userid = null;
            $userpicturehtml = "";

            if ($externcontact) {
                // Extrahiere die Zahl am Ende des Schlüssels.
                $key = substr($externcontact, -1 * (strlen($externcontact) - strlen('name')));

                // Depending on key extract contact information.
                $contactpersonlink = $config->{'contactpersonlink' . $key};
                $name = $usedcontactperson;
                $fieldofaction = $config->{'fieldofaction' . $key};
                $userid = $config->{'userid' . $key};
                $userpicturehtml = "";

                // Try to get user picture.
                $user = core_user::get_user($userid);
                if ($user) {
                    $userpicture = $OUTPUT->user_picture($user, ['courseid' => '1']);
                    $userpicturehtml = $userpicture;
                }
                $htmloutput .= $userid;
            } else {
                $user = core_user::get_user($usedcontactperson);
                if ($user) {

                    $name = $user->firstname . " " . $user->lastname;
                    $email = $user->email;
                    $userid = $user->id;
                    $userpicture = $OUTPUT->user_picture($user, ['courseid' => '1']);
                    $userpicturehtml = $userpicture;
                    $htmloutput .= $userid;
                }
            }

            $htmloutput = $this->get_html_for_user($name, $email, $fieldofaction, $emailfieldofaction,
                                                    $linkfieldofaction, $additionalfieldofaction, $emailadditionalfieldofaction,
                                                    $linkadditionalfieldofaction, $userpicturehtml, $contactpersonlink);        }
        return $htmloutput;
    }

    /**
     * Returns the HTML for a user.
     * 
     * @param string $name The name of the user.
     * @param string $email The email of the user.
     * @param string $fieldofaction The field of action of the user.
     * @param string $emailfieldofaction The email field of action of the user.
     * @param string $linkfieldofaction The link field of action of the user.
     * @param string $additionalfieldofaction The additional field of action of the user.
     * @param string $emailadditionalfieldofaction The email additional field of action of the user.
     * @param string $linkadditionalfieldofaction The link additional field of action of the user.
     * @param string $userpicturehtml The user picture HTML.
     * @param string $contactpersonlink The contact person link.
     * 
     * @return string The HTML for a user.  
     */
    private function get_html_for_user($name, $email, $fieldofaction, $emailfieldofaction,
                                        $linkfieldofaction, $additionalfieldofaction, $emailadditionalfieldofaction,
                                        $linkadditionalfieldofaction, $userpicturehtml, $contactpersonlink) {

        $result = "<div class='container d-flex align-items-center contactperson'>" .
            "   <div class='row w-100 pb-3'>" .
            '       <div class="align-self-center">' .
            $userpicturehtml .
            '       </div>
                        <div class="d-flex flex-column justify-content-between">' .
            "           <a href='{$contactpersonlink}' target='_blank'>{$name}</a>";

        if($email){
            $result .= "<div>(<a class='fa fa-envelope-o' href='mailto: {$email}'></a>)</div>";
        }

        if($fieldofaction){
            $result .= "
            <div>
                <a href='{$linkfieldofaction}'>{$fieldofaction}</a>
                (<a class='fa fa-envelope-o' href='mailto: {$emailfieldofaction}'></a>)
            </div>";
        }

        if($additionalfieldofaction){
            $result .= "
            <div>
                <a href='{$linkadditionalfieldofaction}'>{$additionalfieldofaction}</a>
                (<a class='fa fa-envelope-o' href='mailto: {$emailadditionalfieldofaction}'></a>)
            </div>";
        }

        $result .=
            '       </div>
                    </div>
                </div>';

        return $result;
    }

    /**
     * Returns the index from the config.
     * 
     * @param stdClass $config The config.
     * @param string $usedcontactperson The used contact person.
     * 
     * @return string The index from the config.
     */
    private function get_index_from_config($config, $usedcontactperson) {
        $properties = get_object_vars($config);
        $key = array_search($usedcontactperson, $properties);
        return $key;
    }

    public function has_config() {
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
