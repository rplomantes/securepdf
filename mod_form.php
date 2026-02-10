<?php
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

        if (!empty($this->current->instance)) {
            // Edit mode: module exists
            $cm = get_coursemodule_from_instance('securepdf', $this->current->instance, $this->_customdata['course']->id, false, MUST_EXIST);
            $context = context_module::instance($cm->id);
        } else {
            // Add mode: module not created yet, use course context
            if (empty($this->_customdata['course']) || empty($this->_customdata['course']->id)) {
                throw new coding_exception('Course object not passed to the form in add mode');
            }
            $context = context_course::instance($this->_customdata['course']->id);
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
