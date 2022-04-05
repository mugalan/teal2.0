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
require_once($CFG->dirroot . '/local/teal/class/form/import_content.php');
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');
require_once($CFG->dirroot . '/local/teal/helpers/backup_helper.php');
require_login();

global $DB;

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/import_content.php'));
$PAGE->set_title('Import Content');
$PAGE->set_heading('Import Content');
$PAGE->requires->js('/local/teal/assets/import_content.js');
$githubActionHelper = new \GithubActions();

$mform = new ImportContent();
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Oops course content import cancelled!');
} else if ($fromform = $mform->get_data()) {
    $importCourseFromContent = new stdClass();
    $importCourseFromContent->course_content =  $fromform->course_content;
    $importCourseFromContent->course_id = $fromform->course_id;
    $importCourseFromContent->commit = $fromform->commit_hid;
    $importCourseFromContent->branch = $fromform->branch_hid;
    $githubActionHelper->createBranchForCommit(
        $fromform->database,
        $fromform->course_content,
        $fromform->commit_hid,
    );
    $restore_url = (new \BackupHelper())->handleCourseImport($importCourseFromContent);
    if ($restore_url != "") {
        redirect($restore_url);
    }
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Course content has been imported!');
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
