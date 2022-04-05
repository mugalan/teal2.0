<?php

/*

TODO: 
Fix username/orgname

*/


require_once($CFG->dirroot . '/local/teal/helpers/github_auth.php');
require_once($CFG->dirroot . '/local/teal/helpers/backup_helper.php');
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');

class GithubActions
{
    public $client;
    public $org_global;
    public $username;
    public $backup_helper;

    function __construct()
    {
        $settings = \GithubAuth::getTealSettings();
        $this->client = new \Github\Client();
        $this->client->authenticate($settings['github_access_token'], null, Github\Client::AUTH_ACCESS_TOKEN);
        $this->org_global = $settings['org_global'];
        $this->username = $this->client->api('me')->show()["login"];
        $this->backup_helper = new \BackupHelper();
    }


    function sendProgramDataToDatabases($program)
    {

        $repo_organization = $this->org_global;
        $repo_name = 'Program_' . $program->program_name . '_' . $program->program_code;
        $repo_name = str_replace(' ', '_', $repo_name);
        $repo_visibility = false;
        $repo_details = "";
        $branch = $this->username;

        $repo = $this->createIntitialRepo(
            $repo_name,
            $repo_details,
            $repo_visibility,
            $repo_organization,
            $branch
        );

        $file = $this->getInitialProgramRepositoryFile($program);
        $commitMessage = 'Adding metadata file for program ' . $program->program_name;

        $fileInfo = $this->client->api('repo')->contents()->create(
            $repo_organization ? $repo_organization : $this->username,
            $repo_name,
            $file['name'],
            $file['content'],
            $commitMessage,
            $branch,
            null // committer
        );
    }

    function getInitialProgramRepositoryFile($program)
    {
        $content = [
            'programName' => $program->program_name,
            'program_code' => $program->program_code,
            'programCredits' => $program->program_credits,
            'programCourses' => $program->program_courses,
            'programObjectives' => '',
            'program_level' => $program->program_level
        ];

        $file = [
            'name' => 'metadata.json',
            'content' => json_encode($content, JSON_PRETTY_PRINT)
        ];
        return $file;
    }

    function sendCourseDataToDatabases($course)
    {
        $course->course_name = get_course($course->course_id)->fullname;
        $repo_organization = $course->global === 1 ? $this->org_global : null;
        $repo_name = 'Course_' . $course->course_code . '_' . $course->course_name;
        $repo_name = str_replace(' ', '_', $repo_name);
        $repo_visibility = $course->global === 1 ? false : false;
        $repo_details = $course->summary;
        $branch = $this->username;

        $repo = $this->createIntitialRepo(
            $repo_name,
            $repo_details,
            $repo_visibility,
            $repo_organization,
            $branch
        );

        $file = $this->getInitialCourseRepositoryFile($course);
        $commitMessage = 'Adding metadata file for course ' . $course->course_name;

        $fileInfo = $this->client->api('repo')->contents()->create(
            $repo_organization ? $repo_organization : $this->username,
            $repo_name,
            $file['name'],
            $file['content'],
            $commitMessage,
            $branch,
            null // committer
        );

        if ($course->upload_content)
            $this->exportCourseContentFromCourse($course);
    }


    function updateCourseDataInDatabases($course_id, $commit_message = "updated file", $branch = null)
    {
        if (!$branch) $branch = $this->username;
        $course_details = $this->getCourseDetailsFromCourseId($course_id);
        $course = $course_details['course'];
        $repo_name = $course_details['repository_name'];
        $repo_organization = $course->global == 1 ? $this->org_global : $this->username;
        $path = "metadata.json";
        $oldFile = $this->client->api('repo')->contents()->show($repo_organization, $repo_name, $path, $branch);
        $file = $this->getInitialCourseRepositoryFile($course);
        $fileInfo = $this->client->api('repo')->contents()->update(
            $repo_organization,
            $repo_name,
            $file["name"],
            $file["content"],
            $commit_message,
            $oldFile['sha'],
            $branch,
            null // committer
        );
    }

    function createIntitialRepo($repo_name, $repo_details, $repo_visibility, $repo_organization, $branch)
    {
        // Create the repo
        $repo = $this->client->api('repo')->create(
            $repo_name,
            $repo_details,
            '',
            $repo_visibility,
            $repo_organization,
            false,
            false,
            false,
            null,
            true,
            true
        );

        // Determine the user for the repo
        $repo_user = $repo_organization ? $repo_organization : $this->username;

        // Get the latest commits
        $commits = $this->client->api('repo')->commits()->all(
            $repo_user,
            $repo_name,
            array('sha' => 'main')
        );

        // Decide on the initial commit and create a new branch 
        $initialCommitSha = $commits[0]['sha'];
        $referenceData = ['ref' => "refs/heads/${branch}", 'sha' => $initialCommitSha];
        $reference = $this->client->api('gitData')->references()->create(
            $repo_user,
            $repo_name,
            $referenceData
        );

        // Return the repository object
        return $repo;
    }

    function createBranchForCommit($dbtype, $repo_name, $commit, $branch = null)
    {
        if (!$branch) $branch = $this->username;

        // Checking if the branch already exists
        $oldBranches = $this->getBranchesForCourse($dbtype, $repo_name);
        foreach ($oldBranches as $oldBranch) {
            $splits = explode("/", $oldBranch["ref"]);
            $oldBranchName = $splits[sizeof($splits) - 1];
            if ($branch == $oldBranchName) return;
        }
        $repo_user = $dbtype === 'global' ? $this->org_global : $this->username;
        $referenceData = ['ref' => "refs/heads/${branch}", 'sha' => $commit];
        $reference = $this->client->api('gitData')->references()->create(
            $repo_user,
            $repo_name,
            $referenceData
        );
    }

