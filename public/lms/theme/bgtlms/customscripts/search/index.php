<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Global Search index page for entering queries and display of results
 *
 * @package   core_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');
//require_once(__DIR__ . '/../../search/classes/output/form/mysearch.php');
require_once(__DIR__ . '/searchlib.php');

$page = optional_param('page', 0, PARAM_INT);
$q = optional_param('q', '', PARAM_NOTAGS);
$title = optional_param('title', '', PARAM_NOTAGS);
$contextid = optional_param('context', 0, PARAM_INT);
$cat = optional_param('cat', '', PARAM_NOTAGS);
$mycoursesonly = optional_param('mycoursesonly', 0, PARAM_INT);

if (\core_search\manager::is_search_area_categories_enabled()) {
    $cat = \core_search\manager::get_search_area_category_by_name($cat);
}


// Moving areaids, courseids, timestart, and timeend further down as they might come as an array if they come from the form.

$context = context_system::instance();
$pagetitle = get_string('globalsearch', 'search');
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

if (!empty($CFG->forcelogin)) {
    require_login();
}

require_capability('moodle/search:query', $context);

$searchrenderer = $PAGE->get_renderer('core_search');

if (\core_search\manager::is_global_search_enabled() === false) {
    $PAGE->set_url(new moodle_url('/search/index.php'));
    echo $OUTPUT->header();
    echo $OUTPUT->heading($pagetitle);
    echo $searchrenderer->render_search_disabled();
    echo $OUTPUT->footer();
    exit;
}

$search = \core_search\manager::instance(true);

// Set up custom data for form.
$customdata = ['searchengine' => $search->get_engine()->get_plugin_name()];
if ($contextid) {
    // When a context is supplied, check if it's within course level. If so, show dropdown.
    $context = context::instance_by_id($contextid);
    $coursecontext = $context->get_course_context(false);
    if ($coursecontext) {
        $searchwithin = [];
        $searchwithin[''] = get_string('everywhere', 'search');
        $searchwithin['course'] = $coursecontext->get_context_name();
        if ($context->contextlevel != CONTEXT_COURSE) {
            $searchwithin['context'] = $context->get_context_name();
            if ($context->contextlevel == CONTEXT_MODULE) {
                $customdata['withincmid'] = $context->instanceid;
            }
        }
        $customdata['searchwithin'] = $searchwithin;
        $customdata['withincourseid'] = $coursecontext->instanceid;
    }

}
// Get available ordering options from search engine.
$customdata['orderoptions'] = $search->get_engine()->get_supported_orders($context);

if ($cat instanceof \core_search\area_category) {
    $customdata['cat'] = $cat->get_name();
}


//$mform = new \core_search\output\form\mysearch(null, $customdata);
$mform = new \core_search\output\form\search(null, $customdata);

/*Get areas data*/
$area_selection = \theme_bgtlms\customscripts\search\searchlib::getAreas($customdata['cat']);

/*Get course data*/
$course_selection = \theme_bgtlms\customscripts\search\searchlib::getCourses();

/*Overwrite form data*/
$areaids = optional_param('areaids', '', PARAM_RAW);
//echo json_encode($area_ids);die;


$data = $mform->get_data();

