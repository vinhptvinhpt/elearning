(function ($) {
    $(document).ready(function () {

        var userAgent = window.navigator.userAgent.toLowerCase(),
            safari = /safari/.test(userAgent),
            ios = /iphone|ipod|ipad/.test(userAgent);

        if (ios) {
            if (safari) {
                $('body').addClass('not-webview');
            } else if (!safari) {
                $('body').addClass('webview');
            };
        } else {
            $('body').addClass('not-ios');
        };

        $('.course-content ul.topics li.section .content h3.sectionname > span > a').click(function () {
            if (!$('body.pagelayout-course').hasClass('editing')) {
                var parent = $(this).parents('.content');
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $('ul.section', parent).slideUp();
                } else {
                    $('.course-content ul.topics li.section .content h3.sectionname > span > a').removeClass('active');
                    $(this).addClass('active');
                    $('ul.section').slideUp();
                    $('ul.section', parent).slideDown();
                }
                return false;
            }
        });

        if ($('.completionprogress +h2').length > 0 && $('.completionprogress +h2').hasClass('accesshide')) {
            $('.completionprogress +h2').removeClass('accesshide');
        }

        $('.h5p-iframe-wrapper').append('<div class="h5p_fixcontent"></div>');

        $('form#responseform .que .formulation .answer input[type="radio"]').click(function () {
            var name = $(this).attr('name');
            var array = name.split(":");
            var strs = array[1].split("_");
            var quesid = 'quiznavbutton' + strs[0];
            if ($('#' + quesid).hasClass('notyetanswered')) {
                $('#' + quesid).removeClass('notyetanswered');
                $('#' + quesid).addClass('answersaved');
            }
        });
        /**********
         * Scroll
         * **************/
        function scroll_element() {
            /*change color menu*/
            if ($('#block-region-side-pre').length > 0) {
                var scroll_top = $(window).scrollTop();
                var element_top = $('#block-region-side-pre').offset().top;
                var element_width = $('#block-region-side-pre').outerWidth();
                if (element_top < scroll_top + 61) {
                    $('body').addClass('sidebarFix');
                    $('section#mod_quiz_navblock').css('width', element_width + 'px');
                } else {
                    $('body').removeClass('sidebarFix');
                }
            }
        }
        scroll_element();
        $(window).scroll(function () {
            scroll_element();
        });

        $('.submenu-collapse').click(function () {
            var key = $(this).attr('data-key');
            if ($(this).hasClass('open')) {
                $('.submenu-collapse-items[data-parent-key="' + key + '"]').hide();
                $(this).removeClass('open');
            } else {
                $('.submenu-collapse-items[data-parent-key="' + key + '"]').show();
                $(this).addClass('open');
            }
        });

        $('.path-mod-quiz #mod_quiz_navblock .qnbutton').click(function () {
            var id = $(this).attr('id');
            var ques_num = id.replace("quiznavbutton", "");
            var content_id = $('#responseform .que:first-child').attr('id');
            var ques_content_id = content_id.replace("1", "");
            var offset_top = $('#' + ques_content_id + ques_num).offset().top;
            $("html, body").animate({ scrollTop: offset_top - 60 }, "slow");
            return false;
        });

        $('.userprofile .profile_tree section h3').click(function () {
            var parent = $(this).parent();
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
                $('> ul', parent).slideUp();
            } else {
                //$('.userprofile .profile_tree section h3').removeClass('active');
                $(this).addClass('active');
                //$('.userprofile .profile_tree section > ul').slideUp();
                $('> ul', parent).slideDown();
            }
        });


        if ($('.activity-navigation .btn-link').length > 0) {
            if (($("#page-mod-quiz-attempt").length > 0)) {
                var pre_title = $('.activity-navigation a#prev-activity-link').attr('title');

                var pre_href = $('.activity-navigation a#prev-activity-link').attr('href');
                var next_href = $('.activity-navigation a#next-activity-link').attr('href');
                $('.activity-navigation a#next-activity-link').attr('href', '#');
                $('.activity-navigation a#prev-activity-link').attr('href', '#');


                $('.activity-navigation a#prev-activity-link').html(pre_title);
                $('.activity-navigation a#prev-activity-link').click(function (e) {
                    window.onbeforeunload = null;
                    e.preventDefault();
                    if (confirm("Finish your quiz and go to next activity " + pre_title)) {
                        window.location.replace(pre_href);
                    }
                });

                var next_title = $('.activity-navigation a#next-activity-link').attr('title');
                $('.activity-navigation a#next-activity-link').html(next_title);
                $('.activity-navigation a#next-activity-link').click(function (e) {
                    window.onbeforeunload = null;
                    e.preventDefault();
                    if (confirm("Finish your quiz and go to next activity " + next_title)) {
                        window.location.replace(next_href);
                    }
                });


            } else {
                $('.activity-navigation a#prev-activity-link').html('Previous');
                var pre_title = $('.activity-navigation a#prev-activity-link').attr('title');
                $('.activity-navigation a#prev-activity-link').append('<span class="title">' + pre_title + '</span>');

                if (($(".activity-navigation a#next-activity-link").length > 0)) {
                    $('.activity-navigation a#next-activity-link').html('Next');
                    var next_title = $('.activity-navigation a#next-activity-link').attr('title');
                    $('.activity-navigation a#next-activity-link').append('<span class="title">' + next_title + '</span>');
                } else {
                    $('.float-right').append('<a href="/lms" id="next-activity-link" class="btn btn-link" title="Return to Homepage">Return to Homepage<span class="title">Return to Homepage</span></a>');
                }
            }
        }

        if ($('.over-wrap').length > 0) {
            $('.over-wrap').click(function () {
                $('body').removeClass('drawer-open-left');
                $('#nav-drawer').addClass('closed');
                $('button[data-action="toggle-drawer"]').attr('aria-expanded', false);
            });
        }

        //clear choice
        $('.qtype_multichoice_clearchoice').click(function () {
            var getID = $(this).attr('id');
            var numberOfQuestion = getID.substring(
                getID.lastIndexOf(":") + 1,
                getID.lastIndexOf("_")
            );
            $('#quiznavbutton' + numberOfQuestion).removeClass('answersaved');
        });

        //click choice
        $('.answer input').on('click', function () {
            var getID = $(this).attr('id');
            var numberOfQuestion = getID.substring(
                getID.lastIndexOf(":") + 1,
                getID.lastIndexOf("_")
            );
            $('#quiznavbutton' + numberOfQuestion).addClass('answersaved');
        });

        setInterval(function () {
            var id_ques = 0;
            var check_ans = false;
            $("form#responseform .que .formulation input.placeinput").each(function (index) {
                var name = $(this).attr('name');
                var array = name.split(":");
                var strs = array[1].split("_");
                var quesid = 'quiznavbutton' + strs[0];
                if (id_ques != strs[0]) {
                    check_ans = false;
                    id_ques = strs[0];
                }
                if ($(this).val() && $(this).val() > 0) {
                    if ($('#' + quesid).hasClass('notyetanswered')) {
                        $('#' + quesid).removeClass('notyetanswered');
                    }
                    if (!$('#' + quesid).hasClass('answersaved')) {
                        $('#' + quesid).addClass('answersaved');
                    }
                    check_ans = true;
                }
                if (!check_ans) {
                    if (!$(this).val() || $(this).val() == 0) {
                        if ($('#' + quesid).hasClass('answersaved')) {
                            $('#' + quesid).removeClass('answersaved');
                        }
                    }
                }
            });

            $("form#responseform .que .formulation .answer .qtype_essay_editor textarea").each(function (index) {
                var name = $(this).attr('name');
                var array = name.split(":");
                var strs = array[1].split("_");
                var quesid = 'quiznavbutton' + strs[0];
                if ($(this).val()) {
                    if ($('#' + quesid).hasClass('notyetanswered')) {
                        $('#' + quesid).removeClass('notyetanswered');
                    }
                    if (!$('#' + quesid).hasClass('answersaved')) {
                        $('#' + quesid).addClass('answersaved');
                    }
                }
            });
        }, 1000);

        $('form#responseform .que .formulation .answer select.select').change(function () {
            var name = $(this).attr('name');
            var array = name.split(":");
            var strs = array[1].split("_");
            var quesid = 'quiznavbutton' + strs[0];
            if ($('#' + quesid).hasClass('notyetanswered')) {
                $('#' + quesid).removeClass('notyetanswered');
            }
            if (!$('#' + quesid).hasClass('invalidanswer')) {
                $('#' + quesid).addClass('invalidanswer');
            }
        });

        $('form#responseform .que .formulation .qtext select.select').change(function() {
            var name = $(this).attr('name');
            var array = name.split(":");
            var strs = array[1].split("_");
            var quesid = 'quiznavbutton' + strs[0];
			if($('#' + quesid).hasClass('notyetanswered')){
				$('#' + quesid).removeClass('notyetanswered');
			}
			if(!$('#' + quesid).hasClass('invalidanswer')){
				$('#' + quesid).addClass('invalidanswer');
			}
        });

        $('form#responseform .que .formulation .answer [type="text"]').change(function () {
            var name = $(this).attr('name');
            var array = name.split(":");
            var strs = array[1].split("_");
            var quesid = 'quiznavbutton' + strs[0];
            if ($(this).val()) {
                if ($('#' + quesid).hasClass('notyetanswered')) {
                    $('#' + quesid).removeClass('notyetanswered');
                }
                if (!$('#' + quesid).hasClass('answersaved')) {
                    $('#' + quesid).addClass('answersaved');
                }
            } else {
                if ($('#' + quesid).hasClass('answersaved')) {
                    $('#' + quesid).removeClass('answersaved');
                }
                if (!$('#' + quesid).hasClass('notyetanswered')) {
                    $('#' + quesid).addClass('notyetanswered');
                }
            }
        });
    });
})(jQuery);
