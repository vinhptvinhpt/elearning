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


    $sqlGetCoures = 'select mc.id, mc.fullname, mc.category, SUBSTR(mc.course_avatar, 2) as course_avatar, mc.estimate_duration,
( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections,
( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = mc.id) as numofmodule,
( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate <> 0 and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned,
FLOOR(mccc.gradepass) as pass_score,
(select mgg.finalgrade from mdl_grade_items mgi join mdl_grade_grades mgg on mgg.itemid = mgi.id where mgg.userid=mue.userid and mgi.courseid=mc.id group by mgi.courseid) as finalgrade,

GROUP_CONCAT(CONCAT(tud.fullname, \' created_at \',  muet.timecreated)) as teachers

from mdl_course mc
inner join mdl_enrol me on mc.id = me.courseid
inner join mdl_user_enrolments mue on me.id = mue.enrolid
left JOIN mdl_course_completion_criteria mccc on mccc.course = mc.id

left join mdl_enrol met on mc.id = met.courseid AND met.roleid = 4
left join mdl_user_enrolments muet on met.id = muet.enrolid
left join tms_user_detail tud on tud.user_id = muet.userid
left join tms_organization_employee toe on toe.user_id = muet.userid
left join tms_organization tor on tor.id = toe.organization_id

where me.enrol = \'manual\' and mc.deleted = 0 and mc.visible = 1 and mc.category <> 2 and mue.userid = ' . $USER->id;


    if ($category > 0) {
        $sqlGetCoures .= ' and category = '.$category;
    }
    if ($txtSearch) {
        $sqlGetCoures .= ' and mc.fullname like N\'%'.$txtSearch.'%\'';
    }
    $start_index = $current * $recordPerPage - $recordPerPage;
    $sqlGetCoures .= ' group by mc.id';
    $sqlGetCoures .= ' limit '.$recordPerPage.' offset '.$start_index;
//echo 'dsadsa';

    $courses = array_values($DB->get_records_sql($sqlGetCoures));

    foreach ($courses as &$course) {
        $teachers = $course->teachers;
        $teacher_name = '';
        $teacher_created = 0;

        if (strlen($teachers) != 0) {
            $teachers_and_created = explode(',' , $teachers);
            foreach ($teachers_and_created as $teacher_and_created) {
                $fetch = explode(' created_at ', $teacher_and_created);
                if (intval($fetch[1]) > $teacher_created) {
                    $teacher_created = intval($fetch[1]);
                    $teacher_name = $fetch[0];
                }
            }
        }
        $course->teacher_name = $teacher_name;
        //$course->teacher_created = $teacher_created;

    }

    echo json_encode(['courses'=> $courses, 'totalPage' => ceil($total->total/$recordPerPage), 'totalRecords' => $total->total]);
