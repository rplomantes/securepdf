<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('securepdf', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$securepdf = $DB->get_record('securepdf', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/securepdf:view', $context);

$PAGE->set_url('/mod/securepdf/view.php', ['id' => $id]);
$PAGE->set_title(format_string($securepdf->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

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

$fileurl = moodle_url::make_pluginfile_url(
    $context->id,
    'mod_securepdf',
    'pdf',
    0,
    '/',
    $file->get_filename()
);

echo html_writer::tag('iframe', '', [
    'src' => $fileurl,
    'width' => '100%',
    'height' => '800',
    'style' => 'border:1px solid #ccc;'
]);

echo $OUTPUT->footer();
