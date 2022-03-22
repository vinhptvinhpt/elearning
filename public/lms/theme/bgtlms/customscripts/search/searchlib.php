<?php
namespace theme_bgtlms\customscripts\search;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/externallib.php');

use core_search\manager;

class searchlib {
    public static function getAreas($customdata_cat) {
        $search = \core_search\manager::instance(true);
        $enabledsearchareas = \core_search\manager::get_search_areas_list(true);
        $areanames = array();

        if (\core_search\manager::is_search_area_categories_enabled() && !empty($customdata_cat)) {
            $searchareacategory = \core_search\manager::get_search_area_category_by_name($customdata_cat);
            $searchareas = $searchareacategory->get_areas();
            foreach ($searchareas as $areaid => $searcharea) {
                if (key_exists($areaid, $enabledsearchareas)) {
                    $areanames[$areaid] = $searcharea->get_visible_name();
                }
            }
        } else {
            foreach ($enabledsearchareas as $areaid => $searcharea) {
                $areanames[$areaid] = $searcharea->get_visible_name();
            }
        }

        // Sort the array by the text.
        \core_collator::asort($areanames);
        return $areanames;
    }

    public static function getCourses() {
        global $DB;
        global $USER;

        //Check permission
        $sqlCheck = 'SELECT permission_slug, roles.name from `model_has_roles` as `mhr`
inner join `roles` on `roles`.`id` = `mhr`.`role_id`
left join `permission_slug_role` as `psr` on `psr`.`role_id` = `mhr`.`role_id`
inner join `mdl_user` as `mu` on `mu`.`id` = `mhr`.`model_id`
where `mhr`.`model_id` = ' . $USER->id . ' and `mhr`.`model_type` = "App/MdlUser"';

        $check = $DB->get_records_sql($sqlCheck);
        $permissions = array_values($check);
        $category_condition = "mc.category <> 2";
        $editor = false;
        foreach ($permissions as $permission) {

            if (in_array($permission->name, ['root', 'admin'])) { //Nếu admin => full quyền, k cần thêm điều kiện category
                $editor = true;
                break;
            }

            if ($permission == 'tms-educate-libraly-edit') {
                $category_condition .= " AND mc.cate = 3";
                $editor = true;
            }
            if ($permission == 'tms-educate-exam-offline-edit' && $course_category = 5) {
                $category_condition .= " AND mc.cate = 5";
                $editor = true;
            }
            if ($permission == 'tms-educate-exam-online-edit') {
                $category_condition .= " AND mc.cate <> 3 AND mc.cate <> 5";
                $editor = true;
            }
        }

        if ($editor) {
            $sqlGetCoures = "select mc.id,
                        mc.fullname
                        from mdl_course mc
                        where mc.deleted = 0 and mc.visible = 1 and $category_condition";
        } else {
            $sqlGetCoures = "select mc.id,
            mc.fullname,
            mc.category,
            SUBSTR(mc.course_avatar, 2) as course_avatar,
            mc.estimate_duration,
            ( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections,
            ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = mc.id) as numofmodule,
            ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate in (1,2) and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned,
            FLOOR(mccc.gradepass) as pass_score,
            (select mgg.finalgrade from mdl_grade_items mgi join mdl_grade_grades mgg on mgg.itemid = mgi.id where mgg.userid=mue.userid and mgi.courseid=mc.id group by mgi.courseid) as finalgrade
            from mdl_course mc
            inner join mdl_enrol me on mc.id = me.courseid
            inner join mdl_user_enrolments mue on me.id = mue.enrolid
            left JOIN mdl_course_completion_criteria mccc on mccc.course = mc.id
            where me.enrol = 'manual' and mc.deleted = 0 and mc.visible = 1 and mc.category <> 2 and mue.userid = $USER->id";
        }

        $courses = array_values($DB->get_records_sql($sqlGetCoures));

        $courses_data = array();

        foreach ($courses as $course) {
            $courses_data[$course->id] = $course->fullname;
        }

        \core_collator::asort($courses_data);
        return $courses_data;
    }
}
