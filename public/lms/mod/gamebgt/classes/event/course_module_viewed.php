<?php


namespace mod_gamebgt\event;
defined('MOODLE_INTERNAL') || die();

class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'gamebgt';
    }

    public static function get_objectid_mapping() {
        return array('db' => 'gamebgt', 'restore' => 'gamebgt');
    }
}

