<?php

require_once("$CFG->libdir/externallib.php");

function get_course_contents($courseid)
{
    global $CFG, $DB, $USER;
    require_once($CFG->dirroot . "/course/lib.php");
    require_once($CFG->libdir . '/completionlib.php');

    //retrieve the course
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    if ($course->id != SITEID) {
        // Check course format exist.
        if (!file_exists($CFG->dirroot . '/course/format/' . $course->format . '/lib.php')) {
            throw new moodle_exception(
                'cannotgetcoursecontents',
                'webservice',
                '',
                null,
                get_string('courseformatnotfound', 'error', $course->format)
            );
        } else {
            require_once($CFG->dirroot . '/course/format/' . $course->format . '/lib.php');
        }
    }

    // now security checks
    $context = context_course::instance($course->id, IGNORE_MISSING);

    $canupdatecourse = has_capability('moodle/course:update', $context);

    //create return value
    $coursecontents = array();

    if (
        $canupdatecourse or $course->visible
        or has_capability('moodle/course:viewhiddencourses', $context)
    ) {

        //retrieve sections
        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();
        $coursenumsections = course_get_format($course)->get_last_section_number();
        $stealthmodules = array();   // Array to keep all the modules available but not visible in a course section/topic.

        $completioninfo = new completion_info($course);

        //for each sections (first displayed to last displayed)
        $modinfosections = $modinfo->get_sections();
        foreach ($sections as $key => $section) {

            // This becomes true when we are filtering and we found the value to filter with.
            $sectionfound = false;

            // Filter by section id.
            if (!empty($filters['sectionid'])) {
                if ($section->id != $filters['sectionid']) {
                    continue;
                } else {
                    $sectionfound = true;
                }
            }

            // Filter by section number. Note that 0 is a valid section number.
            if (isset($filters['sectionnumber'])) {
                if ($key != $filters['sectionnumber']) {
                    continue;
                } else {
                    $sectionfound = true;
                }
            }

            // reset $sectioncontents
            $sectionvalues = array();
            $sectionvalues['id'] = $section->id;
            $sectionvalues['name'] = get_section_name($course, $section);
            $sectionvalues['visible'] = $section->visible;

            $options = (object) array('noclean' => true);
            list($sectionvalues['summary'], $sectionvalues['summaryformat']) =
                external_format_text(
                    $section->summary,
                    $section->summaryformat,
                    $context->id,
                    'course',
                    'section',
                    $section->id,
                    $options
                );
            $sectionvalues['section'] = $section->section;
            $sectionvalues['hiddenbynumsections'] = $section->section > $coursenumsections ? 1 : 0;
            $sectionvalues['uservisible'] = $section->uservisible;
            if (!empty($section->availableinfo)) {
                $sectionvalues['availabilityinfo'] = \core_availability\info::format_info($section->availableinfo, $course);
            }

            $sectioncontents = array();

            // For each module of the section.
            if (empty($filters['excludemodules']) and !empty($modinfosections[$section->section])) {
                foreach ($modinfosections[$section->section] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                    //get status of completion
                    $getStatusCompletion = array_values($DB->get_records_sql("Select completionstate from mdl_course_modules_completion where userid = ".$USER->id." and coursemoduleid = ". $cm->id))[0];
                    // Stop here if the module is not visible to the user on the course main page:
                    // The user can't access the module and the user can't view the module on the course page.
                    if (!$cm->uservisible && !$cm->is_visible_on_course_page()) {
                        continue;
                    }

                    // This becomes true when we are filtering and we found the value to filter with.
                    $modfound = false;

                    // Filter by cmid.
                    if (!empty($filters['cmid'])) {
                        if ($cmid != $filters['cmid']) {
                            continue;
                        } else {
                            $modfound = true;
                        }
                    }

                    // Filter by module name and id.
                    if (!empty($filters['modname'])) {
                        if ($cm->modname != $filters['modname']) {
                            continue;
                        } else if (!empty($filters['modid'])) {
                            if ($cm->instance != $filters['modid']) {
                                continue;
                            } else {
                                // Note that if we are only filtering by modname we don't break the loop.
                                $modfound = true;
                            }
                        }
                    }

                    $module = array();

                    $modcontext = context_module::instance($cm->id);

                    //common info (for people being able to see the module or availability dates)
                    $module['id'] = $cm->id;
                    $module['countcompletion'] = 0;
                    $module['iscompletion'] = $getStatusCompletion->completionstate;
                    if($getStatusCompletion->completionstate < 3 && $getStatusCompletion->completionstate > 0 )
                        $module['countcompletion'] = 1;
                    $module['name'] = external_format_string($cm->name, $modcontext->id);
                    $module['instance'] = $cm->instance;
                    $module['modname'] = $cm->modname;
                    $module['modplural'] = $cm->modplural;
                    $module['modicon'] = $cm->get_icon_url()->out(false);
                    $module['indent'] = $cm->indent;
                    $module['onclick'] = $cm->onclick;
                    $module['afterlink'] = $cm->afterlink;
                    $module['customdata'] = json_encode($cm->customdata);
                    $module['completion'] = $cm->completion;



                    // Check module completion.
                    $completion = $completioninfo->is_enabled($cm);
                    if ($completion != COMPLETION_DISABLED) {
                        $completiondata = $completioninfo->get_data($cm, true);
                        $module['completiondata'] = array(
                            'state'         => $completiondata->completionstate,
                            'timecompleted' => $completiondata->timemodified,
                            'overrideby'    => $completiondata->overrideby,
                            'valueused'     => core_availability\info::completion_value_used($course, $cm->id)
                        );
                    }

                    if (!empty($cm->showdescription) or $cm->modname == 'label') {
                        // We want to use the external format. However from reading get_formatted_content(), $cm->content format is always FORMAT_HTML.
                        $options = array('noclean' => true);
                        list($module['description'], $descriptionformat) = external_format_text(
                            $cm->content,
                            FORMAT_HTML,
                            $modcontext->id,
                            $cm->modname,
                            'intro',
                            $cm->id,
                            $options
                        );
                    }

                    //url of the module
                    $url = $cm->url;
                    if ($url) { //labels don't have url
                        $module['url'] = $url->out(false);
                    }

                    $canviewhidden = has_capability(
                        'moodle/course:viewhiddenactivities',
                        context_module::instance($cm->id)
                    );
                    //user that can view hidden module should know about the visibility
                    $module['visible'] = $cm->visible;
                    $module['visibleoncoursepage'] = $cm->visibleoncoursepage;
                    $module['uservisible'] = $cm->uservisible;
                    if (!empty($cm->availableinfo)) {
                        $module['availabilityinfo'] = \core_availability\info::format_info($cm->availableinfo, $course);
                    }

                    // Availability date (also send to user who can see hidden module).
                    if ($CFG->enableavailability && ($canviewhidden || $canupdatecourse)) {
                        $module['availability'] = $cm->availability;
                    }

                    // Return contents only if the user can access to the module.
                    if ($cm->uservisible) {
                        $baseurl = 'webservice/pluginfile.php';

                        // Call $modulename_export_contents (each module callback take care about checking the capabilities).
                        require_once($CFG->dirroot . '/mod/' . $cm->modname . '/lib.php');
                        $getcontentfunction = $cm->modname . '_export_contents';
                        if (function_exists($getcontentfunction)) {
                            $contents = $getcontentfunction($cm, $baseurl);
                            $module['contentsinfo'] = array(
                                'filescount' => count($contents),
                                'filessize' => 0,
                                'lastmodified' => 0,
                                'mimetypes' => array(),
                            );
                            foreach ($contents as $content) {
                                // Check repository file (only main file).
                                if (!isset($module['contentsinfo']['repositorytype'])) {
                                    $module['contentsinfo']['repositorytype'] =
                                        isset($content['repositorytype']) ? $content['repositorytype'] : '';
                                }
                                if (isset($content['filesize'])) {
                                    $module['contentsinfo']['filessize'] += $content['filesize'];
                                }
                                if (
                                    isset($content['timemodified']) &&
                                    ($content['timemodified'] > $module['contentsinfo']['lastmodified'])
                                ) {

                                    $module['contentsinfo']['lastmodified'] = $content['timemodified'];
                                }
                                if (isset($content['mimetype'])) {
                                    $module['contentsinfo']['mimetypes'][$content['mimetype']] = $content['mimetype'];
                                }
                            }

                            if (empty($filters['excludecontents']) and !empty($contents)) {
                                $module['contents'] = $contents;
                            } else {
                                $module['contents'] = array();
                            }
                        }
                    }

                    // Assign result to $sectioncontents, there is an exception,
                    // stealth activities in non-visible sections for students go to a special section.
                    if (!empty($filters['includestealthmodules']) && !$section->uservisible && $cm->is_stealth()) {
                        $stealthmodules[] = $module;
                    } else {
                        $sectioncontents[] = $module;
                    }

                    // If we just did a filtering, break the loop.
                    if ($modfound) {
                        break;
                    }
                }
            }
            $sectionvalues['modules'] = $sectioncontents;

            // assign result to $coursecontents
            $coursecontents[$key] = $sectionvalues;

            // Break the loop if we are filtering.
            if ($sectionfound) {
                break;
            }
        }

        // Now that we have iterated over all the sections and activities, check the visibility.
        // We didn't this before to be able to retrieve stealth activities.
        foreach ($coursecontents as $sectionnumber => $sectioncontents) {
            $section = $sections[$sectionnumber];
            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display.
            $showsection = $section->uservisible ||
                ($section->visible && !$section->available &&
                    !empty($section->availableinfo));

            if (!$showsection) {
                unset($coursecontents[$sectionnumber]);
                continue;
            }

            // Remove modules information if the section is not visible for the user.
            if (!$section->uservisible) {
                $coursecontents[$sectionnumber]['modules'] = array();
            }
        }

        // Include stealth modules in special section (without any info).
        if (!empty($stealthmodules)) {
            $coursecontents[] = array(
                'id' => -1,
                'name' => '',
                'summary' => '',
                'summaryformat' => FORMAT_MOODLE,
                'modules' => $stealthmodules
            );
        }
    }

    return $coursecontents;
}
