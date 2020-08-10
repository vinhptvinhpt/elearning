<?php

/**
 * Page guideline for vietlot user
 *
 * [VinhPT]
 */

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/my/lib.php');

require_login();

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/my/guideline.php');
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
    <p></p>
    <iframe src="introduction/user/user_guideline.html"></iframe>
    <p align="right">
        <button type="button" class="btn btn-info py-2 px-3" style="font-size: 20px" onclick="window.location.href='/lms'"><?= get_string('learnnow') ?></button>
    </p>
<?php


echo $OUTPUT->footer();

die;
?>
