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
require_login();

global $DB;

// Settings up the page
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/courses.php'));
$PAGE->set_title('Courses');
$PAGE->set_context(\context_system::instance());
$PAGE->set_heading('Courses');

// Getting all the locally present courses
$synced_courses = $DB->get_records('teal_course_metadata');
$moodle_courses = get_courses();

//HTML
$course_url = new moodle_url("/local/teal/course.php");
echo $OUTPUT->header();
$data = (object)[
    "courses" => array_values($synced_courses),
    "course_url" => $course_url->__toString()
];
echo $OUTPUT->render_from_template('local_teal/courses', $data);
echo $OUTPUT->footer();
