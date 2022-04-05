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

class UpdateCourse extends moodleform
{
    public $id;

    function __construct($id)
    {
        $this->id = $id;
        parent::__construct();
    }

    //Add elements to form
    public function definition()
    {
        global $DB;
        $mform = $this->_form;

        // Select for pre-existing course
        global $DB;
        $courses = $DB->get_records('teal_course_metadata');
        $course = null;
        foreach ($courses as $temp_course) {
            if ($temp_course->id == $this->id)
                $course = $temp_course;
        }

        $mform->addElement('hidden', 'id', $course->id);

        $mform->addElement('select', 'course_id_dropdown', "Course", [$course->course_id => $course->course_name]);

        $mform->addElement('hidden', 'course_id', $course->course_id);

        // Course code field
        $mform->addElement('static', 'course_code', 'Course Code', $course->course_code);

        // Course credits field
        $mform->addElement('text', 'course_credits', 'Course Credits');
        $mform->setType('course_credits', PARAM_NOTAGS);
        $mform->setDefault('course_credits', $course->course_credits);

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
        $mform->setDefault('course_category', $course->course_category);

        // Course Type
        $options2 = array(
            'CORE' => 'CORE',
            'ELECTIVE' => 'ELECTIVE'
        );
        $mform->addElement('select', 'course_type', 'Course Type', $options2);
        $mform->setDefault('course_type', [$course->course_type]);

        // Course Level
        $options2 = array(
            'UG' => 'UG',
            'PG' => 'PG'
        );
        $mform->addElement('select', 'course_level', 'Course Level', $options2);
        $mform->setDefault('course_level', [$course->course_level]);

        // Learning outcomes display
        $mform->addElement('text', 'course_learning_outcomes_1', 'Course Learning Outcomes');
        $mform->addElement('button', 'add_course_learning_outcomes', "Add Outcome");

        // Learning outcomes
        $mform->addElement('hidden', 'course_learning_outcomes', '');

        // Commit message
        $mform->addElement('text', 'commit_message', 'Commit Message');
        $mform->setDefault('commit_message', 'updated file');

        // Add Submit and Cancel button
        $this->add_action_buttons();
    }

    // Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
