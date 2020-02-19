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
 * Moodle frontpage.
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!file_exists('./config.php')) {
    header('Location: install.php');
    die;
}

require_once('config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/filelib.php');

redirect_if_major_upgrade_required();

$urlparams = array();
if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_MY) && optional_param('redirect', 1, PARAM_BOOL) === 0) {
    $urlparams['redirect'] = 0;
}

$action = optional_param('action', '', PARAM_TEXT);
$PAGE->set_url('/', $urlparams);
$PAGE->set_pagelayout('frontpage');
$PAGE->set_other_editing_capability('moodle/course:update');
$PAGE->set_other_editing_capability('moodle/course:manageactivities');
$PAGE->set_other_editing_capability('moodle/course:activityvisibility');

// Prevent caching of this page to stop confusion when changing page after making AJAX changes.
$PAGE->set_cacheable(false);

require_course_login($SITE);

$hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', context_system::instance());

// If the site is currently under maintenance, then print a message.
if (!empty($CFG->maintenance_enabled) and !$hasmaintenanceaccess) {
    print_maintenance_message();
}

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());

if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect($CFG->wwwroot . '/' . $CFG->admin . '/index.php');
}

// If site registration needs updating, redirect.
\core\hub\registration::registration_reminder('/index.php');

if (get_home_page() != HOMEPAGE_SITE) {
    // Redirect logged-in users to My Moodle overview if required.
    $redirect = optional_param('redirect', 1, PARAM_BOOL);
    if (optional_param('setdefaulthome', false, PARAM_BOOL)) {
        set_user_preference('user_home_page_preference', HOMEPAGE_SITE);
    } else if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_MY) && $redirect === 1) {
        redirect($CFG->wwwroot . '/my/');
    } else if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_USER)) {
        $frontpagenode = $PAGE->settingsnav->find('frontpage', null);
        if ($frontpagenode) {
            $frontpagenode->add(
                get_string('makethismyhome'),
                new moodle_url('/', array('setdefaulthome' => true)),
                navigation_node::TYPE_SETTING
            );
        } else {
            $frontpagenode = $PAGE->settingsnav->add(get_string('frontpagesettings'), null, navigation_node::TYPE_SETTING, null);
            $frontpagenode->force_open();
            $frontpagenode->add(
                get_string('makethismyhome'),
                new moodle_url('/', array('setdefaulthome' => true)),
                navigation_node::TYPE_SETTING
            );
        }
    }
}

// Trigger event.
course_view(context_course::instance(SITEID));

// If the hub plugin is installed then we let it take over the homepage here.
if (file_exists($CFG->dirroot . '/local/hub/lib.php') and get_config('local_hub', 'hubenabled')) {
    require_once($CFG->dirroot . '/local/hub/lib.php');
    $hub = new local_hub();
    $continue = $hub->display_homepage();
    // Function display_homepage() returns true if the hub home page is not displayed
    // ...mostly when search form is not displayed for not logged users.
    if (empty($continue)) {
        exit;
    }
}

$PAGE->set_pagetype('site-index');
$PAGE->set_docs_path('');
$editing = $PAGE->user_is_editing();
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$courserenderer = $PAGE->get_renderer('core', 'course');
$sql_category = "Select id, name from {course_categories} where id > 2";
$list_categories = array_values($DB->get_records_sql($sql_category));

echo $OUTPUT->header();
// echo "
// <div style='
// background-image: url(https://d3njjcbhbojbot.cloudfront.net/api/utilities/v1/imageproxy/https://s3.amazonaws.com/coursera_assets/browse/domain-banner/data_science.png?auto=format%2Ccompress&amp;dpr=1);
// background-size: cover;
// min-height: 220px;
// '></div>";
?>

<?php


if (isloggedin()) {
    ?>
    <script>
        function searchFilterCourse() {
            if ($('.courses .coursebox').length > 0) {
                var category = $('#search_category_course').val();
                var text = $('.searchCourseInput').val();
                if (category == 0) {
                    if (text == '') {
                        $('.courses .coursebox').removeClass('hide');
                    } else {
                        $('.courses .coursebox').addClass('hide');
                        $('.courses .coursebox .coursename a:contains("' + text + '")').parents('.coursebox').removeClass('hide');
                    }
                } else {
                    $('.courses .coursebox').each(function() {
                        var cate = $(this).attr('data-category');
                        if (category == cate) {
                            if (text != '') {
                                $(this).addClass('hide');
                                $('.coursename a:contains("' + text + '")', $(this)).parents('.coursebox').removeClass('hide');
                                //$(this).removeClass('hide');
                            } else {
                                $(this).removeClass('hide');
                            }
                        } else {
                            $(this).addClass('hide');
                        }
                    });
                }
            }
        }
    </script>

    <style>

        .banner-content{
            max-width: 800px;
            position: absolute;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            -moz-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            width: 80%;
        }
        .banner-search {
            background-image: url(/lms/theme/bgtlms/img/business.jpg);
            background-size: cover;
            min-height: 180px;
            position: relative;
            margin-bottom: 20px;
        }

        .search_content {
            display: flex;
        }

        .filter_cate select#search_category_course {
            height: 40px;
            border-radius: 4px 0 0 4px;
            border: 0;
        }

        .search_content .searchCourseInput {
            height: 40px;
            border-radius: 0;
            border: 0;
        }

        .search_content button.searchCourse {
            height: 40px;
            border: 0;
            width: 50px;
            border-radius: 0 4px 4px 0;
        }

        .search_content .filter_cate {
            border-right: 2px solid;
            min-width: 230px;
        }

        .site_home_title {
            color: white;
        }
    </style>

    <!-- Example single danger button -->
    <div class="banner-search">
        <div class="banner-content">
            <h1 class="site_home_title mb-3"><?= get_string('availabelcourselabel') ?></h1>

            <div class="search_content">
                <div class="filter_cate">
                    <select onchange="searchFilterCourse()" class="form-control" id="search_category_course">
                        <option value="0"><?= get_string('allcoursetext') ?></h1></option>
                        <?php foreach ($list_categories as $category) { ?>
                            <option value="<?= $category->id ?>"><?= $category->name ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="input-group">
                    <input type="search" placeholder=<?= get_string('coursenametext') ?> aria-describedby="button-addon5" class="form-control searchCourseInput">
                    <div class="input-group-append">
                        <button id="button-addon5" onclick="searchFilterCourse()" type="button" class="btn btn-primary searchCourse"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
$siteformatoptions = course_get_format($SITE)->get_format_options();
$modinfo = get_fast_modinfo($SITE);
$modnamesused = $modinfo->get_used_module_names();

// Print Section or custom info.
if (!empty($CFG->customfrontpageinclude)) {
    // Pre-fill some variables that custom front page might use.
    $modnames = get_module_types_names();
    $modnamesplural = get_module_types_names(true);
    $mods = $modinfo->get_cms();

    include($CFG->customfrontpageinclude);
} else if ($siteformatoptions['numsections'] > 0) {
    echo $courserenderer->frontpage_section1();
}
// Include course AJAX.
include_course_ajax($SITE, $modnamesused);

echo $courserenderer->frontpage();

if ($editing && has_capability('moodle/course:create', context_system::instance())) {
    echo $courserenderer->add_new_course_button();
}
echo $OUTPUT->footer();
?>