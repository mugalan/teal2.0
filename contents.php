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
$PAGE->set_url(new moodle_url('/local/teal/contents.php'));
$PAGE->set_title('Contents');
$PAGE->set_context(\context_system::instance());
$PAGE->set_heading('Contents');

// Getting all the locally present contents
$contents = $DB->get_records('teal_course_content_metadata');

//HTML
$content_url = new moodle_url("/local/teal/content.php");
echo $OUTPUT->header();
$data = (object)[
    "contents" => array_values($contents),
    "content_url" => $content_url->__toString()
];
echo $OUTPUT->render_from_template('local_teal/contents', $data);
echo $OUTPUT->footer();
