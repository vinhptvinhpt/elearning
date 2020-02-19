<?php

/**
 * Page survey for vietlot user about system
 *
 * [VinhPT]
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');

require_login();

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/my/surveydashboard.php');
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('my-index');
$PAGE->set_title(get_string('surveydashboard'));
$PAGE->set_heading($header);
$linksurvey = '/survey/viewlayout/10';

echo $OUTPUT->header();
?>
<style>
    iframe {
        width: 100%;
        height: calc(100vh - 100px) !important;
    }
</style>

<body>
    <div class="card">
        <div class="body-card p-3" >
            <h2><?= get_string("surveytitle")?></h2>
            <a href="#" onclick="location.replace('<?= $linksurvey ?>')"><?= get_string('surveytext') ?>
            </a>
        </div>
    </div>
</body>

<?php

echo $OUTPUT->footer();

?>