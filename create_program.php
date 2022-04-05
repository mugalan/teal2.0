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

// Require whaever you want 
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/teal/class/form/create_program.php');
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');
require_login();

// Declare the variables
global $DB;
$githubActionHelper = new \GithubActions();

// Set Up the page
$PAGE->set_pagelayout('incourse');
$PAGE->set_url(new moodle_url('/local/teal/create_program.php'));
$PAGE->set_title('Create Program');
$PAGE->set_heading('Create Program');

// Initialize Form
$mform = new CreateProgram();

// Handle Form Actions
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Oops program create cancelled!');
} else if ($fromform = $mform->get_data()) {

    $program = new stdClass();
    $program->program_name = $fromform->program_name;
    $program->program_code = $fromform->program_code;
    $program->program_level = $fromform->program_level;
    $program->program_credits = $fromform->program_credits;
    $program->program_courses = "";

    foreach ($fromform->pcourses as $course) {
        $course = ($githubActionHelper->getCourseDetailsFromCourseId($course))['course'];
        if ($course->global == "0") {
            $course->global = 1;
            $githubActionHelper->sendCourseDataToDatabases($course);
            $DB->update_record('teal_course_metadata', $course);
        }
        $program->program_courses .= $githubActionHelper->getRepoName($course) . ";";
    }
    $program->program_courses =  substr($program->program_courses, 0, -1);

    $DB->insert_record('teal_program_metadata', $program);
    $githubActionHelper->sendProgramDataToDatabases($program);
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Program has been created!');
}

// Render everything
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
