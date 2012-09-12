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

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_sookooroo_mod_form extends moodleform_mod {

    function definition() {
        global $DB, $SITE, $CFG, $PAGE;
        $mform = $this->_form;

        // buttons
        //-------------------------------------------------------------------------------
    	$this->add_action_buttons();

        // Module settings
        //--------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // intro
        $this->add_intro_editor(false);

        // Website id
        $mform->addElement('text', 'websiteid', get_string('websiteid', 'sookooroo'));
        $mform->setType('websiteid', PARAM_TEXT);
        $mform->addRule('websiteid', null, 'maxlength', 20, 'client'); 
        $mform->addHelpButton('websiteid', 'websiteid', 'sookooroo');

        // Room name
        $mform->addElement('text', 'room', get_string('room', 'sookooroo'));
        $mform->setType('room', PARAM_TEXT);
        $mform->addRule('room', null, 'maxlength', 30, 'client'); 
        $mform->addHelpButton('room', 'room', 'sookooroo');

        // Button text
        $mform->addElement('text', 'btn', get_string('btn', 'sookooroo'));
        $mform->setType('btn', PARAM_TEXT);
        $mform->addRule('btn', null, 'maxlength', 256, 'client'); 
        $mform->addHelpButton('btn', 'btn', 'sookooroo');

        $this->standard_coursemodule_elements();

        // buttons
        //-------------------------------------------------------------------------------
    	$this->add_action_buttons();
    }   
}
