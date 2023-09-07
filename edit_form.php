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
        $max_amount = get_config('block_contactperson','contactsmaxamount');
        for ($i = 1; $i <= $max_amount; $i++) {
            $this->addContactPerson($mform,$i);
        }
    }

    private function prepareUsedContactPersons(){
        global $COURSE;
        $config = get_config('block_contactperson');
        $options = array();

        for ($i = 1; $i <= 15; $i++) {
            $nextname = 'name'.$i;
            if (!empty($config->{$nextname})) {
                $optionValue = $config->{$nextname};
                $options[$optionValue] = $optionValue;
            }
        }

        $accessroles = $config->accessroles;
        $accessrolesarray = explode(',',$accessroles);
        $context = context_course::instance($COURSE->id);
        $userroles = array();

        foreach($accessrolesarray as $roleid) {
            $userroles = array_merge($userroles, get_role_users($roleid, $context, false, 'u.id, u.firstname, u.lastname'));
        }

        foreach ($userroles as $user) {
            $optionValue = $user->firstname ." ". $user->lastname;
            $options[$user->id] = $optionValue;
        }

        //Prepare result array for config_usedcontactperson.
        asort($options);
        $mergedarray = array_merge( array("empty" => get_string('nopersonassigned','block_contactperson')) ,$options);

        return $mergedarray;
    }

    //Später Refactoren damit n-Eintröge möglich sind
    private function addContactPerson($mform,$index){

        $mform->addElement('header', 'configheader', get_string('name', 'block_contactperson')." {$index}");

        $optionsconfigusedcontactperson = $this->prepareUsedContactPersons();
        $mform->addElement('select', 'config_usedcontactperson'.$index, get_string('dropdowncontactperson', 'block_contactperson')." {$index}", $optionsconfigusedcontactperson);
        $mform->setDefault('config_usedcontactperson'.$index, get_string('nopersonassigned','block_contactperson'));
    }


}
