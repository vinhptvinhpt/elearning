<?php

/**
 * Page introduction for vietlot user about system
 *
 * [VinhPT]
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');

require_login();

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/my/vietlot_introduction.php');
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('my-index');
$PAGE->set_title(get_string('vietlot_introduction'));
$PAGE->set_heading($header);

echo $OUTPUT->header();
?>
<style>
    iframe{
        width: 100%;
        height: calc(100vh - 100px) !important;
    }
</style>
<iframe  src="introduction/lmshtml/docx.html"></iframe>

<?php

echo $OUTPUT->footer();

?>