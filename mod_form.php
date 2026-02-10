<?php
require_once($CFG->dirroot.'/course/moodleform_mod.php');


class mod_securepdf_mod_form extends moodleform_mod {
public function definition() {
$mform = $this->_form;


$mform->addElement('text', 'name', get_string('name'), ['size' => 64]);
$mform->setType('name', PARAM_TEXT);
$mform->addRule('name', null, 'required', null, 'client');


$this->standard_intro_elements();


$mform->addElement('filemanager', 'pdf', get_string('pdf', 'securepdf'), null, [
    'subdirs' => 0,
    'maxbytes' => 0,
    'accepted_types' => ['.pdf']
]);
$mform->addRule('pdf', null, 'required', null, 'client');



$this->standard_coursemodule_elements();
$this->add_action_buttons();
}

public function data_preprocessing(&$defaultvalues) {
    // Draft item for the PDF file
    $draftitemid = file_get_submitted_draft_itemid('pdf');

    if (!empty($this->current->instance)) {
        // Editing existing activity
        $context = context_module::instance($this->current->id);
    } else {
        // Adding new activity
        // Use course context as fallback for draft
        $context = context_course::instance($this->_customdata['course']->id);
    }

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