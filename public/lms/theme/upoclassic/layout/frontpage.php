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
 * The columns layout for the upoclassic theme.
 *
 * @package   theme_upoclassic
 * @copyright 2019 Roberto Pinna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$bodyattributes = $OUTPUT->body_attributes();
$blocksbanner = $OUTPUT->upoblocks(array('banner'), array('banner-blocks'));
$blockshome = $OUTPUT->upoblocks(array('home-left', 'home-middle', 'home-right'), array('home-blocks'));
$blockspre = $OUTPUT->blocks('side-pre');
$blockspost = $OUTPUT->blocks('side-post');

$stradminonly = get_string('adminonly', 'theme_upoclassic');
$hiddendock = $OUTPUT->upoblocks(array('hidden-dock'), array('hidden-blocks'), $stradminonly);
$blocksfooter = $OUTPUT->upoblocks(array('footer-left', 'footer-middle', 'footer-right'),
                                   array('footer-blocks'));

$hasbanner = $PAGE->blocks->region_has_content('banner', $OUTPUT) || $PAGE->user_is_editing();
$hashomeblock = $PAGE->blocks->region_has_content('home-left', $OUTPUT) || $PAGE->user_is_editing();
$hashomeblock = $hashomeblock || $PAGE->blocks->region_has_content('home-middle', $OUTPUT);
$hashomeblock = $hashomeblock || $PAGE->blocks->region_has_content('home-right', $OUTPUT);
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT) || $PAGE->user_is_editing();
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT) || $PAGE->user_is_editing();
$hashiddendock = is_siteadmin() && ($PAGE->blocks->region_has_content('hidden-dock', $OUTPUT) || $PAGE->user_is_editing());
$hasfooterblock = $PAGE->blocks->region_has_content('footer-left', $OUTPUT) || $PAGE->user_is_editing();
$hasfooterblock = $hasfooterblock || $PAGE->blocks->region_has_content('footer-middle', $OUTPUT);
$hasfooterblock = $hasfooterblock || $PAGE->blocks->region_has_content('footer-right', $OUTPUT);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bannerblocks' => $blocksbanner,
    'homeblocks' => $blockshome,
    'sidepreblocks' => $blockspre,
    'sidepostblocks' => $blockspost,
    'hiddendock' => $hiddendock,
    'footerblocks' => $blocksfooter,
    'hasbannerblocks' => $hasbanner,
    'hashomeblocks' => $hashomeblock,
    'haspreblocks' => $hassidepre,
    'haspostblocks' => $hassidepost,
    'hashiddendock' => $hashiddendock,
    'hasfooterblocks' => $hasfooterblock,
    'bodyattributes' => $bodyattributes
];

echo $OUTPUT->render_from_template('theme_upoclassic/frontpage', $templatecontext);

