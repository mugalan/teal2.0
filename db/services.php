<?php

// We defined the web service functions to install.
$functions = array(
    'external_calls_helpers_getCoursesFromDB' => array(
        'classname' => 'ExternalCallHelper',
        'methodname' => 'getCoursesFromDB',
        'classpath' => 'local/teal/external_lib.php',
        'description' => 'External Calls Helper Service',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true
    ),
    'external_calls_helpers_getCourse' => array(
        'classname' => 'ExternalCallHelper',
        'methodname' => 'getCourse',
        'classpath' => 'local/teal/external_lib.php',
        'description' => 'External Calls Helper Service',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true
    ),
    'external_calls_helpers_getBranchesForCourse' => array(
        'classname' => 'ExternalCallHelper',
        'methodname' => 'getBranchesForCourse',
        'classpath' => 'local/teal/external_lib.php',
        'description' => 'External Calls Helper Service',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true
    ),
    'external_calls_helpers_getCommitsForBranches' => array(
        'classname' => 'ExternalCallHelper',
        'methodname' => 'getCommitsForBranches',
        'classpath' => 'local/teal/external_lib.php',
        'description' => 'External Calls Helper Service',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true
    )

);
