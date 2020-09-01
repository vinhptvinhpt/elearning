<?php
require_once('../config.php');

$category = isset($_POST['category']) ? $_POST['category'] : 0;
$progress = isset($_POST['progress']) ? $_POST['progress'] : 0;

$current = isset($_POST['current']) ? $_POST['current'] : 1;
//    $pageCount = isset($_POST['pageCount']) ? $_POST['pageCount']:1;
$recordPerPage = isset($_POST['recordPerPage']) ? $_POST['recordPerPage'] : 1;
$txtSearch = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : null;

$sql_teacher = "select id, name, mdl_role_id, status from roles where name = 'teacher'";
$teacher = $DB->get_record_sql($sql_teacher);
$teacher_role_id = $teacher->mdl_role_id ? $teacher->mdl_role_id : 4;
$pageRequest = isset($_POST['pageRequest']) ? $_POST['pageRequest'] : '';

if ($progress == 1) { //List from home

    $courses_current = array();
    $courses_required = array();
    $courses_completed = array();
    $courses_others = array();
    $courses_others_id = '(0';

    $sql = 'select @s:=@s+1 stt,
mc.id,
mc.fullname,
mc.category,
mc.course_avatar,
mc.estimate_duration,
( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections,
 ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = mc.id) as numofmodule,
  ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate <> 0 and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned,
    muet.userid as teacher_id,
    tud.fullname as teacher_name,
    tor.name as teacher_organization,
    muet.timecreated as teacher_created,
    toe.position as teacher_position,
    toe.description as teacher_description,
    ttp.name as training_name,
    ttc.order_no,
    ttp.id as training_id,
    ttp.deleted as training_deleted,
    GROUP_CONCAT(CONCAT(tud.fullname, \' created_at \',  muet.timecreated)) as teachers
  from mdl_course mc
  inner join mdl_enrol me on mc.id = me.courseid AND me.roleid = 5
  inner join mdl_user_enrolments mue on me.id = mue.enrolid
  left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . '
  left join mdl_user_enrolments muet on met.id = muet.enrolid
  left join tms_user_detail tud on tud.user_id = muet.userid
  left join tms_organization_employee toe on toe.user_id = muet.userid
  left join tms_trainning_courses ttc on mc.id = ttc.course_id
  left join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
  left join tms_organization tor on tor.id = toe.organization_id, (SELECT @s:= 0) AS s
  where me.enrol = \'manual\'
  and mc.deleted = 0
  and mc.visible = 1
  and mc.category NOT IN (2,7)
  and ttc.deleted <> 1
  and mue.userid = ' . $USER->id;

    if ($category > 0) {
        $sql .= ' and mc.category = ' . $category;
    }
    if ($txtSearch) {
        $sql .= ' and mc.fullname like N\'%' . $txtSearch . '%\'';
    }

    $sql .= ' group by mc.id ORDER BY ttp.deleted, ttp.id, ttc.order_no'; //cần để tạo tên giáo viên

    $courses = array_values($DB->get_records_sql($sql));

    $competency_exists = array();
    $courses_training = array();

    foreach ($courses as $course) {
        $courses_training[$course->training_id][$course->id] = $course;
    }

    foreach ($courses_training as $courses) {
        $stt = 1;
        foreach ($courses as &$course) {
            $course->sttShow = $stt;
            //current first
            if ($course->numofmodule > 0 && $course->numoflearned / $course->numofmodule > 0 && $course->numoflearned / $course->numofmodule < 1) {
                $courses_others_id .= ', ' . $course->id;
                array_push($competency_exists, $course->training_id);
                push_course($courses_current, $course);
            } //then complete
            elseif ($course->numoflearned / $course->numofmodule == 1) {
                push_course($courses_completed, $course);
                $courses_others_id .= ', ' . $course->id;
            } //then required = khoa hoc trong khung nang luc
            elseif ($course->training_name && ($course->training_deleted == 0 || $course->training_deleted == 2)) {
                $courses_required[$course->training_id][$course->id] = $course;
                $courses_required[$course->training_id] = array_values($courses_required[$course->training_id]);
                if ($course->training_deleted == 2) {
                    $courses_others_id .= ', ' . $course->id;
                }
//            push_course($courses_required, $course);
            }
            $stt++;
        }
    }

    $courses_others_id .= ')';

    //get course can not enrol
    $sqlCourseNotEnrol = 'select mc.id,
mc.fullname,
mc.category,
mc.course_avatar,
mc.estimate_duration,
muet.userid as teacher_id,
tud.fullname as teacher_name,
toe.position as teacher_position,
tor.name as teacher_organization,
ttp.id as training_id,
muet.timecreated as teacher_created
from mdl_course mc
inner join tms_trainning_courses ttc on mc.id = ttc.course_id
  left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . '
  left join mdl_user_enrolments muet on met.id = muet.enrolid
left join tms_user_detail tud on tud.user_id = muet.userid
  left join tms_organization_employee toe on toe.user_id = muet.userid
  left join tms_organization tor on tor.id = toe.organization_id
  inner join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
  and ttp.deleted = 2 and mc.deleted = 0 and
  mc.id not in ' . $courses_others_id;

    if($category == 'other'){
        if ($txtSearch) {
            $sqlCourseNotEnrol .= ' and mc.fullname like N\'%' . $txtSearch . '%\'';
        }
    }

    $coursesSuggest = array_values($DB->get_records_sql($sqlCourseNotEnrol));
    //

    $course_list = array();
    //
    $courses_required_sort = [];

    if ($category == 'current') {
        $all_courses = $courses_current;
    } elseif ($category == 'required') {
        foreach ($courses_required as $training_courses) {
            $sttNew = 1;
            foreach ($training_courses as $course) {
                $newCourse = $course;
                if ($course->training_deleted == 2)
                    $newCourse->sttShow = 99999;
                else
                    $newCourse->sttShow = $sttNew;
                $courses_required_sort[] = $newCourse;
                $sttNew++;
            }
        }
        usort($courses_required_sort, 'cmp_stt');

        $all_courses = array_values($courses_required_sort);
    } elseif ($category == 'completed') {
        $all_courses = $courses_completed;
    } else {
        $all_courses = $coursesSuggest;
    }

    $start_index = $current * $recordPerPage - $recordPerPage;

    $course_list = array_slice($all_courses, $start_index, $recordPerPage);

    $total = count($all_courses);

    $response = json_encode(['courses' => $course_list, 'totalPage' => ceil($total / $recordPerPage), 'totalRecords' => $total, 'competency_exists' => $competency_exists, 'coursesSuggest' => $coursesSuggest]);

} else {
    //course available
    //count total
    $sqlCountCoures = 'select mc.id
from mdl_course mc
inner join mdl_enrol me on mc.id = me.courseid AND me.roleid = 5
inner join mdl_user_enrolments mue on me.id = mue.enrolid
inner join tms_trainning_courses ttc on mc.id = ttc.course_id
where me.enrol = \'manual\'
and mc.deleted = 0
and mc.visible = 1
and mc.category NOT IN (2,7)
and ttc.deleted <> 1
and mue.userid = ' . $USER->id;
    if ($category > 0) {
        $sqlCountCoures .= ' and category = ' . $category;
    }

    $total = count(array_values($DB->get_records_sql($sqlCountCoures)));

    $sqlGetCoures = 'select
mc.id,
mc.fullname,
mc.category,
SUBSTR(mc.course_avatar, 2) as course_avatar,
mc.estimate_duration,
( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections,
( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = mc.id) as numofmodule,
( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate <> 0 and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned,
FLOOR(mccc.gradepass) as pass_score,
(select mgg.finalgrade from mdl_grade_items mgi join mdl_grade_grades mgg on mgg.itemid = mgi.id where mgg.userid=mue.userid and mgi.courseid=mc.id group by mgi.courseid) as finalgrade,
ttp.name as training_name,
ttc.order_no,
ttp.id as training_id,
ttp.deleted as training_deleted,
GROUP_CONCAT(CONCAT(tud.fullname, \' created_at \',  muet.timecreated)) as teachers

from mdl_course mc
inner join mdl_enrol me on mc.id = me.courseid AND me.roleid = 5
inner join mdl_user_enrolments mue on me.id = mue.enrolid
left JOIN mdl_course_completion_criteria mccc on mccc.course = mc.id
left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . '
left join mdl_user_enrolments muet on met.id = muet.enrolid
left join tms_user_detail tud on tud.user_id = muet.userid
left join tms_organization_employee toe on toe.user_id = muet.userid
left join tms_trainning_courses ttc on mc.id = ttc.course_id
left join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
left join tms_organization tor on tor.id = toe.organization_id

where me.enrol = \'manual\'
and mc.deleted = 0
and mc.visible = 1
and mc.category NOT IN (2,7)
and ttc.deleted <> 1
and mue.userid = ' . $USER->id;


    if ($category > 0) {
        $sqlGetCoures .= ' and mc.category = ' . $category;
    }
    if ($txtSearch) {
        $sqlGetCoures .= ' and mc.fullname like N\'%' . $txtSearch . '%\'';
    }

    $sqlGetCoures .= ' group by mc.id ORDER BY ttp.id, ttc.order_no'; //cần để tạo tên giáo viên
//    $start_index = $current * $recordPerPage - $recordPerPage;
//    $sqlGetCoures .= ' limit ' . $recordPerPage . ' offset ' . $start_index;
    $courses = array_values($DB->get_records_sql($sqlGetCoures));

    $stt_count = 1;
    $competency_exists = array();
    $temp_competency_exists = array();

    //
    $tempCourse = [];
    //
    $courses_training = array();

    foreach ($courses as $course) {
        $courses_training[$course->training_id][$course->id] = $course;
    }

    $coures_result = array();
    foreach ($courses_training as $courses) {
        $stt = 1;
        foreach ($courses as &$course) {
            $course->sttShow = $stt;
            $teachers = $course->teachers;
            $teacher_name = '';
            $teacher_created = 0;
            //
            $course->enable = 'enable';
            $course->category_type = '';
            if (strlen($teachers) != 0) {
                $teachers_and_created = explode(',', $teachers);
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
            //
            if ($course->numoflearned / $course->numofmodule > 0 && $course->numoflearned / $course->numofmodule < 1) {
                $course->order_learn = 2;
                array_push($competency_exists, $course->training_id);
            }
            elseif (($course->training_name && $course->training_deleted == 0) && (($course->numoflearned == 0) || ($course->numofmodule == 0))) {
                $course->order_learn = 1;
                $course->category_type = 'required';
                array_push($temp_competency_exists, $course->training_id);
                //
                if (in_array($course->training_id, $temp_competency_exists)) {
                    $stt_count = $tempCourse[$course->training_id]['stt'];
                    $stt_count++;
                } else {
                    $stt_count = 1;
                }
                $tempCourse[$course->training_id]['stt'] = $stt_count;
                $course->stt_count = $tempCourse[$course->training_id]['stt'];
            }
            elseif($course->numoflearned / $course->numofmodule == 1){
                $course->order_learn = 0;
            }
            $stt++;
            $coures_result[] = $course;
        }
    }

    if($pageRequest == 'profile'){
        usort($coures_result, 'cmp_order_learn');
    }

    $start_index = $current * $recordPerPage - $recordPerPage;

    $course_list = array_slice($coures_result, $start_index, $recordPerPage);

    $total = count($coures_result);

    $response = json_encode(['courses' => $course_list, 'totalPage' => ceil($total / $recordPerPage), 'totalRecords' => $total, 'competency_exists' => $competency_exists]);
}

function push_course(&$array, $course)
{
    if (array_key_exists($course->id, $array)) {//đã có, check created date mới nhất thì overwwrite
        $old_created = $array[$course->id]->teacher_created;
        if ($course->teacher_created > intval($old_created)) {
            $array[$course->id] = $course;
        }
    } else {//mới
        $array[$course->id] = $course;
    }
}

function cmp($a, $b)
{
    if ($a->training_deleted == $b->training_deleted) return 0;
    return ($a->training_deleted < $b->training_deleted) ? -1 : 1;
//    return strcmp($a->training_deleted, $b->training_deleted);
}

function cmp_training_id($a, $b)
{
    return strcmp($a->training_id, $b->training_id);
}

function cmp_order_learn($a, $b)
{
//    return strcmp($a->order_learn, $b->order_learn);
    if ($a->order_learn == $b->order_learn) return 0;
    return ($a->order_learn < $b->order_learn) ? 1 : -1;
}

function cmp_stt($a, $b)
{
    if ($a->sttShow == $b->sttShow) return 0;
    return ($a->sttShow < $b->sttShow) ? -1 : 1;
}

echo $response;
