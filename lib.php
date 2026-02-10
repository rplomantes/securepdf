<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Add a new Secure PDF instance
 */
function securepdf_add_instance($data, $mform) {
    global $DB;

    $data->timecreated  = time();
    $data->timemodified = time();

    $id = $DB->insert_record('securepdf', $data);

    // DO NOT call get_coursemodule_from_instance() here!
    // Save files will be handled automatically later
    return $id;
}



/**
 * Update an existing Secure PDF instance
 */
function securepdf_after_add_instance($data, $mform) {
    global $DB;

    $cm = get_coursemodule_from_instance('securepdf', $data->id, $data->course, false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    file_postupdate_standard_filemanager(
        $data,
        'pdf',
        ['subdirs'=>0, 'maxbytes'=>0, 'accepted_types'=>['.pdf']],
        $context,
        'mod_securepdf',
        'pdf',
        0
    );
}



/**
 * Delete a Secure PDF instance
 */
function securepdf_delete_instance($id) {
    global $DB;

    if (!$securepdf = $DB->get_record('securepdf', ['id' => $id])) {
        return false;
    }

    $DB->delete_records('securepdf', ['id' => $securepdf->id]);

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
