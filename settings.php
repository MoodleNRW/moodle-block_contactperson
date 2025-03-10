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
 * Plugin administration pages are defined here.
 *
 * @package     block_contactperson
 * @copyright   2023 Moodle.NRW <alexander.mikasch@ruhr-uni-bochum.de
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/adminlib.php');

defined('MOODLE_INTERNAL') || die();

// Konstanten nur einmal definieren - prüfen ob sie bereits existieren
if (!defined('BLOCK_CONTACTPERSON_SETTING_SELECT_YES')) {
    define('BLOCK_CONTACTPERSON_SETTING_SELECT_YES', 'yes');
}
if (!defined('BLOCK_CONTACTPERSON_SETTING_SELECT_NO')) {
    define('BLOCK_CONTACTPERSON_SETTING_SELECT_NO', 'no');
}

// Add a setting for a contact person.
$addcontactpersonconfigtexts = function($settings, $name, $i, $default = "", $conditionvalue = 'yes'){
    $settings->add(new admin_setting_configtext(
        "block_contactperson/$name" . $i,
        get_string($name, 'block_contactperson', array('no' => $i), null, true),
        "",
        $default
    ));
    $settings->hide_if(
        "block_contactperson/$name" . $i,
        "block_contactperson/personenabled" . $i,
        'neq',
        $conditionvalue
    );
};

// Add a setting for a contact person.
// @param $settings The settings object.
// @param $optionspersonenabled The options for the person enabled setting.
// $add_contactperson_configtexts The function for adding a config text.
$addcontactpersonsettings = function($settings, $optionspersonenabled) use ($addcontactpersonconfigtexts) {
    $settingkeys = [
        'name',
        'contactpersonlink',
        'userid',
        'fieldofaction',
        'linkfieldofaction',
        'emailfieldofaction',
        'additionalfieldofaction',
        'linkadditionalfieldofaction',
        'emailadditionalfieldofaction',
    ];

    // Add settings for each contact person.
    for ($i = 1; $i <= 15; $i++) {
        $settings->add(new admin_setting_heading(
            'block_contactperson/contactpersonheader' . $i,
            get_string('name', 'block_contactperson', array('no' => $i), null, true) . " {$i}",
            null
        ));

        $settings->add(new admin_setting_configselect(
            'block_contactperson/personenabled' . $i,
            get_string('personenabled', 'block_contactperson', array('no' => $i), null, true),
            "",
            BLOCK_CONTACTPERSON_SETTING_SELECT_NO,
            $optionspersonenabled
        ));

        foreach ($settingkeys as $key) {
            $addcontactpersonconfigtexts($settings, $key, $i);
        }
    }
};

if ($hassiteconfig) {
    if ($ADMIN->fulltree) {
        // Option for enabling Contactperson.
        $optionspersonenabled = array(
            BLOCK_CONTACTPERSON_SETTING_SELECT_YES => get_string('yes', 'block_contactperson', null, null, true),
            BLOCK_CONTACTPERSON_SETTING_SELECT_NO => get_string('no', 'block_contactperson', null, null, true)
        );

        $headingname = 'block_contactperson/contactpersonheadergeneral';
        $headingtitle = 'Allgemein';
        $settings->add(new admin_setting_heading(
            $headingname,
            $headingtitle,
            null
        ));

        $name = 'block_contactperson/contactsmaxamount';
        $title = 'Contact person maximum';
        $description = 'How many contacts are maximal displayed in one contact block.';
        $maxamount = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5);
        $settings->add(new admin_setting_configselect($name, $title, $description, 2, $maxamount));

        $name = 'block_contactperson/placeholderimage';
        $title = 'Placeholder image';
        $description = 'Placeholder for contacts without a profile picture.';
        $placeholderimagesetting = new admin_setting_configstoredfile($name, $title, $description,
                                 'placeholderimage', 0, array('maxfiles' => 1, 'accepted_types' => 'web_image'));
        $placeholderimagesetting->set_updatedcallback('theme_reset_all_caches');

        $settings->add($placeholderimagesetting);

        $addcontactpersonsettings($settings, $optionspersonenabled);

        $settings->add(new admin_setting_heading(
            'block_contactperson/contactpersonheaderaccess',
            get_string('addtionalperson', 'block_contactperson', null, null, true), 
            null
        ));

        $roles = role_get_names();
        $roleoptions = array();
        foreach ($roles as $role) {
            $roleoptions[$role->id] = $role->localname;
        }

        $settings->add(new admin_setting_configmultiselect(
            'block_contactperson/accessroles',
            get_string('accessroles', 'block_contactperson', null, null, true),
            "",
            null,
            $roleoptions
        ));
    }
}
