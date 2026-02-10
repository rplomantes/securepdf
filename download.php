<?php
require('../../config.php');
require_login();

use setasign\Fpdi\TcpdfFpdi;

// 1. Get activity ID
$id = required_param('id', PARAM_INT);

// 2. Get course module
$cm = get_coursemodule_from_id('securepdf', $id, 0, false, MUST_EXIST);

// 3. Get context + permission
$context = context_module::instance($cm->id);
require_capability('mod/securepdf:view', $context);

// 4. Get file storage
$fs = get_file_storage();

// 5. Fetch PDF file
$files = $fs->get_area_files(
    $context->id,
    'mod_securepdf',
    'pdf',
    0,
    'filename',
    false
);

if (empty($files)) {
    throw new moodle_exception('filenotfound', 'error', '', null, 'Secure PDF file not found');
}

$file = reset($files);

// 6. Create temp files
$tempin  = tempnam(sys_get_temp_dir(), 'spdf_in');
$tempout = tempnam(sys_get_temp_dir(), 'spdf_out');

// 7. Copy Moodle file to temp
$file->copy_content_to($tempin);

// 8. Watermark PDF
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
    $pdf->Rotate(45, $size['width'] / 2, $size['height'] / 2);
    $pdf->Text(
        $size['width'] * 0.05,
        $size['height'] * 0.45,
        $USER->email
    );
    $pdf->StopTransform();
}

// 9. Output watermarked PDF
$pdf->Output($tempout, 'F');

// 10. Force download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="secure.pdf"');
header('Content-Length: ' . filesize($tempout));
readfile($tempout);

// 11. Cleanup
@unlink($tempin);
@unlink($tempout);
exit;
