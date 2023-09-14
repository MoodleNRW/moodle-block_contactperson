<?php

function block_contactperson_get_url_of_placeholderimage(): string {
    $fs = get_file_storage();
    $systemcontext = context_system::instance();
    $files = $fs->get_area_files($systemcontext->id, 'block_contactperson', 'placeholderimage', false, 'itemid', false);
    $file = reset($files);
    $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
    $file->get_itemid(), $file->get_filepath(), $file->get_filename());

    return $url;
}