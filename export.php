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
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/teal/class/form/export.php');
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');
require_login();

global $DB;

$PAGE->set_pagelayout('incourse');
$PAGE->requires->js('/local/teal/assets/export_course.js');
$PAGE->set_url(new moodle_url('/local/teal/export.php'));
$PAGE->set_title('Export Course');
$PAGE->set_heading('Export Course');
$githubActionHelper = new \GithubActions();

$mform = new export();
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Oops course creation cancelled!');
} else if ($fromform = $mform->get_data()) {
    $course = new stdClass();
    $course->course_id = $fromform->course_id;
    $course->course_code = $fromform->course_code;
    $course->course_category = $fromform->course_category;
    $course->course_type = $fromform->course_type;
    $course->course_credits = (int)$fromform->course_credits;
    $course->course_level = $fromform->course_level;
    $course->course_learning_outcomes = $fromform->course_learning_outcomes;
    $course->global = $fromform->course_db === 'global' ? 1 : 0;
    $course->course_name = get_course($course->course_id)->fullname;
    $DB->insert_record('teal_course_metadata', $course);
    $course->upload_content = $fromform->upload_content;
    $githubActionHelper->sendCourseDataToDatabases($course);
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Course has been exported!');
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
