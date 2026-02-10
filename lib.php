<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Add a new Secure PDF instance
 *
 * @param stdClass $data Submitted form data
 * @param mod_form $mform The form instance
 * @return int The new instance ID
 */
function securepdf_add_instance($data, $mform) {
    global $DB;

    $data->timecreated  = time();
    $data->timemodified = time();

    // Insert new record in database
    $id = $DB->insert_record('securepdf', $data);

    // NOTE: Cannot save files here because module context does not exist yet
    // File saving is handled in securepdf_update_instance(), called immediately after add

    return $id;
}

/**
 * Update an existing Secure PDF instance
 *
 * @param stdClass $data Submitted form data
 * @param mod_form $mform The form instance
 * @return bool True on success
 */
function securepdf_update_instance($data, $mform) {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();

    // Update DB record
    $DB->update_record('securepdf', $data);

    // Get the course module and module context
    $cm = get_coursemodule_from_instance('securepdf', $data->id, $data->course, false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    // Save uploaded PDF into module context
    file_postupdate_standard_filemanager(
        $data,
        'pdf',             // Form element name
        [
            'subdirs' => 0,
            'maxbytes' => 0,
            'accepted_types' => ['.pdf']
        ],
        $context,
        'mod_securepdf',   // Component
        'pdf',             // File area
        0                  // Item ID
    );

    return true;
}

/**
 * Delete a Secure PDF instance
 *
 * @param int $id Instance ID
 * @return bool True on success
 */
function securepdf_delete_instance($id) {
    global $DB;

    if (!$securepdf = $DB->get_record('securepdf', ['id' => $id])) {
        return false;
    }

    // Delete the record
    $DB->delete_records('securepdf', ['id' => $securepdf->id]);

    return true;
}

/**
 * Module feature support
 *
 * @param string $feature FEATURE_ constant
 * @return bool|null
 */
function securepdf_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}
