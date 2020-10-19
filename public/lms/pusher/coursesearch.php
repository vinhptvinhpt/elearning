<?php
require_once('../config.php');

$category = isset($_POST['category']) ? $_POST['category'] : 0;
$progress = isset($_POST['progress']) ? $_POST['progress'] : 0;

$current = isset($_POST['current']) ? $_POST['current'] : 1;
//    $pageCount = isset($_POST['pageCount']) ? $_POST['pageCount']:1;
$recordPerPage = isset($_POST['recordPerPage']) ? $_POST['recordPerPage'] : 1;
$txtSearch = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : null;
//trim text search
$txtSearch = trim($txtSearch);

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
    $student_role_id = 5;

    $sql_training = 'select @s:=@s+1 stt,
mc.id,
mc.fullname,
mc.category,
mc.course_avatar,
mc.estimate_duration,
( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections,
 ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 AND cm.completion <> 0  and cm.course = mc.id) as numofmodule,
  ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 AND cm.completion <> 0  and cmc.completionstate in (1, 2) and cm.course = mc.id and cmc.userid = tud.user_id) as numoflearned,
    muet.userid as teacher_id,
    tudt.fullname as teacher_name,
    tor.name as teacher_organization,
    muet.timecreated as teacher_created,
    toe.position as teacher_position,
    toe.description as teacher_description,
    ttp.id as training_id,
    ttp.name as training_name,
    ttp.deleted as training_deleted,
    ttc.order_no,
    GROUP_CONCAT(CONCAT(tudt.fullname, " created_at ",  muet.timecreated)) as teachers


  from tms_user_detail tud

  inner join tms_traninning_users ttu on ttu.user_id = tud.user_id
  inner join tms_trainning_courses ttc on ttu.trainning_id = ttc.trainning_id
  inner join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
  inner join mdl_course mc on ttc.course_id = mc.id


  left join mdl_enrol me on mc.id = me.courseid AND me.roleid = ' . $student_role_id . '
  left join mdl_user_enrolments mue on me.id = mue.enrolid AND mue.userid = '. $USER->id . '

  left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . '
  left join mdl_user_enrolments muet on met.id = muet.enrolid
  left join tms_user_detail tudt on tudt.user_id = muet.userid

  left join tms_organization_employee toe on toe.user_id = muet.userid
  left join tms_organization tor on tor.id = toe.organization_id, (SELECT @s:= 0) AS s

  where mc.deleted = 0
  and mc.visible = 1
  and mc.category NOT IN (2,7)
  and ttc.deleted <> 1
  and ttp.style NOT IN (2)
  and mue.id IS NOT NULL
  and tud.user_id = ' . $USER->id;


    if ($txtSearch) {
        $sql_training .= ' and mc.fullname like N\'%' . $txtSearch . '%\'';
    }

    $sql_training .= ' group by mc.id'; //cần để tạo tên giáo viên => remove when use union

    //$sql_training .= ' ORDER BY ttp.deleted, ttp.id, ttc.order_no'; // Remove when union

    //Lấy khóa học pqdl
    $sql_pqdl = 'select @s:=@s+1 stt,
