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
$PAGE->set_url(new moodle_url('/local/teal/content.php'));
$PAGE->set_title('Content');

$contents = $DB->get_records('teal_course_content_metadata');
$content = null;
foreach ($contents as $temp_content) {
    if ($temp_content->id == $_GET["id"])
        $content = $temp_content;
}

$PAGE->set_heading($content->content_name);

$repo_name = 'CourseContent_' . $content->content_code . '_' . $content->content_name;
$repo_name = str_replace(' ', '_', $repo_name);
$github = new \GithubActions();
$repo_user = $content->global == 1 ? $github->org_global : $github->username;
$github_url = "https://github.com/" . $repo_user . "/" . $repo_name;
$update_url = new moodle_url("/local/teal/update_content.php");
$data = (object)[
    "content" => $content,
    "github_url" => $github_url,
    "update_url" => $update_url
];

//HTML
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_teal/content', $data);
echo $OUTPUT->footer();
