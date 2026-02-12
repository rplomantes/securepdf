<?php
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
