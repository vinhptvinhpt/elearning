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
    $coursesSuggest = [];

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
	(select count(id) from mdl_user_enrolments where userid = tud.user_id and enrolid in (select id from mdl_enrol where courseid = mc.id and mdl_enrol.roleid = ' . $student_role_id . ')) as enrol_count,

    muet.userid as teacher_id,
    tudt.fullname as teacher_name,
    tor.name as teacher_organization,
    muet.timecreated as teacher_created,
    toe.position as teacher_position,
    toe.description as teacher_description,

	ttp.id as training_id,
	ttp.name as training_name,
	ttp.deleted as training_deleted,
	ttp.style as training_style,

	ttc.order_no,
	GROUP_CONCAT(CONCAT(tudt.fullname, " created_at ",  muet.timecreated)) as teachers


  from tms_user_detail tud

  inner join tms_traninning_users ttu on ttu.user_id = tud.user_id
  inner join tms_trainning_courses ttc on ttu.trainning_id = ttc.trainning_id
  inner join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
  inner join mdl_course mc on ttc.course_id = mc.id

  left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . ' and met.enrol = "manual"
  left join mdl_user_enrolments muet on met.id = muet.enrolid
  left join tms_user_detail tudt on tudt.user_id = muet.userid
  left join tms_organization_employee toe on toe.user_id = muet.userid
  left join tms_organization tor on tor.id = toe.organization_id

  , (SELECT @s:= 0) AS s

  where ttc.deleted <> 1
  and mc.deleted = 0
  and mc.visible = 1
  and mc.category NOT IN (2,7)
  and ttp.style NOT IN (2)
  and tud.user_id = ' . $USER->id;

    if ($txtSearch) {
        $sql_training .= ' and mc.fullname like N\'%' . $txtSearch . '%\'';
    }

    $sql_training .= ' group by mc.id'; //cần để tạo tên giáo viên => remove when use union

    //Có enrol
    $sql_training .=  ' having enrol_count > 0';
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
	(select count(id) from mdl_user_enrolments where userid = tud.user_id and enrolid in (select id from mdl_enrol where courseid = mc.id and mdl_enrol.roleid = ' . $student_role_id . ')) as enrol_count,

	muet.userid as teacher_id,
    tudt.fullname as teacher_name,
    tort.name as teacher_organization,
    muet.timecreated as teacher_created,
    toet.position as teacher_position,
    toet.description as teacher_description,

	ttp.id as training_id,
	ttp.name as training_name,
	ttp.deleted as training_deleted,
	ttp.style as training_style,

    ttc.order_no,
    GROUP_CONCAT(CONCAT(tudt.fullname, " created_at ",  muet.timecreated)) as teachers

  from tms_user_detail tud

  inner join tms_organization_employee toe on toe.user_id = tud.user_id
  inner join tms_role_organization tro on tro.organization_id = toe.organization_id
  inner join tms_role_course trc on tro.role_id = trc.role_id
  inner join mdl_course mc on trc.course_id = mc.id

  left join tms_trainning_courses ttc on ttc.course_id = mc.id
  left join tms_traninning_programs ttp on ttc.trainning_id = ttp.id

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
  and tud.user_id = ' . $USER->id;

    if ($txtSearch) {
        $sql_pqdl .= ' and mc.fullname like N\'%' . $txtSearch . '%\'';
    }

    $sql_pqdl .= ' group by mc.id'; //cần để tạo tên giáo viên

    //Có enrol
    $sql_pqdl .=  ' having enrol_count > 0';

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
            //current first
            if ($course->numofmodule > 0 && $course->numoflearned / $course->numofmodule > 0 && $course->numoflearned / $course->numofmodule < 1) {
                $courses_others_id .= ', ' . $course->id;
                array_push($competency_exists, $course->training_id);
                push_course($courses_current, $course);
            } //then complete
            elseif ($course->numoflearned / $course->numofmodule == 1) {
                push_course($courses_completed, $course);
                $courses_others_id .= ', ' . $course->id;
            } //then required
            elseif ($course->training_name && ($course->training_deleted == 0 || $course->training_deleted == 2)) {
                $courses_required[$course->training_id][$course->id] = $course;
                $courses_required[$course->training_id] = array_values($courses_required[$course->training_id]);
                $courses_others_id .= ', ' . $course->id;
                if (!in_array($course->training_id, $getInCourses)) {
                    array_push($getInCourses, $course->training_id);
                    $course->enable = true;
                } else {
                    $course->enable = false;
                }
            } else {
                $courses_others_id .= ', ' . $course->id;
            }
            $stt++;
        }
    }

    $courses_others_id .= ')';

