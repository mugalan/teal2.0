<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @package     local_teal
 * @author      abhiandthetruth
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * 
 */
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');

class ExportContent extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $DB;
        $mform = $this->_form;

        // Select for pre-existing content
        $moodle_courses = get_courses();

        foreach ($moodle_courses as $course) {
            if ($course->id !== "1")
                $options0[$course->id] = $course->fullname . "(" . $course->shortname . ")";
        }
        $mform->addElement('select', 'course_id', 'Course', $options0);
        $mform->setDefault('course_id', [0]);

        // Content code field
        $mform->addElement('text', 'content_code', 'Content Code');

        // Content description addition
        $mform->addElement("textarea", 'description', "Content Description");

        // Add Submit and Cancel button
        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
