<?php
require('../../config.php');
require_login();

$id = required_param('id', PARAM_INT);

// Get course module and context
$cm = get_coursemodule_from_id('securepdf', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/securepdf:view', $context);

$PAGE->set_url('/mod/securepdf/view.php', ['id' => $cm->id]);
$PAGE->set_title($cm->name);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo html_writer::tag('p', get_string('pluginname', 'securepdf'));

// Download button
echo html_writer::link(
    new moodle_url('/mod/securepdf/download.php', ['id' => $cm->id]),
    get_string('download', 'securepdf'),
    ['class' => 'btn btn-primary']
);

echo $OUTPUT->footer();
