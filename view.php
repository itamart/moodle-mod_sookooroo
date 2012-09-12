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
 * Sookooroo
 *
 * @package    mod
 * @subpackage sookooroo
 * @copyright  2012 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

$id = optional_param('id',0,PARAM_INT);    // Course Module ID, or
$skr = optional_param('skr',0,PARAM_INT);     // Sookooroo ID

if ($id) {
    $PAGE->set_url('/mod/sookooroo/index.php', array('id' => $id));
    if (!$cm = get_coursemodule_from_id('sookooroo', $id)) {
        print_error('invalidcoursemodule');
    }

    if (!$course = $DB->get_record("course", array('id' => $cm->course))) {
        print_error('coursemisconf');
    }

    if (!$sookooroo = $DB->get_record("sookooroo", array('id' => $cm->instance))) {
        print_error('invalidcoursemodule');
    }

} else {
    if (! $sookooroo = $DB->get_record("sookooroo", array('id' => $skr))) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record("course", array('id' => $sookooroo->course)) ){
        print_error('coursemisconf');
    }
    if (!$cm = get_coursemodule_from_instance('sookooroo', $sookooroo->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
    $PAGE->set_url('/mod/sookooroo/index.php', array('id' => $cm->id));
}

require_login($course, true, $cm);

redirect("$CFG->wwwroot/course/view.php?id=$course->id");


