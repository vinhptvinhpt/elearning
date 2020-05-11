<?php
    require_once('config.php');
    $category = isset($_POST['category']) ? $_POST['category']:0;
    $current = isset($_POST['current']) ? $_POST['current']:1;
//    $pageCount = isset($_POST['pageCount']) ? $_POST['pageCount']:1;
    $recordPerPage = isset($_POST['recordPerPage']) ? $_POST['recordPerPage']:1;
    $txtSearch = isset($_POST['txtSearch']) ? $_POST['txtSearch']:null;

    //count total
    $sqlCountCoures = 'select count(*) as total from mdl_course mc inner join mdl_enrol me on mc.id = me.courseid inner join mdl_user_enrolments mue on me.id = mue.enrolid where me.enrol = \'manual\' and mc.deleted = 0 and mc.visible = 1 and mc.category <> 2 and mue.userid = '. $USER->id;
    if ($category > 0) {
        $sqlCountCoures .= ' and category = '.$category;
    }
    $total = array_values($DB->get_records_sql($sqlCountCoures))[0];


    $sqlGetCoures = 'select mc.id, mc.fullname, mc.category, mc.course_avatar, mc.estimate_duration, ( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections, ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = mc.id) as numofmodule, ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate <> 0 and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned from mdl_course mc inner join mdl_enrol me on mc.id = me.courseid inner join mdl_user_enrolments mue on me.id = mue.enrolid where me.enrol = \'manual\' and mc.deleted = 0 and mc.visible = 1 and mc.category <> 2 and mue.userid = ' . $USER->id;
    if ($category > 0) {
        $sqlGetCoures .= ' and category = '.$category;
    }
    if ($txtSearch) {
        $sqlGetCoures .= ' and mc.fullname like N\'%'.$txtSearch.'%\'';
    }
    $start_index = $current * $recordPerPage - $recordPerPage;
    $sqlGetCoures .= ' limit '.$recordPerPage.' offset '.$start_index;
//echo 'dsadsa';

    $courses = array_values($DB->get_records_sql($sqlGetCoures));
//echo '456';
//die;
    echo json_encode(['courses'=> $courses, 'totalPage' => ceil($total->total/$recordPerPage), 'totalRecords' => $total->total]);
