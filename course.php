<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * TODO: global vs local
 */

/**
 * @package     local_teal
 * @author      abhiandthetruth
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * 
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');
require_login();
global $OUTPUT, $DB;

$PAGE->set_pagelayout('incourse');
$PAGE->set_url(new moodle_url('/local/teal/course.php'));
$PAGE->set_title('Course');

$github = new \GithubActions();

$course_details = $github->getCourseDetailsFromCourseId($_GET["id"]);
$course = $course_details["course"];

$PAGE->set_heading($course->course_name);

$github_url = $course_details["github_url"];

$learning_outcomes = explode(";", $course->course_learning_outcomes);
$learningOutcomeString = '';
$i = 1;
foreach ($learning_outcomes as $outcome) {
    $learningOutcomeString .= "{$i}) " . $outcome . "<br />";
    $i++;
}

$update_url = new moodle_url("/local/teal/update_course.php");
$data = (object)[
    "course" => $course,
    "github_url" => $github_url,
    "update_url" => $update_url->__toString(),
    "learning_outcomes" => $learningOutcomeString
];

//HTML
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_teal/course', $data);
echo $OUTPUT->footer();
