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

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // Enable watermark
    $settings->add(new admin_setting_configcheckbox(
        'mod_securepdf/enablewatermark',
        get_string('enablewatermark', 'mod_securepdf'),
        get_string('enablewatermark_desc', 'mod_securepdf'),
        1
    ));

    // Opacity
    $settings->add(new admin_setting_configtext(
        'mod_securepdf/opacity',
        get_string('opacity', 'mod_securepdf'),
        get_string('opacity_desc', 'mod_securepdf'),
        '0.25',
        PARAM_FLOAT
    ));

    // Font multiplier
    $settings->add(new admin_setting_configtext(
        'mod_securepdf/fontmultiplier',
        get_string('fontmultiplier', 'mod_securepdf'),
        get_string('fontmultiplier_desc', 'mod_securepdf'),
        '0.18',
        PARAM_FLOAT
    ));

    // Rotation
    $settings->add(new admin_setting_configtext(
        'mod_securepdf/rotation',
        get_string('rotation', 'mod_securepdf'),
        get_string('rotation_desc', 'mod_securepdf'),
        '45',
        PARAM_INT
    ));

    // Text color
    $settings->add(new admin_setting_configtext(
        'mod_securepdf/textcolor',
        get_string('textcolor', 'mod_securepdf'),
        get_string('textcolor_desc', 'mod_securepdf'),
        '150,150,150',
        PARAM_TEXT
    ));
}
