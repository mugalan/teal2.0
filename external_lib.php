<?php

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . '/local/teal/helpers/github_actions.php');

class ExternalCallHelper extends external_api
{

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function getCoursesFromDB_parameters()
    {
        return new external_function_parameters(
            array("db_type" => new external_value(PARAM_TEXT, "db_type"))
        );
    }

    /**
     * Returns welcome message
     * @return array = array('' => , ); welcome message
     */
    public static function  getCoursesFromDB($db_type)
    {
        if ($db_type == 'none') {
            return "[]";
        }

        $isGlobal = $db_type == 'global' ? true : false;
        $githubActions = new \GithubActions();
        $repoNames = $githubActions->getCourseRepositoriesNames($isGlobal);
        return json_encode(array_values($repoNames));
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function  getCoursesFromDB_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
        // return new external_value();//new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function getCourse_parameters()
    {
        return new external_function_parameters(
            array(
                "commit" => new external_value(PARAM_TEXT, "commit"),
                "selected_course" => new external_value(PARAM_TEXT, "selected_course"),
                "db_type" => new external_value(PARAM_TEXT, "db_type")
            )
        );
    }

    /**
     * Returns welcome message
     * @return array = array('' => , ); welcome message
     */
    public static function  getCourse($commit, $selected_course, $db_type)
    {
        $githubActions = new \GithubActions();
        $course = $githubActions->getCourseFromCourseName($commit, $selected_course, $db_type);
        return json_encode($course);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function  getCourse_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
        // return new external_value();//new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function getBranchesForCourse_parameters()
    {
        return new external_function_parameters(
            array(
                "selected_course" => new external_value(PARAM_TEXT, "selected_course"),
                "db_type" => new external_value(PARAM_TEXT, "db_type")
            )
        );
    }

    /**
     * Returns welcome message
     * @return array = array('' => , ); welcome message
     */
    public static function  getBranchesForCourse($selected_course, $db_type)
    {
        $githubActions = new \GithubActions();
        $branches = $githubActions->getBranchesForCourse($db_type, $selected_course);
        return json_encode($branches);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function  getBranchesForCourse_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
        // return new external_value();//new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }


    public static function getCommitsForBranches_parameters()
    {
        return new external_function_parameters(
            array(
                "selected_branch" => new external_value(PARAM_TEXT, "selected_branch"),
                "selected_course" => new external_value(PARAM_TEXT, "selected_course"),
                "db_type" => new external_value(PARAM_TEXT, "db_type")
            )
        );
    }

    /**
     * Returns welcome message
     * @return array = array('' => , ); welcome message
     */
    public static function  getCommitsForBranches($selected_branch, $selected_course, $db_type)
    {
        $githubActions = new \GithubActions();
        $commits = $githubActions->getCommitsForBranches($selected_branch, $selected_course, $db_type);
        return json_encode($commits);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function  getCommitsForBranches_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
        // return new external_value();//new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }
}
