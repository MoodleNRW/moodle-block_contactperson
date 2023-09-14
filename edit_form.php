<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Form for editing badges block instances.
 *
 * @package    block_contactperson
 * @copyright  2023 Alexander Mikasch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Alexander Mikasch <alexander.mikasch@ruhr-uni-bochum.de>
 */

 defined('MOODLE_INTERNAL') || die();

 require_once($CFG->dirroot.'/lib/formslib.php');

class block_contactperson_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        $maxamount = get_config('block_contactperson', 'contactsmaxamount');
        for ($i = 1; $i <= $maxamount; $i++) {
            $this->add_contact_person($mform, $i);
        }
    }

    private function prepare_used_contact_persons() {
        global $COURSE;
        $config = get_config('block_contactperson');
        $options = array();

        for ($i = 1; $i <= 15; $i++) {
            $nextname = 'name'.$i;
            if (!empty($config->{$nextname})) {
                $optionvalue = $config->{$nextname};
                $options[$optionvalue] = $optionvalue;
            }
        }

        $accessroles = $config->accessroles;
        $accessrolesarray = explode(',', $accessroles);
        $context = context_course::instance($COURSE->id);
        $userroles = array();

        foreach ($accessrolesarray as $roleid) {
            $userroles = array_merge($userroles, get_role_users($roleid, $context, false, 'u.id, u.firstname, u.lastname'));
        }

        foreach ($userroles as $user) {
            $optionvalue = $user->firstname ." ". $user->lastname;
            $options[$user->id] = $optionvalue;
        }

        // Prepare result array for config_usedcontactperson.
        arsort($options);
        $options["empty"] = get_string('nopersonassigned', 'block_contactperson');
        $options = array_reverse($options, true);

        return $options;
    }

    // Später Refactoren damit n-Eintröge möglich sind.
    private function add_contact_person($mform, $index) {

        $mform->addElement('header', 'configheader', get_string('name', 'block_contactperson')." {$index}");

        $optionsconfigusedcontactperson = $this->prepare_used_contact_persons();
        $mform->addElement('select', 'config_usedcontactperson'.$index,
                            get_string('dropdowncontactperson',
                            'block_contactperson')." {$index}", $optionsconfigusedcontactperson);
        $mform->setDefault('config_usedcontactperson'.$index, get_string('nopersonassigned', 'block_contactperson'));
    }
}
