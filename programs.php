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
$PAGE->set_url(new moodle_url('/local/teal/programs.php'));
$PAGE->set_title('Programs');
$PAGE->set_context(\context_system::instance());
$PAGE->set_heading('Programs');

// Getting all the locally present courses
$programs = $DB->get_records('teal_program_metadata');

//HTML
$program_url = new moodle_url("/local/teal/program.php");
echo $OUTPUT->header();
$data = (object)[
    "programs" => array_values($programs),
    "program_url" => $program_url->__toString()
];
echo $OUTPUT->render_from_template('local_teal/programs', $data);
echo $OUTPUT->footer();
