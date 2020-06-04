<?php

/**
 * Page guideline for vietlot user 
 *
 * [VinhPT]
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');

require_login();

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/my/managemarket_guide.php');
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('my-index');
$PAGE->set_title(get_string('guideline'));
$PAGE->set_heading($header);

echo $OUTPUT->header();
?>
<style>
    iframe {
        width: 100%;
        height: calc(100vh - 100px) !important;
    }
</style>
<iframe src="introduction/cvkd.html"></iframe>

<?php


echo $OUTPUT->footer();

?>