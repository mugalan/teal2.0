<?php

require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/converter/convertlib.php');
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');

class BackupHelper
{
    function getBackupFileForCourseWithCourseID($id)
    {
        global $USER;
        $course_module_to_backup = $id; // Set this to one existing choice cmid in your dev site
        $user_doing_the_backup   = $USER->id; // Set this to the id of your admin account

        $bc = new backup_controller(
            backup::TYPE_1COURSE,
            $course_module_to_backup,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_YES,
            backup::MODE_GENERAL,
            $user_doing_the_backup
        );

        // Set the default filename.
        $format = $bc->get_format();
        $type = $bc->get_type();
        $id = $bc->get_id();
        $users = $bc->get_plan()->get_setting('users')->get_value();
        $anonymised = $bc->get_plan()->get_setting('anonymize')->get_value();
        $filename = backup_plan_dbops::get_default_backup_filename($format, $type, $id, $users, $anonymised);
        $bc->get_plan()->get_setting('filename')->set_value($filename);

        // Execution.
        $bc->finish_ui();
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        return $file;
    }

    function handleCourseImport($courseAndContentID)
    {
        $course_id = $courseAndContentID->course_id;
        $course_content_repo_name = $courseAndContentID->course_content;
        $backup = (new \GithubActions())->getBackupFromCourseContent(
            $course_content_repo_name, // Repository Name
            $courseAndContentID->commit // commit 
        );


        $fs = get_file_storage();

        // Prepare file record object
        echo $course_id;
        $ctxID = CONTEXT_COURSE::instance($course_id)->id;
        $fileinfo = array(
            'contextid' => $ctxID, // ID of context
            'component' => 'mod_mymodule',     // usually = table name
            'filearea' => 'myarea',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => '/',           // any path beginning and ending in /
            'filename' => 'backup' . time() . ".mdz"
        ); // any filename

        // Create file containing text 'hello world'
        $file = $fs->create_file_from_string($fileinfo, $backup);
        $restore_url = new moodle_url('/backup/restore.php', array(
            'contextid'    =>    $ctxID,
            'pathnamehash' => $file->get_pathnamehash(), 'contenthash' => $file->get_contenthash()
        ));

        return $restore_url;
    }
}
