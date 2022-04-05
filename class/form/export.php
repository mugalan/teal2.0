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

class export extends moodleform
{

    //Add elements to form
    public function definition()
    {
        global $DB;
        $mform = $this->_form;

        // Select for pre-existing course
        $moodle_courses = get_courses();
        $exported_courses = $DB->get_records('teal_course_metadata');
        $exported_course_ids = [];
        foreach ($exported_courses as $course) {
            array_push($exported_course_ids, $course->course_id);
        }
        $options0 = [];
        foreach ($moodle_courses as $course) {
            // if ($course->id !== "1" and !in_array($course->id, $exported_course_ids, true))
            if ($course->id !== "1")
                $options0[$course->id] = $course->fullname . "(" . $course->shortname . ")";
        }
        $mform->addElement('select', 'course_id', 'Course', $options0);
        $mform->setDefault('course_id', [0]);

        // Course code field
        $mform->addElement('text', 'course_code', 'Course Code');
        $mform->setType('course_code', PARAM_NOTAGS);

        // Course credits field
        $mform->addElement('text', 'course_credits', 'Course Credits');
        $mform->setType('course_credits', PARAM_NOTAGS);

        // Course Category
        $options1 = array(
            'Mathematics' => 'Mathematics',
            'Basic Sciences' => 'Basic Sciences',
            'Computing' => 'Computing',
            'Design' => 'Design',
            'Projects' => 'Projects',
            'Management' => 'Management',
            'Economics' => 'Economics',
            'Ethics' => 'Ethics',
            'Other' => 'Other',
            'Humanities' => 'Humanities',
            'Engineering Science' => 'Engineering Science',
            'Communication' => 'Communication',
            'Law' => 'Law',
            'Finance' => 'Finance',
            'Languages' => 'Languages',
            'Music' => 'Music',
            'Drama' => 'Drama'
        );
        $mform->addElement('select', 'course_category', 'Course Category', $options1);
        $mform->setDefault('course_category', [1]);

        // Course Type
        $options2 = array(
            'CORE' => 'CORE',
            'ELECTIVE' => 'ELECTIVE'
        );
        $mform->addElement('select', 'course_type', 'Course Type', $options2);
        $mform->setDefault('course_type', [1]);

        // Course Level
        $options2 = array(
            'UG' => 'UG',
            'PG' => 'PG'
        );
        $mform->addElement('select', 'course_level', 'Course Level', $options2);
        $mform->setDefault('course_level', ['UG']);

        // Learning outcomes display
        $mform->addElement('text', 'course_learning_outcomes_1', 'Course Learning Outcomes');
        $mform->addElement('button', 'add_course_learning_outcomes', "Add Outcome");

        // Learning outcomes
        $mform->addElement('hidden', 'course_learning_outcomes', '');

        // Course DB
        $options3 = array(
            'global' => 'global',
            'local' => 'local'
        );
        $mform->addElement('select', 'course_db', 'Course DB', $options3);
        $mform->setDefault('course_db', ['global']);

        // Also upload course contents in a seperate repo? 
        $mform->addElement('checkbox', 'upload_content', 'Export the course content?');

        // Add Submit and Cancel button
        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
