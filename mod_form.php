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

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_securepdf_mod_form extends moodleform_mod {

    public function definition() {
        $mform = $this->_form;

        // Activity name
        $mform->addElement('text', 'name', get_string('name'), ['size' => 64]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // Intro and description
        $this->standard_intro_elements();

        // Filemanager for PDF upload
        $mform->addElement('filemanager', 'pdf', get_string('pdf', 'securepdf'), null, [
            'subdirs' => 0,
            'maxbytes' => 0,
            'accepted_types' => ['.pdf']
        ]);
        $mform->addRule('pdf', null, 'required', null, 'client');

        // Standard module elements (availability, group, etc.)
        $this->standard_coursemodule_elements();

        // Add save/cancel buttons
        $this->add_action_buttons();
    }

    /**
     * Preprocess data for filemanager
     */
    public function data_preprocessing(&$defaultvalues) {
    // Draft item for filemanager
    $draftitemid = file_get_submitted_draft_itemid('pdf');

    // Determine context
    if (!empty($this->current->instance)) {
        // Edit mode
        $cm = get_coursemodule_from_instance('securepdf', $this->current->instance, $this->current->course, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
    } else {
        // Add mode
        if (!empty($this->current->course)) {
            $context = context_course::instance($this->current->course);
        } else {
            // Last resort fallback: try to use _customdata['course'] if available
            if (empty($this->_customdata['course']) || empty($this->_customdata['course']->id)) {
                throw new coding_exception('Course object not available in add mode');
            }
            $context = context_course::instance($this->_customdata['course']->id);
        }
    }

    // Prepare draft area
    file_prepare_draft_area(
        $draftitemid,
        $context->id,
        'mod_securepdf',
        'pdf',
        0,
        ['subdirs' => 0]
    );

    $defaultvalues['pdf'] = $draftitemid;
}

}
