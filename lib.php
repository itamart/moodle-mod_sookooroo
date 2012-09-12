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
 * @package    mod
 * @subpackage sookooroo
 * @copyright  2012 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

/**
 * @global object
 * @param object $sookooroo
 * @return bool|int
 */
function sookooroo_add_instance($sookooroo) {
    global $DB;

    if (empty($sookooroo->websiteid) or empty($sookooroo->room)) {
        return null;
    }

    $sookooroo->name = get_string('modulename','sookooroo');
    $sookooroo->timemodified = time();

    return $DB->insert_record("sookooroo", $sookooroo);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $sookooroo
 * @return bool
 */
function sookooroo_update_instance($sookooroo) {
    global $DB;

    if (empty($sookooroo->websiteid) or empty($sookooroo->room)) {
        return null;
    }

    $sookooroo->name = get_string('modulename','sookooroo');
    $sookooroo->timemodified = time();
    $sookooroo->id = $sookooroo->instance;

    return $DB->update_record("sookooroo", $sookooroo);
}

/**
 * @global object
 * @param int $id
 * @return bool
 */
function sookooroo_delete_instance($id) {
    global $DB;

    if (!$sookooroo = $DB->get_record("sookooroo", array("id"=>$id))) {
        return false;
    }

    $result = true;

    if (!$DB->delete_records("sookooroo", array("id"=>$sookooroo->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return object|null
 */
function sookooroo_get_coursemodule_info($coursemodule) {
    global $DB;

    if ($sookooroo = $DB->get_record('sookooroo', array('id'=>$coursemodule->instance), 'id, name')) {
        if (empty($sookooroo->name)) {
            // sookooroo name missing, fix it
            $sookooroo->name = "sookooroo{$sookooroo->id}";
            $DB->set_field('sookooroo', 'name', $sookooroo->name, array('id'=>$sookooroo->id));
        }
        $info = new stdClass();
        $info->extra = '';           
        $info->name  = $sookooroo->name;
        return $info;
    } else {
        return null;
    }
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return object|null
 */
function sookooroo_cm_info_view(cm_info $cm) {
    global $DB, $PAGE, $USER;

    // No access to guests
//    if (isguestuser() or !isloggedin()) {
//        $cm->set_content('');
//        return;
//    }

    // Get the resource data
    if (!$sookooroo = $DB->get_record('sookooroo', array('id' => $cm->instance), 'id, course, name, websiteid, room, btn')) {
        $cm->set_content('');
        return;
    }

    // We must have at least website id and room name
    if (empty($sookooroo->websiteid) or empty($sookooroo->room)) {
        $cm->set_content('');
        return;
    }
        
    // Add the javascript
    $jsinclude = new moodle_url('http://api.sookooroo.com/Api/Vc', array('WebsiteId' => $sookooroo->websiteid));
    $PAGE->requires->js($jsinclude);

    // Generate user pic
    $userpic = new user_picture($USER);

    $userid = isguestuser() ? '' : $USER->id;
    
    // Add the container
    $params = array(
        'class' => "Skr-room",
        'data-room' => $sookooroo->room,
        'data-cid' => $userid,
        'data-cname' => fullname($USER),
        'data-cpic' => $userpic->get_url($PAGE),    
        'data-iscidadmin' => has_capability('mod/sookooroo:moderator', context_module::instance($cm->id)),
        'data-source' => 'moodle',    
        'data-showfaces' => true,    
        'data-width' => "300",    
        'data-btn' => !empty($sookooroo->btn) ? $sookooroo->btn: '',    
    );

    $content = html_writer::tag(
        'div',
        null,
        $params  
    );

    $cm->set_content($content);
}

/**
 * @return array
 */
function sookooroo_get_view_actions() {
    return array();
}

/**
 * @return array
 */
function sookooroo_get_post_actions() {
    return array();
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function sookooroo_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function sookooroo_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 */
function sookooroo_supports($feature) {
    switch($feature) {
        case FEATURE_IDNUMBER:                return false;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return false;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_NO_VIEW_LINK:            return true;

        default: return null;
    }
}