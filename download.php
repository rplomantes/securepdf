<?php
require('../../config.php');
require_login();

use setasign\Fpdi\TcpdfFpdi;

global $USER;

// 1. Get course module ID
$cmid = required_param('id', PARAM_INT);

// 2. Get course module and context
$cm = get_coursemodule_from_id('securepdf', $cmid, 0, false, MUST_EXIST);
$context = context_module::instance($cm->id);

// 3. Require view capability
require_capability('mod/securepdf:view', $context);

// 4. Get file storage
$fs = get_file_storage();

// 5. Fetch PDF file (itemid = 0)
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

// 6. Create temporary files
$tempin  = tempnam(sys_get_temp_dir(), 'spdf_in');
$tempout = tempnam(sys_get_temp_dir(), 'spdf_out');
$file->copy_content_to($tempin);

// 7. Watermark PDF
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

// 8. Output watermarked PDF
$pdf->Output($tempout, 'F');

// 9. Force download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="secure.pdf"');
header('Content-Length: ' . filesize($tempout));
readfile($tempout);

// 10. Cleanup
@unlink($tempin);
@unlink($tempout);
exit;
