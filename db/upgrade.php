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