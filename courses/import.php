<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     @abhiandthetruth, @thesmallstar
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/teal/class/form/ImportCourseForm.php');
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');
require_once($CFG->dirroot . '/course/lib.php');
require_login();

global $DB;

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/courses/import.php'));
$PAGE->set_title('Import Course');
$PAGE->set_heading('Import Course');
$PAGE->requires->js('/local/teal/assets/courses/import.js');

$form = new ImportCourseForm();
if ($form->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Oops course import cancelled!');
} else if ($fromform = $form->get_data()) {
    $moodle_course_data = new stdClass();

    // Create moodle course
    $moodle_course_data->fullname = $fromform->course_name;
    $moodle_course_data->shortname = $fromform->course_code;
    $moodle_course_data->category = '1'; // TODO - Constants
    $created_course = create_course($moodle_course_data);

    // Create database entry
    $course = new stdClass();
    $course->id = $created_course->id;
    $course->code = $fromform->code;
    $course->category = $fromform->category;
    $course->type = $fromform->type;
    $course->credits = (int)$fromform->credits;
    $course->level = $fromform->level;
    $course->name = $created_course->fullname;
    $course->learning_outcomes = $fromform->learning_outcomes;
    $course->is_global = 1; // TODO - Constants Currently setting all courses as global, local to be supported in future release
    $id = $DB->insert_record('teal_course_metadata', $course);

    // Handle Github Update
    $githubActionHelper = new \GithubActions();
    $githubActionHelper->createBranchForCommit($fromform->database, $fromform->repo_name, $fromform->commit_hid);
    $githubActionHelper->updateCourseDataInDatabases($id, "Initial Update");
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Course has been imported!');
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();
