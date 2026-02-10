<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Add a new Secure PDF instance
 */
function securepdf_add_instance($data, $mform) {
    global $DB;

    $data->timecreated  = time();
    $data->timemodified = time();

    // Insert the module instance
    $id = $DB->insert_record('securepdf', $data);

    // Save uploaded PDF to file area
    $context = context_module::instance($data->coursemodule);
    if ($mform) {
        file_save_draft_area_files(
            $data->pdf,
            $context->id,
            'mod_securepdf',
            'pdf',
            0,
            ['subdirs'=>0, 'maxfiles'=>1]
        );
    }

    return $id;
}


function securepdf_update_instance($data, $mform) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('securepdf', $data);

    $context = context_module::instance($data->coursemodule);

    if ($mform) {
        file_save_draft_area_files(
            $data->pdf,
            $context->id,
            'mod_securepdf',
            'pdf',
            0,
            ['subdirs'=>0, 'maxfiles'=>1]
        );
    }

    return true;
}


function securepdf_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    require_login($course, true, $cm);

    if ($filearea !== 'pdf') {
        return false;
    }

    $fs = get_file_storage();
    $filename = array_pop($args);
    $file = $fs->get_file($context->id, 'mod_securepdf', 'pdf', 0, '/', $filename);

    if (!$file) {
        return false;
    }

    send_stored_file($file, 0, 0, false);
}


/**
 * Delete a Secure PDF instance
 */
function securepdf_delete_instance($id) {
    global $DB;

    if (!$securepdf = $DB->get_record('securepdf', ['id' => $id])) {
        return false;
    }

    // Delete associated files
    $cm = get_coursemodule_from_instance('securepdf', $id, $securepdf->course, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_securepdf', 'pdf');

    // Delete DB record
    $DB->delete_records('securepdf', ['id' => $id]);

    return true;
}


/**
 * Feature support
 */
function securepdf_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}
