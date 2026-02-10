<?php
$capabilities = [
'mod/securepdf:addinstance' => [
'captype' => 'write',
'contextlevel' => CONTEXT_COURSE,
'archetypes' => [
'editingteacher' => CAP_ALLOW,
'manager' => CAP_ALLOW
]
],
'mod/securepdf:view' => [
'captype' => 'read',
'contextlevel' => CONTEXT_MODULE,
'archetypes' => [
'student' => CAP_ALLOW,
'teacher' => CAP_ALLOW
]
],
'mod/securepdf:viewpdf' => [
'captype' => 'read',
'contextlevel' => CONTEXT_MODULE,
'archetypes' => [
'teacher' => CAP_ALLOW,
'editingteacher' => CAP_ALLOW,
'manager' => CAP_ALLOW
]
]
];