mc.id,
mc.fullname,
mc.category,
mc.course_avatar,
mc.estimate_duration,
( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections,
 ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 AND cm.completion <> 0 and cm.course = mc.id) as numofmodule,
  ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate in (1, 2) AND cm.completion <> 0 and  cm.course = mc.id and cmc.userid = tud.user_id) as numoflearned,
    muet.userid as teacher_id,
    tudt.fullname as teacher_name,
    tort.name as teacher_organization,
    muet.timecreated as teacher_created,
    toet.position as teacher_position,
    toet.description as teacher_description,
    ttp.id as training_id,
    ttp.name as training_name,
    ttp.deleted as training_deleted,
    ttc.order_no,
    GROUP_CONCAT(CONCAT(tudt.fullname, " created_at ",  muet.timecreated)) as teachers

  from tms_user_detail tud

  inner join tms_organization_employee toe on toe.user_id = tud.user_id
  inner join tms_role_organization tro on tro.organization_id = toe.organization_id
  inner join tms_role_course trc on tro.role_id = trc.role_id
  inner join mdl_course mc on trc.course_id = mc.id

  left join tms_trainning_courses ttc on ttc.course_id = mc.id
  left join tms_traninning_programs ttp on ttc.trainning_id = ttp.id

  left join mdl_enrol me on mc.id = me.courseid AND me.roleid = ' . $student_role_id . '
  left join mdl_user_enrolments mue on me.id = mue.enrolid AND mue.userid = '. $USER->id . '

  left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . '
  left join mdl_user_enrolments muet on met.id = muet.enrolid
  left join tms_user_detail tudt on tudt.user_id = muet.userid

  left join tms_organization_employee toet on toet.user_id = muet.userid
  left join tms_organization tort on tort.id = toet.organization_id, (SELECT @s:= 0) AS s

  where mc.deleted = 0
  and mc.visible = 1
  and mc.category NOT IN (2,7)
  and ttc.deleted <> 1
  and ttp.style NOT IN (2)
  and mue.id IS NOT NULL
  and tud.user_id = ' . $USER->id;

    $sql_pqdl .= ' group by mc.id'; //cần để tạo tên giáo viên
    $sql_pqdl .= ' ORDER BY ttp.deleted, ttp.id, ttc.order_no';

    $sql = '(' . $sql_training . ') UNION ALL (' . $sql_pqdl . ')';

    $courses = array_values($DB->get_records_sql($sql));

    $competency_exists = array();
    $courses_training = array();
    $getInCourses = array();

    foreach ($courses as $course) {
        $courses_training[$course->training_id][$course->id] = $course;
    }

    foreach ($courses_training as $courses) {
        $stt = 1;
        foreach ($courses as &$course) {
            $course->sttShow = $stt;
//            $course->sttShow = $course->order_no;
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
                //if ($course->training_deleted == 2) {
                    $courses_others_id .= ', ' . $course->id;
                //}
                if (!in_array($course->training_id, $getInCourses)) {
                    array_push($getInCourses, $course->training_id);
                    $course->enable = true;
                } else {
                    $course->enable = false;
                }
//            push_course($courses_required, $course);
            } else {
                $courses_others_id .= ', ' . $course->id;
            }
            $stt++;
        }
    }

    $courses_others_id .= ')';

//Optional courses

