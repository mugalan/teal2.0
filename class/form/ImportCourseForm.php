<?php


/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     @abhiandthetruth, @thesmallstar
 */

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');

class ImportCourseForm extends moodleform
{

    //Add elements to form
    public function definition()
    {
        $form = $this->_form;

        // select local/global course type  
        $option_types = ['none' => 'none', 'global' => 'global', 'local' => 'local'];
        $form->addElement('select', 'database', 'Course Database', $option_types);
        $form->setDefault('database', ['none']);

        $options = [];

        $form->addElement('select', 'course', 'Course', $options);
        $form->setDefault('course', [0]);
        $form->addElement('hidden', 'repo_name', '');
        $form->addElement('select', 'branch', 'Institute(Branch)', $options);
        $form->addElement('hidden', 'branch_hid', '');
        $form->addElement('select', 'commit', 'Version(Commit)', $options);
        $form->addElement('hidden', 'commit_hid', '');

        $form->addElement('text', 'code', 'Course Code');
        $form->addElement('text', 'name', 'Course Name');
        $form->addElement('text', 'type', 'Course Type');
        $form->addElement('text', 'category', 'Course Category');
        $form->addElement('text', 'level', 'Course Level');
        $form->addElement('text', 'credits', 'Course Credits');
        $form->addElement('hidden', 'learning_outcomes', '');

        // Add Submit and Cancel button
        $this->add_action_buttons();
    }
}
