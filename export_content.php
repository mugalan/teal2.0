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

// Require whaever you want 
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/teal/class/form/export_content.php');
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');
require_login();

// Declare the variables
global $DB;
$githubActionHelper = new \GithubActions();

// Set Up the page
$PAGE->set_pagelayout('incourse');
$PAGE->set_url(new moodle_url('/local/teal/export_content.php'));
$PAGE->set_title('Export Content');
$PAGE->set_heading('Export Content');

// Initialize Form
$mform = new ExportContent();

// Handle Form Actions
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Oops content export cancelled!');
} else if ($fromform = $mform->get_data()) {
    $course = get_course($fromform->course_id);

    $content = new stdClass();
    $content->course_id = $fromform->course_id;
    $content->content_code = $fromform->content_code;
    $content->description = $fromform->description;
    $content->content_name = $course->fullname;
    $content->global = 1;

    $DB->insert_record('teal_course_content_metadata', $content);
    $githubActionHelper->exportCourseContent($content);
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Course Content has been exported!');
}

// Render everything
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
