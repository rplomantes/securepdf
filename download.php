<?php

require('../../config.php');
require_once(__DIR__ . '/vendor/autoload.php');

require_login();

// Release session immediately (prevents Redis lock issue)
\core\session\manager::write_close();

use setasign\Fpdi\TcpdfFpdi;

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('securepdf', $id, 0, false, MUST_EXIST);
$context = context_module::instance($cm->id);

require_capability('mod/securepdf:view', $context);

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
    throw new moodle_exception('filenotfound', 'error');
}

$file = reset($files);

$tempin  = tempnam(sys_get_temp_dir(), 'spdf_in');
$tempout = tempnam(sys_get_temp_dir(), 'spdf_out');

$file->copy_content_to($tempin);

// Watermark PDF
$pdf = new TcpdfFpdi();
$pagecount = $pdf->setSourceFile($tempin);

for ($i = 1; $i <= $pagecount; $i++) {

    $tpl = $pdf->importPage($i);
    $size = $pdf->getTemplateSize($tpl);

    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($tpl);

    $pdf->SetAlpha(0.12);
    $pdf->SetFont('helvetica', 'B', $size['height'] * 0.08);
    $pdf->SetTextColor(180, 180, 180);

    $pdf->StartTransform();
    $pdf->Rotate(45, $size['width']/2, $size['height']/2);
    $pdf->Text($size['width'] * 0.05, $size['height'] * 0.45, $USER->email);
    $pdf->StopTransform();
}

$pdf->Output($tempout, 'F');

// Serve using Moodle safe method
$filename = clean_filename($file->get_filename());
send_temp_file($tempout, $filename);

// Cleanup
@unlink($tempin);
@unlink($tempout);
exit;
