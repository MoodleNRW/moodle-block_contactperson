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
        $max_amount = get_config('block_contactperson','contactsmaxamount');

        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            for ($i=1; $i<=$max_amount; $i++) {
                $contactperson = $this->config->{'usedcontactperson'.$i};
                $text .= $this->get_html_for_contactperson($contactperson);
            }
            $this->content->text = $text;
        }

        return $this->content;
    }

    private function get_html_for_contactperson($usedcontactperson) {
        global $DB, $OUTPUT;

        $htmloutput = "";

        if ($usedcontactperson !== "empty") {
            $config = get_config('block_contactperson');
            $propertykey = $this->get_index_from_config($config,$usedcontactperson);

            //todo email 
            //todo userid 
            // contactpersonlink ist nicht immer gesetzt wenn User aus Kurs kommt
            // fieldofaction ist nicht immer gesetzt wenn User aus Kurs kommt
            //userpicturehtml -> Wird automatisch gefunden 
            $contactpersonlink = null;
            $name = null;
            $email = null;
            $fieldofaction =  null;
            $userid = null;
            $userpicturehtml = "";

            if($propertykey){
                // Extrahiere die Zahl am Ende des Schlüssels
                $key = substr($propertykey, -1 * (strlen($propertykey) - strlen('name')));

                // Depending on key extract contact information.
                $contactpersonlink = $config->{'contactpersonlink'.$key};
                $name = $usedcontactperson;
                $email = $config->{'email'.$key};
                $fieldofaction = $config->{'fieldofaction'.$key};
                $userid = $config->{'userid'.$key};
                $userpicturehtml = "";

                // Try to get user picture.
                $user = core_user::get_user($userid);
                if ($user) {
                    $userpicture = $OUTPUT->user_picture($user,['courseid' => '1']);
                    $userpicturehtml =  $userpicture;
                }
            }elseif ($propertykey == false) {
               //Get Data from User
               $user = core_user::get_user($usedcontactperson);
               if ($user) {
                $name =$user->firstname . " " . $user->lastname;
                $email = $user->email;
                $userid = $user->id;
                $userpicture = $OUTPUT->user_picture($user,['courseid' => '1']);
                $userpicturehtml =  $userpicture;
                }
            } 

            if($userid) {       
                // Try to get Team URLs for fields of action.
                $fields = explode(" | ", $fieldofaction);
                $mapping = array("Entwicklung & Technik" => 17, "Support & Anwendungswissen" => 18, "E-Assessment" => 19);
                $fields_to_url = array();

                foreach ($fields as $field) {
                    if (isset($mapping[$field])) {
                        $fields_to_url[$field] = $mapping[$field];
                    }
                }

                // Actual HTML Output.
                // For the picture and name.
                $htmloutput =
                "<div class='container d-flex align-items-center contactperson'>".
                "   <div class='row w-100 pb-3'>".
                '       <div class="align-self-center">'.
                            $userpicturehtml.
                '       </div>
                        <div class="d-flex flex-column justify-content-between">'.
                "           <a href='{$contactpersonlink}' target='_blank'>{$name}</a>";

                // For the fields of action.
                $first = TRUE;
                foreach ($fields_to_url as $key => $value) {
                    $field = $key;
                    $field_id = $value;

                    $htmloutput .= "<div>";
                    // If multiple fields seperate them by " | "
                    if (!$first) { $htmloutput .= " | ";}

                    $htmloutput .= "<a href='https://moodlenrw.de/course/index.php?categoryid={$field_id}'>{$field}</a>
                        (<a class='fa fa-envelope-o' href='mailto: {$email}'></a>)
                    </div>";

                    if ($first) $first = FALSE;
                }

                $htmloutput .=
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
