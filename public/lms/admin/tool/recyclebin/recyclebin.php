<?php

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../../authen_api.php');
require_once($CFG->libdir . '/tablelib.php');

if (isset($_POST['data'])) {
    $data = $_POST['data'];
    if($data){
        $decode_data = decodeJWT($data);
        if($decode_data){
            $json_data = json_decode($decode_data);
            if(encrypt_key(API_KEY_SEC) == $json_data->data->app_key){
                $contextid = $json_data->data->contextid;
                $action = $json_data->data->action;
                $itemid = $json_data->data->itemid;
            }
        }
    }
}
// Get context from contextid
try {
    $context = context::instance_by_id($contextid, MUST_EXIST);
} catch (Exception $e) {
    echo 0;
    die;
}

// We could be a course or a category.
switch ($context->contextlevel) {
    case CONTEXT_COURSE:
        $recyclebin = new \tool_recyclebin\course_bin($context->instanceid);
        break;

    case CONTEXT_COURSECAT:
        $recyclebin = new \tool_recyclebin\category_bin($context->instanceid);
        break;

    default:
        echo 0;
        break;
}

if (!$recyclebin::is_enabled()) {
    echo 0;
}

// Action cases
if (!empty($action)) {

    $item = null;
    if ($action == 'restore' || $action == 'delete') {
        $item = $recyclebin->get_item($itemid);
    }

    switch ($action) {
            // Restore it.
        case 'restore':
            $recyclebin->restore_item($item);
            echo 1;
            break;

            // Delete it.
        case 'delete':
            $recyclebin->delete_item($item);
            echo 1;
            break;

            // Empty it.
        case 'empty':
            $recyclebin->delete_all_items();
            echo 1;
            break;
    }
} else {
    echo 0;
}

exit;
