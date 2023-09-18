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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/blocks/contactperson/locallib.php');

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
        global $OUTPUT;

        $htmloutput = "";

        if ($usedcontactperson !== "empty" && $usedcontactperson !== null ) {
            $config = get_config('block_contactperson');
            $externcontact = $this->get_index_from_config($config, $usedcontactperson);

            $userdata = $this->get_prepared_userdata();

            if ($externcontact) {
                // Extrahiere die Zahl am Ende des Schlüssels.
                $key = substr($externcontact, -1 * (strlen($externcontact) - strlen('name')));

                // Depending on key extract contact information.
                $userdata->contactpersonlink = $config->{'contactpersonlink' . $key};
                $userdata->name = $usedcontactperson;
                $userdata->fieldofaction = $config->{'fieldofaction' . $key};
                $userdata->emailfieldofaction = $config->{'emailfieldofaction' . $key};
                $userdata->linkfieldofaction = $config->{'linkfieldofaction' . $key};
                $userdata->additionalfieldofaction = $config->{'additionalfieldofaction' . $key};
                $userdata->linkadditionalfieldofaction = $config->{'linkadditionalfieldofaction' . $key};
                $userdata->userid = $config->{'userid' . $key};

                // Try to get user picture.
                $user = core_user::get_user($userdata->userid);
                if ($user) {
                    $userdata->userpicturehtml = $OUTPUT->user_picture($user, ['courseid' => '1']);
                }
                $htmloutput .= $userdata->userid;
            } else {
                $user = core_user::get_user($usedcontactperson);
                if ($user) {

                    $userdata->name = $user->firstname . " " . $user->lastname;
                    $userdata->email = $user->email;
                    $userdata->userid = $user->id;
                    $userdata->userpicturehtml = $OUTPUT->user_picture($user, ['courseid' => '1']);
                    $htmloutput .= $userdata->userid;
                }
            }

            $htmloutput = $this->get_html_for_user($userdata);
        }
        return $htmloutput;
    }

    /**
     * Returns the prepared user data.
     *
     * @return stdClass $userdata The user data.
     *
     */
    private function get_prepared_userdata() {
        $url = block_contactperson_get_url_of_placeholderimage();

        $userdata = new stdClass();
        $userdata->name = null;
        $userdata->email = null;
        $userdata->fieldofaction = null;
        $userdata->emailfieldofaction = null;
        $userdata->linkfieldofaction = null;
        $userdata->additionalfieldofaction = null;
        $userdata->emailadditionalfieldofaction = null;
        $userdata->linkadditionalfieldofaction = null;
        $userdata->userpicturehtml = "<div class='d-inline-block aabtn'>
                    <img class='userpicture' width='35' height='35' src='{$url}' alt=''></div>";
        $userdata->contactpersonlink = null;
        $userdata->userid = null;

        return $userdata;
    }

    private function add_url_prefix($url) {
        if (preg_match("/^https?:\/\//", $url)) {
            return $url;
        } else {
            return 'https://' . $url;
        }
    }
    

    /**
     * Returns the HTML for a user.
     *
     * @param stdClass $userdata The user data.
     *
     * @return string The HTML for a user.
     */
    private function get_html_for_user($userdata) {

        $prefixurlcheck = $this->add_url_prefix($userdata->contactpersonlink);
        $result = "<div class='container d-flex align-items-center contactperson'>" .
            "   <div class='row w-100 pb-3'>" .
            '       <div class="align-self-center">' .
            $userdata->userpicturehtml .
            '       </div>
                        <div>' .
            "           <a href='{$prefixurlcheck}' target='_blank'>{$userdata->name}</a>";

        if ($userdata->email) {
            $result .= "(<a class='fa fa-envelope-o' href='mailto: {$userdata->email}'></a>)";
        }

        $linkfieldofaction = $this->add_url_prefix($userdata->linkfieldofaction);
        $result .= $this->get_action_field_html($userdata->fieldofaction,
                                                $userdata->emailfieldofaction,
                                                $linkfieldofaction);
        $additionalfieldofaction = $this->add_url_prefix($userdata->linkadditionalfieldofaction);
        $result .= $this->get_action_field_html($userdata->additionalfieldofaction,
                                             $userdata->emailadditionalfieldofaction,
                                             $additionalfieldofaction);

        $result .=
            '       </div>
                    </div>
                </div>';

        return $result;
    }

    private function get_action_field_html($field, $email, $link) {
        if ($field) {
            return <<<HTML
            <div>
                <a href='$link'>$field</a>
                (<a class='fa fa-envelope-o' href='mailto:$email'></a>)
            </div>
            HTML;
        }
        return "";
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
