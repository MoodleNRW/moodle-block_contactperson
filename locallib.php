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

function block_contactperson_get_url_of_placeholderimage(): string {
    $fs = get_file_storage();
    $systemcontext = context_system::instance();
    $files = $fs->get_area_files($systemcontext->id, 'block_contactperson', 'placeholderimage', false, 'itemid', false);
    $file = reset($files);
    $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
    $file->get_itemid(), $file->get_filepath(), $file->get_filename());

    return $url;
}
