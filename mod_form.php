<?php
require_once($CFG->dirroot.'/course/moodleform_mod.php');


class mod_securepdf_mod_form extends moodleform_mod {
public function definition() {
$mform = $this->_form;


$mform->addElement('text', 'name', get_string('name'), ['size' => 64]);
$mform->setType('name', PARAM_TEXT);
$mform->addRule('name', null, 'required', null, 'client');


$this->standard_intro_elements();


$mform->addElement('filepicker', 'pdf', get_string('pdf', 'securepdf'), null, [
'accepted_types' => ['.pdf'],
'maxbytes' => 0
]);
$mform->addRule('pdf', null, 'required', null, 'client');


$this->standard_coursemodule_elements();
$this->add_action_buttons();
}
}