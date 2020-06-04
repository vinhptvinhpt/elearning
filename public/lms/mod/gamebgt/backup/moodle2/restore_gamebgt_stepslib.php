<?php

/**
 * Define all the restore steps that will be used by the restore_page_activity_task
 */

/**
 * Structure step to restore one page activity
 */
class restore_gamebgt_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('gamebgt', '/activity/gamebgt');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_gamebgt($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.

        // insert the page record
        $newitemid = $DB->insert_record('gamebgt', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Add page related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_gamebgt', 'intro', null);
        $this->add_related_files('mod_gamebgt', 'content', null);
    }
}