    function getRepoName($course)
    {
        $repo_name = 'Course_' . $course->course_code . '_' . $course->course_name;
        $repo_name = str_replace(' ', '_', $repo_name);
        return $repo_name;
    }

    function getCourseDetailsFromCourseId($course_id)
    {
        global $DB;
        $courses = $DB->get_records('teal_course_metadata');
        $course = null;
        foreach ($courses as $temp_course) {
            if ($temp_course->id == $course_id)
                $course = $temp_course;
        }
        $repo_name = $this->getRepoName($course);
        $repo_name = str_replace(' ', '_', $repo_name);
        $repo_user = $course->global == 1 ? $this->org_global : $this->username;
        $github_url = "https://github.com/" . $repo_user . "/" . $repo_name;
        $course_details = [
            "course" => $course,
            "repository_name" => $repo_name,
            "repository_user" => $repo_user,
            "github_url" => $github_url
        ];

        return $course_details;
    }

    function exportCourseContentFromCourse($course)
    {
        global $DB;
        $content = new stdClass();
        $content->course_id = $course->course_id;
        $content->content_code = $course->course_code;
        $content->content_name = $course->course_name;
        $content->global = $course->global;
        $content->description = get_course($course->course_id)->summary;
        if (!$content->description)
            $content->description = '';
        $DB->insert_record('teal_course_content_metadata', $content);
        $this->exportCourseContent($content);
    }

    function exportCourseContent($content)
    {
        $repo_name = 'CourseContent_' . $content->content_code . '_' . $content->content_name;
        $repo_name = str_replace(' ', '_', $repo_name);
        $repo_visibility = $content->global === 1 ? false : false;
        $repo_organization = $content->global === 1 ? $this->org_global : null;
        $branch = $this->username;

        $this->createIntitialRepo($repo_name, $content->description, $repo_visibility, $repo_organization, $branch);

        $file = $this->backup_helper->getBackupFileForCourseWithCourseID($content->course_id);
        $commitMessage = 'Adding course content for course content ' . $content->content_name;
        $fileInfo = $this->client->api('repo')->contents()->create(
            $repo_organization ? $repo_organization : $this->username,
            $repo_name,
            "backup.mdz",
            $file->get_content(),
            $commitMessage,
            $branch,
            null // committer
        );
    }

    function updateCourseContent($content, $commitMessage)
    {
        $repo_name = 'CourseContent_' . $content->content_code . '_' . $content->content_name;
        $repo_name = str_replace(' ', '_', $repo_name);
        $repo_organization = $content->global == 1 ? $this->org_global : $this->username;
        $path = "backup.mdz";
        $branch = $this->username;

        // getting the old file
        $oldFile = $this->client->api('repo')->contents()->show(
            $repo_organization,
            $repo_name,
            $path,
            $branch
        );

        // getting the backup file
        $file = $this->backup_helper->getBackupFileForCourseWithCourseID($content->course_id);
        $fileInfo = $this->client->api('repo')->contents()->update(
            $repo_organization,
            $repo_name,
            $path,
            $file->get_content(),
            $commitMessage,
            $oldFile['sha'],
            $branch,
            null // committer
        );
    }

    function getInitialCourseRepositoryFile($course)
    {
        $content = [
            'courseName' => $course->course_name,
            'courseCode' => $course->course_code,
            'courseCategory' => $course->course_category,
            'courseType' => $course->course_type,
            'courseCredits' => $course->course_credits,
            'courseLevel' => $course->course_level,
            'courseLearningOutcomes' => explode(";", $course->course_learning_outcomes)
        ];
        $file = [
            'name' => 'metadata.json',
            'content' => json_encode($content, JSON_PRETTY_PRINT)
        ];
        return $file;
    }

    function getCourseRepositoriesNames($fromGlobal)
    {

        $repos = null;
        if ($fromGlobal === true) {
            $repos = $this->client->api('repo')->org($this->org_global, ['per_page' => 100]);
        } else {
            $repos = $this->client->api('current_user')->repositories();
        }

        $repo_names = array();
        foreach ($repos as $repo) {
            array_push($repo_names, $repo['name']);
        }

        return $repo_names;
    }

    function getBranchesForCourse($dbtype, $repoName)
    {
        $repo_organization = $dbtype === 'global' ? $this->org_global : $this->username;
        $branches = $this->client->api('gitData')->references()->branches($repo_organization, $repoName);
        return $branches;
    }

    function getCommitsForBranches($branch, $repoName, $dbtype)
    {
        $repo_organization = $dbtype === 'global' ? $this->org_global : $this->username;
        $commits = $this->client->api('repo')->commits()->all($repo_organization, $repoName, array('sha' => $branch));
        return $commits;
    }

    function getCourseFromCourseName($commit, $repoName, $dbtype)
    {
        $repo_organization = $dbtype === 'global' ? $this->org_global : $this->username;
        $raw_content = $this->client->api('repo')->contents()->show(
            $repo_organization,
            $repoName,
            'metadata.json',
            $commit
        )["content"];
        $decoded_content = json_decode(base64_decode($raw_content), true);
        return $decoded_content;
    }

    function getBackupFromCourseContent($repoName, $commit, $dbtype = 'global')
    {
        $repo_organization = $dbtype === 'global' ? $this->org_global : $this->username;
        $raw_content = $this->client->api('repo')->contents()->show(
            $repo_organization,
            $repoName,
            'backup.mdz',
            $commit
        )["content"];

        return base64_decode($raw_content);
    }
}
