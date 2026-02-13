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

/**
 * Provides secure PDF download with watermark for Moodle.
 *
 * @package    mod_securepdf
 * @copyright  2026 Nephila Web Technology Inc.
 * @author     Roy Plomantes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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


$iframeurl = new moodle_url('/mod/securepdf/download.php', [
    'id' => $cm->id,
    'inline' => 1
]);

$downloadurl = new moodle_url('/mod/securepdf/download.php', [
    'id' => $cm->id,
    'download' => 1
]);


/*
|--------------------------------------------------------------------------
| Capability Logic
|--------------------------------------------------------------------------
*/

// 👨‍🏫 Teachers / Managers → Can view iframe
if (has_capability('mod/securepdf:viewiframe', $context)) {

    echo html_writer::tag('button', 'View PDF', [
        'type' => 'button',
        'class' => 'btn btn-secondary',
        'data-toggle' => 'modal',
        'data-target' => '#securepdfModal'
    ]);

    // Modal HTML
    echo '
    <div class="modal fade" id="securepdfModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="max-width:90%;">
        <div class="modal-content" style="height:90vh;">
          <div class="modal-header">
            <h5 class="modal-title">'.format_string($securepdf->name).'</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body" style="height:100%; padding:0;">
            <iframe src="'.$iframeurl.'" 
                    style="width:100%; height:100%; border:none;">
            </iframe>
          </div>
        </div>
      </div>
    </div>';
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
