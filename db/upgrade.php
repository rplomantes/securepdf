<?php


defined('MOODLE_INTERNAL') || die();


function xmldb_securepdf_upgrade($oldversion) {
global $DB;


$dbman = $DB->get_manager();


if ($oldversion < 2026021001) {
// Example future upgrade logic goes here


// Always finish with savepoint
upgrade_mod_savepoint(true, 2026021001, 'securepdf');
}


return true;
}