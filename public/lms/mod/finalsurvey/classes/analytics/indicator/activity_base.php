<?php


namespace mod_finalsurvey\analytics\indicator;

defined('MOODLE_INTERNAL') || die();


abstract class activity_base extends \core_analytics\local\indicator\community_of_inquiry_activity {

    /**
     * No need to fetch grades for resources.
     *
     * @param \core_analytics\course $course
     * @return void
     */
    public function fetch_student_grades(\core_analytics\course $course) {
    }
}
