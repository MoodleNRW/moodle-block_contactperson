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

use core\admintree_test;

defined('MOODLE_INTERNAL') || die();
// Constants which are use throughout this theme.
define('BLOCK_CONTACTPERSON_SETTING_SELECT_YES', 'yes');
define('BLOCK_CONTACTPERSON_SETTING_SELECT_NO', 'no');

/**
 * Add a setting for a contact person.
 */
$add_contactperson_configtexts = function($settings, $name, $i, $default = "", $conditionValue = 'yes'){
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
        $conditionValue
    );
};

/**
 * Add a setting for a contact person.
 * 
 * @param $settings The settings object.
 * @param $optionspersonenabled The options for the person enabled setting.
 * $add_contactperson_configtexts The function for adding a config text.
 */
$add_contactperson_settings = function($settings, $optionspersonenabled) use ($add_contactperson_configtexts) {
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
            $add_contactperson_configtexts($settings, $key, $i);
        }
    }
};

if ($hassiteconfig) {
    if ($ADMIN->fulltree) {

        // Option for enabling Contactperson.
        $optionspersonenabled = array(
            BLOCK_CONTACTPERSON_SETTING_SELECT_YES => get_string('yes', 'block_contactperson', array('no' => $i), null, true),
            BLOCK_CONTACTPERSON_SETTING_SELECT_NO => get_string('no', 'block_contactperson', array('no' => $i), null, true)
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
        $placeholderimagesetting = new admin_setting_configstoredfile($name, $title, $description, 'placeholderimage', 0,
        array('maxfiles' => 1, 'accepted_types' => 'web_image'));
        $placeholderimagesetting->set_updatedcallback('theme_reset_all_caches');

        $settings->add($placeholderimagesetting);

        $add_contactperson_settings($settings, $optionspersonenabled);

        $settings->add(new admin_setting_heading(
            'block_contactperson/contactpersonheaderaccess' ,
            get_string('addtionalperson', 'block_contactperson', array('no' => $i), null, true),
            null
        ));

        $roles = role_get_names();
        $roleoptions = array();
        foreach ($roles as $role) {
            $roleoptions[$role->id] = $role->localname;
        }

        $settings->add(new admin_setting_configmultiselect(
            'block_contactperson/accessroles',
            get_string('accessroles', 'block_contactperson', array('no' => $i), null, true),
            "",
            null,
            $roleoptions
        ));
    }
}
