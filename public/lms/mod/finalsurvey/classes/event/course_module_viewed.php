<?php


namespace mod_finalsurvey\event;
defined('MOODLE_INTERNAL') || die();

class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'finalsurvey';
    }

    public static function get_objectid_mapping() {
        return array('db' => 'finalsurvey', 'restore' => 'finalsurvey');
    }
}

