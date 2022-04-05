<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     @abhiandthetruth, @thesmallstar
 */


require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/teal/class/form/SettingsForm.php');
require_login();
global $DB;

// Settings up the page
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/dashboard/home.php'));
$PAGE->set_title('TEAL Home');
$PAGE->set_context(\context_system::instance());
$PAGE->set_heading('TEAL Home');

// Initializing the form
$form = new SettingsForm();

// Manging form responses
if ($form->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Oops course creation cancelled!');
} else if ($fromform = $form->get_data()) {
    foreach ($fromform as $id => $value) {
        if ($id !== "submitbutton")
            $DB->update_record('teal_settings', (object)["id" => $id, "value" => $value]);
    }
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Setting has been updated!');
}

$export_url = new moodle_url("/local/teal/export.php");
$import_url = new moodle_url("/local/teal/courses/import.php");
$courses_url = new moodle_url("/local/teal/courses.php");
$contents_url = new moodle_url("/local/teal/contents.php");
$programs_url = new moodle_url("/local/teal/programs.php");
$create_program_url = new moodle_url("/local/teal/create_program.php");
$import_content_url = new moodle_url("/local/teal/import_content.php");
$export_content_url = new moodle_url("/local/teal/export_content.php");

$data = (object)[
    "export_url" => $export_url->__toString(),
    "import_url" => $import_url->__toString(),
    "courses_url" => $courses_url->__toString(),
    "contents_url" => $contents_url->__toString(),
    "programs_url" => $programs_url->__toString(),
    "create_program_url" => $create_program_url->__toString(),
    "import_content_url" => $import_content_url->__toString(),
    "export_content_url" => $export_content_url->__toString(),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_teal/home', $data);

$form->display();

echo $OUTPUT->footer();
