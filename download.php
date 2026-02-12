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

require_capability('mod/securepdf:viewpdf', $context);

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
//settings
$config = get_config('mod_securepdf');

$enablewatermark = $config->enablewatermark ?? 1;
$opacity         = $config->opacity ?? 0.25;
$fontmultiplier  = $config->fontmultiplier ?? 0.18;
$rotation        = $config->rotation ?? 45;
$textcolor       = $config->textcolor ?? '150,150,150';

list($r, $g, $b) = array_map('intval', explode(',', $textcolor));

// Watermark PDF
$pdf = new TcpdfFpdi();
$pagecount = $pdf->setSourceFile($tempin);
if ($enablewatermark) {

    $pdf->SetAlpha((float)$opacity);
    $pdf->SetFont('helvetica', 'B', $size['width'] * (float)$fontmultiplier);
    $pdf->SetTextColor($r, $g, $b);

    $centerX = $size['width'] / 2;
    $centerY = $size['height'] / 2;
    $textWidth = $pdf->GetStringWidth($USER->email);

    $pdf->StartTransform();
    $pdf->Rotate((float)$rotation, $centerX, $centerY);
    $pdf->Text($centerX - ($textWidth / 2), $centerY, $USER->email);
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
