<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(RESOURCELIB_DISPLAY_OPEN, RESOURCELIB_DISPLAY_POPUP));
    $defaultdisplayoptions = array(RESOURCELIB_DISPLAY_OPEN);

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configmultiselect('gamebgt/displayoptions',
        get_string('displayoptions', 'gamebgt'), get_string('configdisplayoptions', 'gamebgt'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('gamebgtmodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox('gamebgt/printheading',
        get_string('printheading', 'gamebgt'), get_string('printheadingexplain', 'gamebgt'), 1));
    $settings->add(new admin_setting_configcheckbox('gamebgt/printintro',
        get_string('printintro', 'gamebgt'), get_string('printintroexplain', 'gamebgt'), 0));
    $settings->add(new admin_setting_configcheckbox('gamebgt/printlastmodified',
        get_string('printlastmodified', 'gamebgt'), get_string('printlastmodifiedexplain', 'gamebgt'), 1));
    $settings->add(new admin_setting_configselect('gamebgt/display',
        get_string('displayselect', 'gamebgt'), get_string('displayselectexplain', 'gamebgt'), RESOURCELIB_DISPLAY_OPEN, $displayoptions));
    $settings->add(new admin_setting_configtext('gamebgt/popupwidth',
        get_string('popupwidth', 'gamebgt'), get_string('popupwidthexplain', 'gamebgt'), 620, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('gamebgt/popupheight',
        get_string('popupheight', 'gamebgt'), get_string('popupheightexplain', 'gamebgt'), 450, PARAM_INT, 7));
}
