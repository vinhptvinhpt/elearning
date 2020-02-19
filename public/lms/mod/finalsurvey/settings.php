<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(RESOURCELIB_DISPLAY_OPEN, RESOURCELIB_DISPLAY_POPUP));
    $defaultdisplayoptions = array(RESOURCELIB_DISPLAY_OPEN);

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configmultiselect('finalsurvey/displayoptions',
        get_string('displayoptions', 'finalsurvey'), get_string('configdisplayoptions', 'finalsurvey'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('finalsurveymodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox('finalsurvey/printheading',
        get_string('printheading', 'finalsurvey'), get_string('printheadingexplain', 'finalsurvey'), 1));
    $settings->add(new admin_setting_configcheckbox('finalsurvey/printintro',
        get_string('printintro', 'finalsurvey'), get_string('printintroexplain', 'finalsurvey'), 0));
    $settings->add(new admin_setting_configcheckbox('finalsurvey/printlastmodified',
        get_string('printlastmodified', 'finalsurvey'), get_string('printlastmodifiedexplain', 'finalsurvey'), 1));
    $settings->add(new admin_setting_configselect('finalsurvey/display',
        get_string('displayselect', 'finalsurvey'), get_string('displayselectexplain', 'finalsurvey'), RESOURCELIB_DISPLAY_OPEN, $displayoptions));
    $settings->add(new admin_setting_configtext('finalsurvey/popupwidth',
        get_string('popupwidth', 'finalsurvey'), get_string('popupwidthexplain', 'finalsurvey'), 620, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('finalsurvey/popupheight',
        get_string('popupheight', 'finalsurvey'), get_string('popupheightexplain', 'finalsurvey'), 450, PARAM_INT, 7));
}
