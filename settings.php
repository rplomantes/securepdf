<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage(
        'mod_securepdf_settings',
        get_string('pluginname', 'securepdf')
    );

    // Enable watermark
    $settings->add(new admin_setting_configcheckbox(
        'mod_securepdf/enablewatermark',
        'Enable watermark',
        'Enable watermark on downloaded PDFs',
        1
    ));

    // Opacity
    $settings->add(new admin_setting_configtext(
        'mod_securepdf/opacity',
        'Watermark opacity',
        'Value between 0.1 and 1',
        '0.25',
        PARAM_FLOAT
    ));

    // Font size multiplier
    $settings->add(new admin_setting_configtext(
        'mod_securepdf/fontmultiplier',
        'Font size multiplier',
        'Recommended: 0.15 - 0.25',
        '0.18',
        PARAM_FLOAT
    ));

    // Rotation
    $settings->add(new admin_setting_configtext(
        'mod_securepdf/rotation',
        'Rotation angle',
        'Degrees (e.g. 45)',
        '45',
        PARAM_INT
    ));

    // Text color
    $settings->add(new admin_setting_configtext(
        'mod_securepdf/textcolor',
        'Text color (RGB)',
        'Example: 150,150,150',
        '150,150,150',
        PARAM_TEXT
    ));

    $ADMIN->add('modsettings', $settings);
}
