<?php
require('../../config.php');
require_once(__DIR__ . '/vendor/autoload.php');

require_login();
\core\session\manager::write_close();

use setasign\Fpdi\TcpdfFpdi;

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('securepdf', $id, 0, false, MUST_EXIST);
$context = context_module::instance($cm->id);

require_capability('mod/securepdf:viewpdf', $context);

// Get the PDF file
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_securepdf', 'pdf', 0, 'filename', false);
if (empty($files)) {
    throw new moodle_exception('filenotfound', 'error');
}
$file = reset($files);

// Load admin settings
$config = get_config('mod_securepdf');
$enablewatermark = $config->enablewatermark ?? 1;
$opacity         = $config->opacity ?? 0.5;
$fontmultiplier  = $config->fontmultiplier ?? 0.25;
$rotation        = $config->rotation ?? 45;
$textcolor       = $config->textcolor ?? '150,150,150';
list($r, $g, $b) = array_map('intval', explode(',', $textcolor));

// Prepare FPDI
$pdf = new TcpdfFpdi();
$tempfile = tempnam(sys_get_temp_dir(), 'spdf_in');
$file->copy_content_to($tempfile);

// Safety check
if (!file_exists($tempfile) || filesize($tempfile) == 0) {
    throw new moodle_exception('filenotfound', 'error', '', 'PDF is missing or empty');
}

$pagecount = $pdf->setSourceFile($tempfile);

for ($pageNo = 1; $pageNo <= $pagecount; $pageNo++) {
    $templateId = $pdf->importPage($pageNo);
    $size = $pdf->getTemplateSize($templateId);
    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

    // Overlay the original PDF content first
    $pdf->useTemplate($templateId);

    // Now add watermark on top
    if ($enablewatermark) {
        $pdf->SetAlpha((float)$opacity); // semi-transparent
        $pdf->SetFont('helvetica', 'B', $size['width'] * (float)$fontmultiplier);
        $pdf->SetTextColor($r, $g, $b);

        $centerX = $size['width']/2;
        $centerY = $size['height']/2;
        $textWidth = $pdf->GetStringWidth($USER->email);

        $pdf->StartTransform();
        $pdf->Rotate((float)$rotation, $centerX, $centerY);
        $pdf->Text($centerX - ($textWidth/2), $centerY, $USER->email);
        $pdf->StopTransform();

        $pdf->SetAlpha(1); // reset transparency
        $pdf->SetTextColor(0, 0, 0); // reset color
    }
}


// Decide inline vs download
$inline = optional_param('inline', 0, PARAM_INT);
$download = optional_param('download', 0, PARAM_INT);
$filename = clean_filename($file->get_filename());

if ($download) {
    $pdf->Output($filename, 'D'); // download
} else if ($inline) {
    $pdf->Output($filename, 'I'); // inline
} else {
    $pdf->Output($filename, 'D'); // fallback
}

@unlink($tempfile);
exit;
