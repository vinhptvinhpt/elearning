<?php

/**
 * Page guideline for vietlot user
 *
 * [VinhPT]
 */

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/my/lib.php');

require_login();
global $DB, $USER;
// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/my/guideline.php');
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('my-index');
$PAGE->set_title(get_string('guideline'));
$PAGE->set_heading($header);

//

//
$sql = "select content from tms_configs where target = 'guideline'";
$guide_line = array_values($DB->get_records_sql($sql))[0]->content;
//
//get permission
$sqlCheckPermission = 'SELECT permission_slug, roles.name from `model_has_roles` as `mhr`
inner join `roles` on `roles`.`id` = `mhr`.`role_id`
left join `permission_slug_role` as `psr` on `psr`.`role_id` = `mhr`.`role_id`
inner join `mdl_user` as `mu` on `mu`.`id` = `mhr`.`model_id`
where `mhr`.`model_id` = ' . $USER->id . ' and `mhr`.`model_type` = "App/MdlUser"';

$getPermissions = $DB->get_records_sql($sqlCheckPermission);

$allowCms = false;
$permissions = array_values($getPermissions);
foreach ($permissions as $permission) {
    if (in_array($permission->name, ['admin', 'root'])) {
        $allowCms = true;
        break;
    }
}

$sqlGetOrganization = 'SELECT f.id, f.level, f.code
            FROM (SELECT @id AS _id, (SELECT @id := parent_id FROM tms_organization WHERE id = _id)
            FROM (SELECT @id := (select organization_id from tms_organization_employee where user_id= ' . $USER->id . ')) tmp1
            JOIN tms_organization ON @id IS NOT NULL) tmp2
            JOIN tms_organization f ON tmp2._id = f.id
            where f.level = 1 limit 1';
$organization = array_values($DB->get_records_sql($sqlGetOrganization))[0];
$organizationLower = strtolower($organization->code);
if (strpos($organizationLower, 'bg') === 0 || strpos($organizationLower, 'begodi') === 0) {
    $_SESSION["color"] = '#333';
} else if (strpos($organizationLower, 'ea') === 0 || strpos($organizationLower, 'easia') === 0) {
    $_SESSION["color"] = '#862055';
} else if (strpos($organizationLower, 'ev') === 0 || strpos($organizationLower, 'exotic') === 0) {
    $_SESSION["color"] = '#CAB143';
} else if (strpos($organizationLower, 'av') === 0 || strpos($organizationLower, 'avana') === 0) {
    $_SESSION["color"] = '#202020';
} else if (strpos($organizationLower, 'tve') === 0) {
    $_SESSION["color"] = '#007f48';
} else {
    $_SESSION["color"] = '#0080EF';
}


echo $OUTPUT->header();
?>
<style>
    iframe, .content {
        width: 100%;
        height: calc(100vh - 100px) !important;
    }

    .content {
        border-width: 2px;
        border-style: inset;
        border-color: initial;
        border-image: initial;
        background-color: #fff;
        overflow: scroll;
        padding: 2%;
        overflow-wrap: break-word;
    }

    .btn-guideline-click{
        background-color: <?=$_SESSION["color"]?>;
        border-color: <?=$_SESSION["color"]?>;
    }

    .btn-guideline-click:hover{
        background-color: <?=$_SESSION["color"]?>;
        border-color: <?=$_SESSION["color"]?>;
        opacity: 0.7;
    }
</style>
<p></p>
<?php if ($allowCms) { ?>
    <p align="right">
        <button type="button" class="btn btn-info py-2 px-3 btn-guideline-click" style="font-size: 20px"
                onclick="window.location.href='/lms/my/edit_guideline.php'">Edit
        </button>
    </p>
<?php } ?>
<!--    <iframe src="introduction/user/user_guideline.html"></iframe>-->
<div class="content mb-2"><?php echo $guide_line; ?></div>
<p align="right">
    <button type="button" class="btn btn-info py-2 px-3 btn-guideline-click" style="font-size: 20px"
            onclick="window.location.href='/lms'"><?= get_string('learnnow') ?></button>
</p>
<?php


echo $OUTPUT->footer();

die;
?>
