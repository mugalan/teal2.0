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
$PAGE->set_url(new moodle_url('/local/teal/program.php'));
$PAGE->set_title('Program');

$github = new \GithubActions();

$program = $DB->get_record('teal_program_metadata', ["id" => intval($_GET["id"])]);

$PAGE->set_heading($program->program_name);

$github_url = "https://github.com";

$courses = explode(";", $program->program_courses);
$coursesString = '';
$i = 1;
foreach ($courses as $course) {
    $coursesString .= "{$i}) " . $course . "<br />";
    $i++;
}

$data = (object)[
    "program" => $program,
    "github_url" => $github_url,
    "courses" => $coursesString
];

//HTML
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_teal/program', $data);
echo $OUTPUT->footer();
