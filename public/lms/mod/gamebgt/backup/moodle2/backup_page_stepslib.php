<?php


defined('MOODLE_INTERNAL') || die;

class backup_page_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $page = new backup_nested_element('gamebgt', array('id'), array(
            'name', 'intro', 'introformat', 'content', 'contentformat',
            'legacyfiles', 'legacyfileslast', 'display', 'displayoptions',
            'revision', 'timemodified'));

        // Build the tree
        // (love this)

        // Define sources
        $page->set_source_table('gamebgt', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $page->annotate_files('mod_gamebgt', 'intro', null); // This file areas haven't itemid
        $page->annotate_files('mod_gamebgt', 'content', null); // This file areas haven't itemid

        // Return the root element (page), wrapped into standard activity structure
        return $this->prepare_activity_structure($page);
    }
}