if (!$data && $q) {
    // Data can also come from the URL.
    $data = new stdClass();
    $data->q = $q;
    $data->title = $title;
    /*
    $areaids = optional_param('areaids', '', PARAM_RAW);
    if (!empty($areaids)) {
        $areaids = explode(',', $areaids);
        $data->areaids = clean_param_array($areaids, PARAM_ALPHANUMEXT);
    }
    $courseids = optional_param('courseids', '', PARAM_RAW);
    if (!empty($courseids)) {
        $courseids = explode(',', $courseids);
        $data->courseids = clean_param_array($courseids, PARAM_INT);
    }
    */

    function cleanEmptyValue(&$array) {
        foreach ($array as $key => $item) {
            if ($item == '') {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /*Map query params and pass to custom form*/
    $area_ids = $_REQUEST['areaids'];
    $area_ids = array_filter($area_ids);

    if (!empty($areaids)) {
        $data->areaids = clean_param_array($areaids, PARAM_ALPHANUMEXT);
    }

    $course_ids = $_REQUEST['courseids'];
    $course_ids = array_filter($course_ids);
    if (!empty($course_ids)) {
        $data->courseids = clean_param_array($course_ids, PARAM_INT);
    }

    //$data->timestart = optional_param('timestart', 0, PARAM_INT);
    //$data->timeend = optional_param('timeend', 0, PARAM_INT);

    if ($_REQUEST['timestart_custom'] && strlen($_REQUEST['timestart_custom']) != 0) {
        $timestart_full = $_REQUEST['timestart_custom'];
        $timestart = strtotime($timestart_full. ":00");
        $data->timestart = $timestart;
    }

    if ($_REQUEST['timeend_custom'] && strlen($_REQUEST['timeend_custom']) != 0) {
        $timeend_full = $_REQUEST['timeend_custom'];
        $timeend = strtotime($timeend_full. ":00");
        $data->timeend = $timeend;
    }

    $data->context = $contextid;
    $data->mycoursesonly = $mycoursesonly;
    $mform->set_data($data);
}

//Check params 'q' required
$missing_q = 0;
if (strlen($_REQUEST['cat']) != 0 && strlen($q) == 0) { //Có search nhưng không truyền params 'q'
    $missing_q = 1;
}

// Convert the 'search within' option, if used, to course or context restrictions.
if ($data && !empty($data->searchwithin)) {
    switch ($data->searchwithin) {
        case 'course':
            $data->courseids = [$coursecontext->instanceid];
            break;
        case 'context':
            $data->courseids = [$coursecontext->instanceid];
            $data->contextids = [$context->id];
            break;
    }
}

// Inform search engine about source context.
if (!empty($context) && $data) {
    $data->context = $context;
}

if ($data && $cat instanceof \core_search\area_category) {
    $data->cat = $cat->get_name();
}

// Set the page URL.
$urlparams = array('page' => $page);
if ($data) {
    $urlparams['q'] = $data->q;
    $urlparams['title'] = $data->title;
    if (!empty($data->areaids)) {
        $urlparams['areaids'] = implode(',', $data->areaids);
    }
    if (!empty($data->courseids)) {
        $urlparams['courseids'] = implode(',', $data->courseids);
    }
    $urlparams['timestart'] = $data->timestart;
    $urlparams['timeend'] = $data->timeend;
    $urlparams['mycoursesonly'] = isset($data->mycoursesonly) ? $data->mycoursesonly : 0;
}


if ($cat instanceof \core_search\area_category) {
    $urlparams['cat'] = $cat->get_name();
}

$url = new moodle_url('/search/index.php', $urlparams);
$PAGE->set_url($url);

// We are ready to render.
echo $OUTPUT->header();
?>
<style>
    .hide {
        display: none;
    }
    .span-error {
        border-color: #dc3545;
    }
    .id-q-icon-error {
        color: #dc3545;
    }
</style>
<?php
//echo $OUTPUT->heading($pagetitle); //Ẩn title

// Get the results.
if ($data) {
    $results = $search->paged_search($data, $page);
}

if ($errorstr = $search->get_engine()->get_query_error()) {
    echo $OUTPUT->notification(get_string('queryerror', 'search', $errorstr), 'notifyproblem');
} else if (empty($results->totalcount) && !empty($data)) {
    echo $OUTPUT->notification(get_string('noresults', 'search'), 'notifymessage');
}

?>
<div class="row">
    <div class="col-4 search-filter-block">
        <?php // $mform->display(); ?>
        <form>
            <h4>Search</h4>
            <div class="row">
                <div class="col-12">
                    <div class="input-group">
                        <input class="form-control search-input py-2 border-right-0" type="text" name="q" id="id_q" value="<?= $q ?>">
                        <span class="input-group-append">
                            <div class="input-group-text bg-transparent" id="id-q-append">
                                <i class="fa fa-search" id="id-q-icon"></i>
                            </div>
                        </span>
                    </div>
                    <small id="error-missing-q" class="text-danger hide">Search query is required</small>
                </div>
            </div>
            <h4 class="mt-3">Filter</h4>
            <div class="row">
                <div class="col-3 col-title-container">
                    <label class="btn search_label_button">Title</label>
                </div>
                <div class="col-9">
                    <div class="input-group">
                        <input class="form-control" type="text" name="title" id="id_title" value="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3 col-title-container">
                    <label class="btn search_label_button">Search area</label>
                </div>
                <div class="col-9">
                    <!--http://slimselectjs.com-->
                    <!--https://www.cssscript.com/multi-select-dropdown-component-javascript-slim-select/-->
                    <select multiple name="areaids[]" id="id_areaids">
                        <option value="" selected><?= get_string('allareas', 'search') ?></option>
                        <?php foreach ($area_selection as $area_id => $area_name) { ?>
                            <option value="<?= $area_id ?>"><?= $area_name ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-3 col-title-container">
                    <label class="btn search_label_button">Course</label>
                </div>
                <div class="col-9">
                    <select multiple name="courseids[]" id="id_courseids">
                        <option value="" selected><?= get_string('allcourses', 'search') ?></option>
                        <?php foreach ($course_selection as $course_id => $course_name) { ?>
                            <option value="<?= $course_id ?>"><?= $course_name ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-title-container">
                    <label class="btn search_label_button">Modified before</label>
                </div>
                <div class="col-6">
                    <!--https://www.malot.fr/bootstrap-datetimepicker/-->
                    <input size="16" type="text" name="timestart_custom" readonly class="form_datetime form-control" placeholder="yyyy/mm/dd hh:mm">
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-title-container">
                    <label class="btn search_label_button">Modified after</label>
                </div>
                <div class="col-6">
                    <input size="16" type="text"  name="timeend_custom" readonly class="form_datetime form-control" placeholder="yyyy/mm/dd hh:mm">
                </div>
            </div>
            <input type="hidden" id="cat" name="cat" value="core-all">
            <input type="hidden" id="mform_isexpanded_id_filtersection" name="mform_isexpanded_id_filtersection" value="1">
            <input type="submit" class="btn btn-md btn-bgtlms" name="submitbutton" id="id_submitbutton" value="Search">
            <button type="button" class="btn btn-md btn-bgtlms-clear" onclick="resetForm()">CLEAR</button>
        </form>
    </div>
    <div class="col-8 search-result-block">
        <h2>Search result</h2>
        <?php if (!empty($results)) {
            echo $searchrenderer->render_results($results->results, $results->actualpage, $results->totalcount, $url, $cat);

            \core_search\manager::trigger_search_results_viewed([
                'q' => $data->q,
                'page' => $page,
                'title' => $data->title,
                'areaids' => !empty($data->areaids) ? $data->areaids : array(),
                'courseids' => !empty($data->courseids) ? $data->courseids : array(),
                'timestart' => isset($data->timestart) ? $data->timestart : 0,
                'timeend' => isset($data->timeend) ? $data->timeend : 0
            ]);

        } else {
            echo "No result found";
        }?>
    </div>
</div>

<script>

    $(document).ready(function() {
        let missing_q_text = $("#error-missing-q");
        let q_input = $("#id_q");
        let q_input_append = $("#id-q-append");
        let q_input_icon = $("#id-q-icon");

    <?php if ($missing_q == 1) {  ?>
            missing_q_text.removeClass("hide");
            q_input.addClass('is-invalid');
            q_input_append.addClass('span-error');
            q_input_icon.addClass('id-q-icon-error');
        <?php } ?>

        if(q_input.val() && !missing_q_text.hasClass("hide")) { //Có giá trị thì remove error text
            missing_q_text.addClass("hide");
            q_input.removeClass('is-invalid');
            q_input_append.removeClass('span-error');
            q_input_icon.removeClass('id-q-icon-error');
        }
        q_input.on('input', function() {
            if(q_input.val() && !missing_q_text.hasClass("hide")) { //Có giá trị thì remove error text
                missing_q_text.addClass("hide");
                q_input.removeClass('is-invalid');
                q_input_append.removeClass('span-error');
                q_input_icon.removeClass('id-q-icon-error');
            }
        });
    });

    let selectArea = new SlimSelect({
        select: '#id_areaids',
        placeholder: '<?=  get_string('allareas', 'search')  ?>',
        //deselectLabel: '✖',
        onChange: (info) => {
            //hideSelectedAll('area');
            let current_info_length = info.length;
            if (info.length > 1) { //Có chọn từ 2 option trở lên
                info = info.filter(obj => obj.value !== '');
                if (current_info_length !== info.length) { //có chọn select all
                    hideSelectedAll('area'); //remove select all
                }
            } else { //nếu chọn mỗi select all, show lại
                let select = info.filter(obj => obj.value === '');
                if (select.length !== 0) { //có chọn select all
                    showSelectedAll('area'); //show select all
                }
            }
        },
    });

    let selectCourse = new SlimSelect({
        select: '#id_courseids',
        placeholder: '<?=  get_string('allcourses', 'search')  ?>',
        //deselectLabel: '<span class="red">✖</span>',
        onChange: (info) => {
            let current_info_length = info.length;
            if (info.length > 1) { //Có chọn từ 2 option trở lên
                info = info.filter(obj => obj.value !== '');
                if (current_info_length !== info.length) { //có chọn select all
                    hideSelectedAll('course'); //remove select all
                }
            } else { //nếu chọn mỗi select all, show lại
                let select = info.filter(obj => obj.value === '');
                if (select.length !== 0) { //có chọn select all
                    showSelectedAll('course'); //show select all
                }
            }
        }
    });

    function resetForm() {
        //console.log(selectArea.selected());
        selectArea.set(['']);
        selectCourse.set(['']);
        $(".form_datetime").val("");
        $("#id_title").val("");
        $("#id_q").val("");
    }

    function hideSelectedAll(type) {
        if (type === 'area') {
            let x = $("span:contains('<?=  get_string('allareas', 'search')  ?>')");
            x.closest("div").css({
                "display": "none"
            });
        }
        if (type === 'course') {
            let x = $("span:contains('<?=  get_string('allcourses', 'search')  ?>')");
            x.closest("div").css({
                "display": "none"
            });
        }
    }

    function showSelectedAll(type) {
        if (type === 'area') {
            let x = $("span:contains('<?=  get_string('allareas', 'search')  ?>')");
            x.closest("div").css({
                "display": "block"
            });
        }
        if (type === 'course') {
            let x = $("span:contains('<?=  get_string('allcourses', 'search')  ?>')");
            x.closest("div").css({
                "display": "block"
            });
        }
    }

    $(".form_datetime").datetimepicker({format: 'yyyy/mm/dd hh:ii'});

</script>
<?php
echo $OUTPUT->footer();
die;
