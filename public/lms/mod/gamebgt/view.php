<?php

require('../../config.php');
require_once($CFG->dirroot . '/mod/gamebgt/lib.php');
require_once($CFG->dirroot . '/mod/gamebgt/locallib.php');
require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID
$p = optional_param('p', 0, PARAM_INT);  // Page instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($p) {
    if (!$page = $DB->get_record('gamebgt', array('id' => $p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('gamebgt', $page->id, $page->course, false, MUST_EXIST);
} else {
    if (!$cm = get_coursemodule_from_id('gamebgt', $id)) {
        print_error('invalidcoursemodule');
    }
    $page = $DB->get_record('gamebgt', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
// require_capability('mod/gamebgt:view', $context);

// Completion and trigger events.
gamebgt_view($page, $course, $cm, $context);

$PAGE->set_url('/mod/gamebgt/view.php', array('id' => $cm->id));

$options = empty($page->displayoptions) ? array() : unserialize($page->displayoptions);

if ($inpopup and $page->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title($course->shortname . ': ' . $page->name);
    $PAGE->set_heading($course->fullname);
} else {
    $PAGE->set_title($course->shortname . ': ' . $page->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($page);
}
echo $OUTPUT->header();
if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($page->name), 2);
}

if (!empty($options['printintro'])) {
    if (trim(strip_tags($page->intro))) {
        echo $OUTPUT->box_start('mod_introbox', 'pageintro');
        echo format_module_intro('gamebgt', $page, $cm->id);
        echo $OUTPUT->box_end();
    }
}

$content = file_rewrite_pluginfile_urls($page->content, 'pluginfile.php', $context->id, 'mod_gamebgt', 'content', $page->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;

// [VinhPT] Modify url + userid + itemid
// $content = '<iframe src="/lms/mod/gamebgt/vietlott/index.php?userid=' . $USER->id . '&itemid=' . $content . '" width=100% height=100% frameborder="0" ></iframe>';
$button = '<button id="fullscreeniframe" onclick="openFullscreen();" class="btn btn-info py-2 px-2">' . get_string('fullscreen') . '</button>';
//$btn = '<a id="btn-click" target="iframe_modal" href="/lms/mod/gamebgt/vietlott/index.php?userid=' 
//. $USER->id . '&itemid=' . $content . '><button id="fullscreeniframe" class="btn btn-info py-2 px-2">' . get_string('fullscreen') . '</button></a> <br/>';

$btn = '<a class="btn_open_game btn btn-info py-2 px-2" class="btn btn-info py-2 px-2"><span>' . get_string('fullscreen') . '</span></a> <br/>';

$button .= $btn;

echo $button;

$popup = '<div id="myModal" class="modal">
  <p id="btn-close" class="">&times;</span>
  <iframe id="iframe1" height="300px" width="100%" src="" name="iframe_modal"></iframe>
</div>';

$content = '<iframe id="gamebgt_display" src="/lms/mod/gamebgt/vietlott/index.php?userid=' . $USER->id . '&itemid=' . $content . '" frameborder="1" width="1200" height="1200"></iframe>';
$content = $content . $popup;
$content = format_text($content, $page->contentformat, $formatoptions);
echo $OUTPUT->box($content, "generalbox center clearfix game_iframe");


// $content = format_text($content, $page->contentformat, $formatoptions);
// echo $OUTPUT->box($content, "generalbox center clearfix game_iframe");
?>

    <style>
        .modal {
            z-index: 1000;
            display: none;
            padding-top: 50px;
            /*position:fixed;*/
            left: 0;
            top: 0;
            width: 100%;
            height: 200%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4)
        }

        #btn-close {
            margin-left: 10px;
            color: #ffffff;
            font-size: 28px;
            font-weight: 600;
        }
		.game_iframe.isShow {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 2000;
			padding: 10px !important;
			background: rgba(51, 51, 51, 0.5);
		}
		a.btn_open_game{
			color:#fff !important;
			cursor:pointer !important;
		}
		a.btn_open_game.isShow {
			position: fixed;
			z-index: 2050;
			top: 10px;
			right: 10px;
			width: 30px;
			height: 30px;
			overflow: hidden;
			cursor: pointer;
			background: transparent !important;
			border: 0 !important;
		}
		a.btn_open_game.isShow span{
			opacity:0;
		}
		a.btn_open_game.isShow:before {
			content: "\f00d";
			display: inline-block;
			font: normal normal normal 14px/1 FontAwesome;
			font-size: inherit;
			text-rendering: auto;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			position: absolute;
			left: 13px;
			top: 6px;
			color: #fc4d56;
		}
    </style>
    <script>
        // (function(window, document) {
        //     var $ = function(selector, context) {
        //         return (context || document).querySelector(selector)
        //     };

        //     var iframe = $("iframe"),
        //         domPrefixes = 'Webkit Moz O ms Khtml'.split(' ');

        //     var fullscreen = function(elem) {
        //         var prefix;
        //         // Mozilla and webkit intialise fullscreen slightly differently
        //         for (var i = -1, len = domPrefixes.length; ++i < len;) {
        //             prefix = domPrefixes[i].toLowerCase();

        //             if (elem[prefix + 'EnterFullScreen']) {
        //                 // Webkit uses EnterFullScreen for video
        //                 return prefix + 'EnterFullScreen';
        //                 break;
        //             } else if (elem[prefix + 'RequestFullScreen']) {
        //                 // Mozilla uses RequestFullScreen for all elements and webkit uses it for non video elements
        //                 return prefix + 'RequestFullScreen';
        //                 break;
        //             }
        //         }

        //         return false;
        //     };
        //     // Webkit uses "requestFullScreen" for non video elements
        //     var fullscreenother = fullscreen(document.createElement("iframe"));

        //     if (!fullscreen) {
        //         alert("Fullscreen won't work, please make sure you're using a browser that supports it and you have enabled the feature");
        //         return;
        //     }

        //     $("#fullscreeniframe").addEventListener("click", function() {
        //         // iframe fullscreen and non video elements in webkit use request over enter
        //         iframe[fullscreenother]();
        //     }, false);
        // })(this, this.document);

        function getMobileOperatingSystem() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;


            if (/android/i.test(userAgent)) {
                return "android";
            }

            // iOS detection from: http://stackoverflow.com/a/9039885/177710
            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                return "ios";
            }

            return "web";
        }


        var elem = document.getElementById("gamebgt_display");

        function openFullscreen() {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.mozRequestFullScreen) {
                /* Firefox */
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Chrome, Safari & Opera */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* IE/Edge */
                elem.msRequestFullscreen();
            }
        }

        var modal = document.getElementById("myModal");
        $(document).ready(function () {
            $("#btn-click").click(function () {
                $("#myModal").show();
                $("#iframe1").attr("src", $(this).attr("href"));
                document.getElementById('gamebgt_display').contentWindow.location.reload();
                return false;
            });
            $("#btn-close").click(function () {
                modal.style.display = "none";
                $("#iframe1").attr("src", "");
            });

            var system = getMobileOperatingSystem();
            if (system == 'android' || system == 'ios') {
                $("#fullscreeniframe").hide();
                $(".btn_open_game").show();
            } else {
                $("#fullscreeniframe").show();
                $(".btn_open_game").hide();
            }
			
			$('.btn_open_game').click(function(){
				if($(this).hasClass('isShow')){
					$(this).removeClass('isShow');
					$('.game_iframe').removeClass('isShow');
				}else{
					$(this).addClass('isShow');
					$('.game_iframe').addClass('isShow');
				}
			});


        });


    </script>
<?php
if (!isset($options['printlastmodified']) || !empty($options['printlastmodified'])) {
    $strlastmodified = get_string("lastmodified");
    echo html_writer::div("$strlastmodified: " . userdate($page->timemodified), 'modified');
}

echo $OUTPUT->footer();
