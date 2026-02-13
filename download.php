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
require_once(__DIR__ . '/vendor/autoload.php');

require_login();
\core\session\manager::write_close();

use setasign\Fpdi\TcpdfFpdi;

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('securepdf', $id, 0, false, MUST_EXIST);
$context = context_module::instance($cm->id);
require_capability('mod/securepdf:viewpdf', $context);

$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_securepdf', 'pdf', 0, 'filename', false);

if (empty($files)) {
    throw new moodle_exception('filenotfound', 'error');
}

$file = reset($files);

$config = get_config('mod_securepdf');
$enablewatermark = $config->enablewatermark ?? 1;
$opacity         = $config->opacity ?? 0.25;
$rotation        = $config->rotation ?? 45;
$textcolor       = $config->textcolor ?? '150,150,150';

list($r, $g, $b) = array_map('intval', explode(',', $textcolor));

$pdf = new TcpdfFpdi();
$pdf->SetProtection(['print'], '', null);

$tempfile = tempnam(sys_get_temp_dir(), 'spdf_in');
$file->copy_content_to($tempfile);

$pagecount = $pdf->setSourceFile($tempfile);

for ($pageNo = 1; $pageNo <= $pagecount; $pageNo++) {

    $templateId = $pdf->importPage($pageNo);
    $size = $pdf->getTemplateSize($templateId);

    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($templateId);

    if ($enablewatermark) {

    $watermarktext = $USER->email;

    // Create large canvas
    $imgWidth = 2000;
    $imgHeight = 600;

    $image = imagecreatetruecolor($imgWidth, $imgHeight);
    imagesavealpha($image, true);

    $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
    imagefill($image, 0, 0, $transparent);

    // Strong visible grey
    $color = imagecolorallocatealpha($image, 120, 120, 120, 40);

    $fontPath = __DIR__ . '/fonts/DejaVuSans-Bold.ttf';

    // Dynamic font size relative to PDF page width
    $fontSize = ($size['width'] * 0.5); 
    // increase multiplier if needed (try 3–6 range)

    // Center text in image
    $bbox = imagettfbbox($fontSize, 0, $fontPath, $watermarktext);
    $textWidth = $bbox[2] - $bbox[0];
    $textHeight = $bbox[1] - $bbox[7];

    $x = ($imgWidth - $textWidth) / 2;
    $y = ($imgHeight + $textHeight) / 2;

    imagettftext(
        $image,
        $fontSize,
        0,
        $x,
        $y,
        $color,
        $fontPath,
        $watermarktext
    );

    $wmfile = tempnam(sys_get_temp_dir(), 'wm') . '.png';
    imagepng($image, $wmfile);
    imagedestroy($image);

    // Apply watermark image to PDF
    $pdf->SetAlpha(0.30);

    $centerX = $size['width'] / 2;
    $centerY = $size['height'] / 2;

    $pdf->StartTransform();
    $pdf->Rotate((float)$rotation, $centerX, $centerY);

    $pdf->Image(
        $wmfile,
        0,
        $centerY - 60,
        $size['width']
    );

    $pdf->StopTransform();
    $pdf->SetAlpha(1);

    unlink($wmfile);
}



}

$inline   = optional_param('inline', 0, PARAM_INT);
$download = optional_param('download', 0, PARAM_INT);
$filename = clean_filename($file->get_filename());

if ($download) {
    $pdf->Output($filename, 'D');
} else if ($inline) {
    $pdf->Output($filename, 'I');
} else {
    $pdf->Output($filename, 'D');
}

unlink($tempfile);
exit;
