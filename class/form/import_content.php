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
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');

class ImportContent extends moodleform
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

        // Getting available course content
        $githubActionsHelper = new \GithubActions();
        $repos = $githubActionsHelper->getCourseRepositoriesNames(true);
        $options1 = ["" => "Select Course Content"];
        foreach ($repos as $repo) {
            if (substr($repo, 0, 13) == 'CourseContent') {
                $options1[$repo] = substr($repo, 14);
            }
        }
        $mform->addElement('select', 'course_content', 'Course Content', $options1);
        $mform->setDefault('course_content', [""]);

        // Getting the branches and commits for the selected content
        $mform->addElement('select', 'branch', 'Institute(Branch)', []);
        $mform->addElement('hidden', 'branch_hid', '');
        $mform->addElement('select', 'commit', 'Version(Commit)', []);
        $mform->addElement('hidden', 'commit_hid', '');

        // The database
        $mform->addElement('hidden', 'database', 'global');

        // Add Submit and Cancel button
        $this->add_action_buttons(true, "Import");
    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
