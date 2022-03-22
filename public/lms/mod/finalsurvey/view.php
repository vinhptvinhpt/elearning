<?php

require('../../config.php');
require_once($CFG->dirroot.'/mod/finalsurvey/lib.php');
require_once($CFG->dirroot.'/mod/finalsurvey/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('p', 0, PARAM_INT);  // Page instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($p) {
    if (!$page = $DB->get_record('finalsurvey', array('id'=>$p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('finalsurvey', $page->id, $page->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('finalsurvey', $id)) {
        print_error('invalidcoursemodule');
    }
    $page = $DB->get_record('finalsurvey', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
// require_capability('mod/finalsurvey:view', $context);

// Completion and trigger events.
finalsurvey_view($page, $course, $cm, $context);

$PAGE->set_url('/mod/finalsurvey/view.php', array('id' => $cm->id));

$options = empty($page->displayoptions) ? array() : unserialize($page->displayoptions);

if ($inpopup and $page->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title($course->shortname.': '.$page->name);
    $PAGE->set_heading($course->fullname);
} else {
    $PAGE->set_title($course->shortname.': '.$page->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($page);
}
echo $OUTPUT->header();
if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($page->name), 2);
}

if (!empty($options['printintro'])) {
    if (trim(strip_tags($page->intro))) {
        echo $OUTPUT->box_start('mod_introbox', 'pageintro');
        echo format_module_intro('finalsurvey', $page, $cm->id);
        echo $OUTPUT->box_end();
    }
}

$content = file_rewrite_pluginfile_urls($page->content, 'pluginfile.php', $context->id, 'mod_finalsurvey', 'content', $page->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;

$content = preg_replace('/\s+/', '', $content);
$content = '<h2>'.get_string('surveycourse').'</h2>
<a href="#" onclick="location.replace(\''.strip_tags($content).'/'.$course->id.'\');">'.get_string('surveytext').'</a>';

$content = format_text($content, $page->contentformat, $formatoptions);


echo $OUTPUT->box($content, "generalbox center clearfix");

echo $OUTPUT->footer();
