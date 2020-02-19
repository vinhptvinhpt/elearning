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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_upoclassic
 * @copyright  2019 Roberto Pinna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_upoclassic\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * Note: This class is required to avoid inheriting Boost's core_renderer,
 *       which removes the edit button required by Classic.
 *
 * @package    theme_upoclassic
 * @copyright  2018 Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \core_renderer {
    /**
    * Get the HTML for blocks in the given region.
    *
    * @param array $regions The regions to get HTML for.
    * @param array $classes The regions classes.
    * @param string $headertext The regions group header text.
    * @return string HTML.
    */

    public function upoblocks($regions, $classes = array(), $headertext='') {
        global $OUTPUT, $PAGE;

        $classes = (array)$classes;
        $classestext = implode(' ', $classes);
        $activeregions = 0;
        $availableregions = count($regions);
        $result = '';
        foreach ($regions as $region) {
           if (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content($region, $OUTPUT)) {
               $activeregions++;
           } else if ($this->page->user_is_editing()) {
               $activeregions++;
           }

        }
        if ($activeregions > 0) {

           $result .= \html_writer::start_tag('div', array('class' => $classestext));
           if (!empty($headertext)) {
               $result .= \html_writer::tag('h4', $headertext, array());
           }
           $result .= \html_writer::start_tag('div', array('class' => 'row'));

           $span = floor(12/$activeregions);
           $i = 0;
           foreach ($regions as $region) {
               $i++;
               $displayclass = '';

               if ($activeregions > 1) {
                   if ($i == 1) {
                       $displayclass = ' pr-1';
                   } else if ($i == $activeregions) {
                       $displayclass = ' pl-1';
                   } else {
                       $displayclass = ' px-1';
                   }
               }

               $regionclass = 'col-lg-'.$span.$displayclass;
               if ($PAGE->blocks->region_has_content($region, $OUTPUT) || $this->page->user_is_editing()) {
                   $result .= $OUTPUT->blocks($region, $regionclass);
               }
               $first = false;
           }
           $result .= \html_writer::end_tag('div');
           $result .= \html_writer::end_tag('div');
        }

        return $result;
    }
}
