<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Add a new Secure PDF instance
 */
function securepdf_add_instance($data, $mform) {
    global $DB;

    $data->timecreated  = time();
    $data->timemodified = time();

    // Insert instance
    $id = $DB->insert_record('securepdf', $data);

    // Get module context **after insert**
    $cm = get_coursemodule_from_instance('securepdf', $id, $data->course);
    $context = context_module::instance($cm->id);

    // Move uploaded files from draft to module context
    file_postupdate_standard_filemanager(
        $data,
        'pdf',
        [
            'subdirs' => 0,
            'maxbytes' => 0,
            'accepted_types' => ['.pdf']
        ],
        $context,
        'mod_securepdf',
        'pdf',
        0 // itemid = 0
    );

    return $id;
}


/**
 * Update an existing Secure PDF instance
 */
function securepdf_update_instance($data, $mform) {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();

    $DB->update_record('securepdf', $data);

    $cm = get_coursemodule_from_instance('securepdf', $data->id, $data->course);
    $context = context_module::instance($cm->id);

    file_postupdate_standard_filemanager(
        $data,
        'pdf',
        [
            'subdirs' => 0,
            'maxbytes' => 0,
            'accepted_types' => ['.pdf']
        ],
        $context,
        'mod_securepdf',
        'pdf',
        0
    );

    return true;
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
