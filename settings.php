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

defined('MOODLE_INTERNAL') || die();
// Constants which are use throughout this theme.
define('BLOCK_CONTACTPERSON_SETTING_SELECT_YES', 'yes');
define('BLOCK_CONTACTPERSON_SETTING_SELECT_NO', 'no');

if ($hassiteconfig) {
    // $ADMIN->add('localplugins', new admin_category('block_contactperson', new lang_string('pluginname', 'block_contactperson')));
    // $settingspage = new admin_settingpage('block_contactperson', new lang_string('pluginname', 'block_contactperson'));

    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_confightmleditor(
            'block_contactperson/additionalhtml',
            get_string('additionalhtml', 'block_contactperson', array('no' => $i), null, true),
            "",
            ""
        ));

        //Option for enabling Contactperson
        $optionspersonenabled = array( BLOCK_CONTACTPERSON_SETTING_SELECT_YES =>   get_string('yes', 'block_contactperson', array('no' => $i),null,true),
        BLOCK_CONTACTPERSON_SETTING_SELECT_NO =>   get_string('no', 'block_contactperson', array('no' => $i),null,true)
        );

        for ($i = 1; $i <= 15; $i++) {
            $settings->add(new admin_setting_configselect(
                'block_contactperson/personenabled'.$i,
                get_string('personenabled', 'block_contactperson', array('no' => $i), null, true),
                "",
                BLOCK_CONTACTPERSON_SETTING_SELECT_NO,
                $optionspersonenabled
            ));

            // Option for the Name 
            $settings->add(new admin_setting_configtext(
                'block_contactperson/name'.$i,
                get_string('name', 'block_contactperson', array('no' => $i), null, true),
                "",
                ""
            ));
            $settings->hide_if('block_contactperson/name'.$i, 'block_contactperson/personenabled'.$i, 'neq',
            'yes');

            // Option for the Contactpersonlink
            $settings->add(new admin_setting_configtext(
                'block_contactperson/contactpersonlink'.$i,
                get_string('contactpersonlink', 'block_contactperson', array('no' => $i), null, true),
                "",
                ""
            ));
            $settings->hide_if('block_contactperson/contactpersonlink'.$i, 'block_contactperson/personenabled'.$i, 'neq',
            'yes');

            // Option for the Email 
            $settings->add(new admin_setting_configtext(
                'block_contactperson/email'.$i,
                get_string('email', 'block_contactperson', array('no' => $i), null, true),
                "",
                ""
            ));
            $settings->hide_if('block_contactperson/email'.$i, 'block_contactperson/personenabled'.$i, 'neq',
            'yes');

            // Option for the fieldofacrion
            $settings->add(new admin_setting_configtext(
                'block_contactperson/fieldofaction'.$i,
                get_string('fieldofaction', 'block_contactperson', array('no' => $i), null, true),
                "",
                ""
            ));
            $settings->hide_if('block_contactperson/fieldofaction'.$i, 'block_contactperson/personenabled'.$i, 'neq',
            'yes');
        }
     }

    // $ADMIN->add('localplugins', $settings);
}