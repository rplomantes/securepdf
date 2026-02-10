<?php
function securepdf_supports($feature) {
switch ($feature) {
case FEATURE_MOD_INTRO:
return true;
default:
return null;
}
}