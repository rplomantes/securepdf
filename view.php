<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('securepdf', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$securepdf = $DB->get_record('securepdf', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

// Page setup FIRST
$PAGE->set_url('/mod/securepdf/view.php', ['id' => $id]);
$PAGE->set_title(format_string($securepdf->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

// Get file
$fs = get_file_storage();
$files = $fs->get_area_files(
    $context->id,
    'mod_securepdf',
    'pdf',
    0,
    'filename',
    false
);

if (empty($files)) {
    echo $OUTPUT->notification('PDF file not found.', 'error');
    echo $OUTPUT->footer();
    exit;
}

$file = reset($files);

// Pluginfile URL
$fileurl = moodle_url::make_pluginfile_url(
    $context->id,
    'mod_securepdf',
    'pdf',
    0,
    '/',
    $file->get_filename()
);

// Watermarked download URL
// $downloadurl = new moodle_url('/mod/securepdf/download.php', [
//     'id' => $cm->id
// ]);

$downloadurl = new moodle_url('/mod/securepdf/download.php', [
    'id' => $cm->id,
    'download' => 1  // custom flag
]);

/*
|--------------------------------------------------------------------------
| Capability Logic
|--------------------------------------------------------------------------
*/

// 👨‍🏫 Teachers / Managers → Can view iframe
if (has_capability('mod/securepdf:viewiframe', $context)) {

    echo html_writer::tag('iframe', '', [
        'src' => $downloadurl,
        'width' => '100%',
        'height' => '800',
        'style' => 'border:1px solid #ccc;'
    ]);
}

// 👨‍🎓 Students + Teachers → Can download
if (has_capability('mod/securepdf:viewpdf', $context)) {

    echo html_writer::div(
        html_writer::link(
            $downloadurl,
            get_string('download', 'moodle'),
            ['class' => 'btn btn-primary mt-3']
        ),
        'text-center'
    );
}

echo $OUTPUT->footer();