//Optional courses
    if ($category == 'other') {
        //Lấy đệ quy tổ chức ngược lên top
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
            $sqlCourseNotEnrol = 'select @s:=@s+1 stt,
            mc.id,
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
             (
            select count(id)
            from mdl_user_enrolments
            where userid = tud.user_id
            and enrolid in (
                select id
                from mdl_enrol
                where courseid = mc.id
                and mdl_enrol.roleid = ' . $student_role_id . '
                )
            ) as enrol_count,

            mc.estimate_duration,

            muet.userid as teacher_id,
            tudt.fullname as teacher_name,
            toet.position as teacher_position,
            tort.name as teacher_organization,
            muet.timecreated as teacher_created,

			ttp.id as training_id,
			ttp.name as training_name,
			ttp.deleted as training_deleted,
			ttp.style as training_style,
            ttc.order_no,
            GROUP_CONCAT(CONCAT(tudt.fullname, " created_at ",  muet.timecreated)) as teachers

            from tms_user_detail tud
            inner join tms_organization_employee toe on toe.user_id = tud.user_id
            inner join tms_optional_courses toc on toe.organization_id = toc.organization_id AND toc.organization_id IN (' . $reverse_recursive_org_ids_string . ')
            inner join mdl_course mc on toc.course_id = mc.id
            left join tms_trainning_courses ttc on mc.id = ttc.course_id
            left join tms_traninning_programs ttp on ttc.trainning_id = ttp.id

			left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . ' AND met.enrol = "manual"
			left join mdl_user_enrolments muet on met.id = muet.enrolid
			left join tms_user_detail tudt on tudt.user_id = muet.userid
			left join tms_organization_employee toet on toet.user_id = muet.userid
			left join tms_organization tort on tort.id = toet.organization_id

			, (SELECT @s:= 0) AS s

            where
            mc.deleted = 0
            and mc.category NOT IN (2,7)
            and mc.visible = 1
			and ttc.deleted <> 1
			and ttp.style <> 2
			and tud.user_id = ' . $USER->id . '
            and mc.id NOT IN ' . $courses_others_id;

            if ($txtSearch) {
                $sqlCourseNotEnrol .= ' and mc.fullname like N\'%' . $txtSearch . '%\'';
            }

            $sqlCourseNotEnrol .= ' group by mc.id'; //cần để tạo tên giáo viên
            $sqlCourseNotEnrol .= ' ORDER BY ttp.id, ttc.order_no';

            $allCoursesSuggest = array_values($DB->get_records_sql($sqlCourseNotEnrol));

            foreach ($allCoursesSuggest as $course_optional) {
                $courses_training_optional[$course_optional->training_id][$course_optional->id] = $course_optional;
            }

            foreach ($courses_training_optional as $training_courses) {
                foreach ($training_courses as &$course_optional_item) {
                    $course_optional_item->sttShow = $stt;
                    //current first
                    if ($course_optional_item->numofmodule > 0
                        && $course_optional_item->numoflearned / $course_optional_item->numofmodule > 0
                        && $course_optional_item->numoflearned / $course_optional_item->numofmodule < 1) {
                        array_push($competency_exists, $course->training_id);
                        push_course($courses_current, $course_optional_item);
                    } //then complete
                    elseif ($course_optional_item->numofmodule > 0
                        && $course_optional_item->numoflearned / $course_optional_item->numofmodule == 1) {
                        array_push($competency_completed, $course_optional_item->training_id);
                        push_course($courses_completed, $course_optional_item);
                    }
                    elseif ($course_optional_item->enrol_count > 0) {
                        //đã enrol nhưng chưa học => required courses
                        push_course($courses_required, $course_optional_item);
                        $sttTotalCourse++;
                        array_push($couresIdAllow, $course_optional_item->id);
                    }
                    //then chua hoc
                    else {
                        $coursesSuggest[] = $course_optional_item;
                    }
                    $stt++;
                }
            }
        }
    }

    $course_list = array();

    $courses_required_sort = [];

    if ($category == 'current') {
        $all_courses = $courses_current;
    } elseif ($category == 'required') {
        foreach ($courses_required as $training_courses) {
            foreach ($training_courses as $course) {
                $newCourse = $course;
                if ($course->training_deleted == 2) {
                    $newCourse->sttShow = 99999;
                }
                $courses_required_sort[] = $newCourse;
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

    $sqlGetCoures .= ' group by mc.id ORDER BY ttp.id, ttc.order_no'; //cần để tạo tên giáo viên
    $courses = array_values($DB->get_records_sql($sqlGetCoures));

    $stt_count = 1;
    $competency_exists = array();
    $temp_competency_exists = array();
    $tempCourse = [];
    $courses_training = array();
    $courses_result = array();
    $getInCourses = array();

    foreach ($courses as $course) {
        $courses_training[$course->training_id][$course->id] = $course;
    }

    foreach ($courses_training as $courses) {
        $stt = 1;
        foreach ($courses as &$course) {

            $course->sttShow = $stt;
            $teachers = $course->teachers;
            $teacher_name = '';
            $teacher_created = 0;
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

            if ($course->numofmodule > 0 && $course->numoflearned / $course->numofmodule > 0 && $course->numoflearned / $course->numofmodule < 1) {
                $course->order_learn = 2;
                array_push($competency_exists, $course->training_id);
            } elseif (($course->training_name && $course->training_deleted == 0) && (($course->numoflearned == 0) || ($course->numofmodule == 0))) {
                $course->order_learn = 1;
                $course->category_type = 'required';
                array_push($temp_competency_exists, $course->training_id);
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
            $courses_result[] = $course;
        }
    }

    if ($pageRequest == 'profile') {
        usort($courses_result, 'cmp_order_learn');
    }
    //Search by keyword
    $resultSearch = array();
    if ($txtSearch || $category > 0) {
        foreach ($courses_result as $courseR) {
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
        $resultSearch = $courses_result;
    }
    //Pagination
    $start_index = $current * $recordPerPage - $recordPerPage;

    $course_list = array_slice($resultSearch, $start_index, $recordPerPage);

    $total = count($resultSearch);


    $response = json_encode(['courses' => $course_list, 'totalPage' => ceil($total / $recordPerPage), 'totalRecords' => $total, 'competency_exists' => $competency_exists]);
}

//Functions

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
