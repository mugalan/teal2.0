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
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');

class UpdateContent extends moodleform
{
    public $id;

    function __construct($id)
    {
        $this->id = $id;
        parent::__construct();
    }

    //Add elements to form
    public function definition()
    {
        global $DB;
        $mform = $this->_form;
        $content = $DB->get_record('teal_course_content_metadata', ["id" => $this->id]);

        // Content code field
        $mform->addElement('static', 'content_code', 'Content Code', $content->content_code);
        $mform->addElement('hidden', 'id', $content->id);

        // Version Comment
        $mform->addElement('text', 'message', 'Version Message');

        // Add Submit and Cancel button
        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