//$sqlCourseNotEnrol = 'select mc.id,
//mc.fullname,
//mc.category,
//mc.course_avatar,
//mc.estimate_duration,
//muet.userid as teacher_id,
//tud.fullname as teacher_name,
//toe.position as teacher_position,
//tor.name as teacher_organization,
//ttp.id as training_id,
//muet.timecreated as teacher_created
//from mdl_course mc
//inner join tms_trainning_courses ttc on mc.id = ttc.course_id
//left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . '
//left join mdl_user_enrolments muet on met.id = muet.enrolid
//left join tms_user_detail tud on tud.user_id = muet.userid
//left join tms_organization_employee toe on toe.user_id = muet.userid
//left join tms_organization tor on tor.id = toe.organization_id
//inner join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
//and ttp.deleted = 2
//and mc.deleted = 0
//and mc.visible = 1
//and mc.id not in ' . $courses_others_id;
    $coursesSuggest = [];
    if ($category == 'other') {
        $sqlGetOrganization = 'SELECT T2.id, T2.`name`, T2.`code`, T2.`parent_id`, T2.`level`
        FROM (
            SELECT
                @r AS _id,
                (SELECT @r := parent_id FROM tms_organization WHERE id = _id) AS parent_id,
                @l := @l + 1 AS lvl
            FROM
                (SELECT @r := (select organization_id from tms_organization_employee where user_id= ' . $USER->id . '), @l := 0) vars,
                tms_organization m
            WHERE @r <> 0) T1
        JOIN tms_organization T2
        ON T1._id = T2.id
        ORDER BY T1.lvl DESC';

        $organizations = array_values($DB->get_records_sql($sqlGetOrganization));
        $reverse_recursive_org_ids = [];
        $organization_id = 0;
        $organization_code = '';
        if (!empty($organizations)) {
            foreach ($organizations as $organization_item) {
                $reverse_recursive_org_ids[] = $organization_item->id;
            }
            //level 1
            $organization = $organizations[0];
            $organization_id = $organization->id;
            $organization_code = $organization->code;
        }

        if (!empty($reverse_recursive_org_ids)) {
            $reverse_recursive_org_ids_string = implode(',', $reverse_recursive_org_ids);
            $sqlCourseNotEnrol = '
            select mc.id,
            mc.fullname,
            mc.category,
            mc.course_avatar,
            (
                select
                    count(cm.id) as num
                from
                    mdl_course_modules cm
                inner join mdl_course_sections cs on
                    cm.course = cs.course
                    and cm.section = cs.id
                where
                    cs.section <> 0
                    and cm.completion <> 0
                    and cm.course = mc.id) as numofmodule,
            (
            select
                count(cmc.coursemoduleid) as num
            from
                mdl_course_modules cm
            inner join mdl_course_modules_completion cmc on
                cm.id = cmc.coursemoduleid
            inner join mdl_course_sections cs on
                cm.course = cs.course
                and cm.section = cs.id
            inner join mdl_course c on
                cm.course = c.id
            where
                cs.section <> 0
                and cmc.completionstate in (1,
                2)
                and cm.completion <> 0
                and cm.course = mc.id
                and cmc.userid = '.$USER->id.') as numoflearned,
            mc.estimate_duration,
            muet.userid as teacher_id,
            tud.fullname as teacher_name,
            toe.position as teacher_position,
            tor.name as teacher_organization,
            muet.timecreated as teacher_created,
            ttp.id as training_id
            from mdl_course mc
            inner join tms_trainning_courses ttc on mc.id = ttc.course_id
            left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . '
            left join mdl_user_enrolments muet on met.id = muet.enrolid
            left join tms_user_detail tud on tud.user_id = muet.userid
            left join tms_organization_employee toe on toe.user_id = muet.userid
            left join tms_organization tor on tor.id = toe.organization_id
            inner join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
            where
            mc.deleted = 0
            and mc.category NOT IN (2,7)
            and mc.visible = 1
            and met.enrol = "manual"
            and mc.id IN (select course_id from tms_optional_courses where organization_id IN (' . $reverse_recursive_org_ids_string . '))
            and mc.id NOT IN ' . $courses_others_id;

            if ($txtSearch) {
                $sqlCourseNotEnrol .= ' and mc.fullname like N\'%' . $txtSearch . '%\'';
            }
        }
        $coursesSuggest = array_values($DB->get_records_sql($sqlCourseNotEnrol));
    }


    $course_list = array();

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
//                else
//                    $newCourse->sttShow = $sttNew;
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

    $resultSearch = array();
    if ($txtSearch || $category > 0) {
        foreach ($all_courses as $courseA) {
            if ($txtSearch && $category > 0) {
                if (strpos(strtolower($courseA->fullname), strtolower($txtSearch)) !== false && $courseA->category == $category)
                    $resultSearch[] = $courseA;
            } else if ($txtSearch) {
                if (strpos(strtolower($courseA->fullname), strtolower($txtSearch)) !== false)
                    $resultSearch[] = $courseA;
            } else if ($category > 0) {
                if (strpos($courseA->category, $category) !== false)
                    $resultSearch[] = $courseA;
            }
        }
    } else {
        $resultSearch = $all_courses;
    }

    $start_index = $current * $recordPerPage - $recordPerPage;

    $course_list = array_slice($resultSearch, $start_index, $recordPerPage);

    $total = count($resultSearch);

    $response = json_encode(['courses' => $course_list, 'totalPage' => ceil($total / $recordPerPage), 'totalRecords' => $total, 'competency_exists' => $competency_exists, 'coursesSuggest' => $coursesSuggest]);

} else {
    //course available
    //count total
$sqlCountCoures = 'select mc.id
from mdl_course mc
inner join mdl_enrol me on mc.id = me.courseid AND me.roleid = 5
inner join mdl_user_enrolments mue on me.id = mue.enrolid
inner join tms_trainning_courses ttc on mc.id = ttc.course_id
where me.enrol = "manual"
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
( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 AND cm.completion <> 0  and cm.course = mc.id) as numofmodule,
( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate in (1, 2) AND cm.completion <> 0  and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned,
FLOOR(mccc.gradepass) as pass_score,
(select mgg.finalgrade from mdl_grade_items mgi join mdl_grade_grades mgg on mgg.itemid = mgi.id where mgg.userid=mue.userid and mgi.courseid=mc.id group by mgi.courseid) as finalgrade,
ttp.name as training_name,
ttc.order_no,
ttp.id as training_id,
ttp.deleted as training_deleted,
GROUP_CONCAT(CONCAT(tud.fullname, " created_at ",  muet.timecreated)) as teachers

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

where me.enrol = "manual"
and mc.deleted = 0
and mc.visible = 1
and mc.category NOT IN (2,7)
and ttc.deleted <> 1
and ttp.style not in (2)
and mue.userid = ' . $USER->id;


//    if ($category > 0) {
//        $sqlGetCoures .= ' and mc.category = ' . $category;
//    }
//    if ($txtSearch) {
//        $sqlGetCoures .= ' and mc.fullname like N\'%' . $txtSearch . '%\'';
//    }

    $sqlGetCoures .= ' group by mc.id ORDER BY ttp.id, ttc.order_no'; //cần để tạo tên giáo viên
//    $start_index = $current * $recordPerPage - $recordPerPage;
//    $sqlGetCoures .= ' limit ' . $recordPerPage . ' offset ' . $start_index;
    $courses = array_values($DB->get_records_sql($sqlGetCoures));

    $stt_count = 1;
    $competency_exists = array();
    $temp_competency_exists = array();

    $tempCourse = [];
    $courses_training = array();

    foreach ($courses as $course) {
        $courses_training[$course->training_id][$course->id] = $course;
    }

    $coures_result = array();
    $getInCourses = array();

    foreach ($courses_training as $courses) {
        $stt = 1;
        foreach ($courses as &$course) {
            $course->sttShow = $stt;
//            $course->sttShow = $course->order_no;
            $teachers = $course->teachers;
            $teacher_name = '';
            $teacher_created = 0;
            //
            $course->enable = true;
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
            if ($course->numofmodule > 0 && $course->numoflearned / $course->numofmodule > 0 && $course->numoflearned / $course->numofmodule < 1) {
                $course->order_learn = 2;
                array_push($competency_exists, $course->training_id);
            } elseif (($course->training_name && $course->training_deleted == 0) && (($course->numoflearned == 0) || ($course->numofmodule == 0))) {
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
            } else if ($course->training_deleted == 2 && (($course->numoflearned == 0) || ($course->numofmodule == 0))) {
                $course->order_learn = 1;
            } elseif ($course->numoflearned / $course->numofmodule == 1) {
                $course->order_learn = 0;
            }
            $stt++;
            $coures_result[] = $course;
        }
    }

    if ($pageRequest == 'profile') {
        usort($coures_result, 'cmp_order_learn');
    }

    $resultSearch = array();
    if ($txtSearch || $category > 0) {
        foreach ($coures_result as $courseR) {
            if ($txtSearch && $category > 0) {
                if (strpos(strtolower($courseR->fullname), strtolower($txtSearch)) !== false && $courseR->category == $category)
                    $resultSearch[] = $courseR;
            } else if ($txtSearch) {
                if (strpos(strtolower($courseR->fullname), strtolower($txtSearch)) !== false)
                    $resultSearch[] = $courseR;
            } else if ($category > 0) {
                if (strpos($courseR->category, $category) !== false)
                    $resultSearch[] = $courseR;
            }
        }
    } else {
        $resultSearch = $coures_result;
    }

//    if ($category > 0) {
//        $sqlGetCoures .= ' and mc.category = ' . $category;
//    }
//    $coures_result = (array) $coures_result;
//    if ($txtSearch) {
//        $key = array_search($txtSearch, array_column($coures_result, 'fullname'));
//        $coures_result = $coures_result[$key];
//    }

    $start_index = $current * $recordPerPage - $recordPerPage;

    $course_list = array_slice($resultSearch, $start_index, $recordPerPage);

    $total = count($resultSearch);


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
}

function cmp_training_id($a, $b)
{
    return strcmp($a->training_id, $b->training_id);
}

function cmp_order_learn($a, $b)
{
    if ($a->order_learn == $b->order_learn) return 0;
    return ($a->order_learn < $b->order_learn) ? 1 : -1;
}

function cmp_stt($a, $b)
{
    if ($a->sttShow == $b->sttShow) return 0;
    return ($a->sttShow < $b->sttShow) ? -1 : 1;
}

echo $response;
