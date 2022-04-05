<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @package     local_teal
 * @author      thesmallstar
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * 
 */
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');

class CreateProgram extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $DB;
        $mform = $this->_form;

        $local_courses = $DB->get_records('teal_course_metadata');

        // Program Name
        $mform->addElement('text', 'program_name', 'Program name');

        // Program Code
        $mform->addElement('text', 'program_code', 'Program Code');

        // Program Code
        $mform->addElement('text', 'program_credits', 'Program Credits');

        // Course Level
        $options2 = array(
            'UG' => 'UG',
            'PG' => 'PG'
        );

        $mform->addElement('select', 'program_level', 'Program Level', $options2);
        $mform->setDefault('program_level', ['UG']);

        $options = [];
        foreach ($local_courses as $course) {
            $options[$course->id] = $course->course_name . "(" . $course->course_code . ")";
        }

        $options2 = array(
            'multiple' => true,
            'noselectionstring' => "Select Courses",
        );

        $mform->addElement('autocomplete', 'pcourses', "Select Courses", $options, $options2);
        //   $mform->addElement('static', 'info', "Note that all courses not available globally will be exported online!");
        $mform->addElement('html', "<p>Note that all courses not available globally will be exported to global database!</p>");
        // Add Submit and Cancel button
        $this->add_action_buttons($submitlabel = "Create");
    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
