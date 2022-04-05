<?php

function local_teal_extend_navigation(global_navigation $nav)
{
    $main_node = $nav->add("Manage Teal System", new moodle_url('/local/teal/dashboard/home.php'), navigation_node::TYPE_SETTING,  null, null, new pix_icon('i/cohort', ''));
    $main_node->nodetype = 1;
    $main_node->collapse = false;
    $main_node->force_open = true;
    $main_node->isexpandable = false;
    $main_node->showinflatnavigation = true;
    // $nav is the global navigation instance.
    // Here you can add to and manipulate the navigation structure as you like.
    // This callback was introduced in 2.0 as nicehack_extends_navigation(global_navigation $nav)
    // In 2.3 support was added for local_nicehack_extends_navigation(global_navigation $nav).
    // In 2.9 the name was corrected to local_nicehack_extend_navigation() for consistency
}

function local_teal_extend_navigation_course(navigation_node $nav)
{
    $main_node = $nav->add("Manage Teal", new moodle_url('/local/teal/dashboard/home.php'));
    $main_node->nodetype = 1;
    $main_node->collapse = false;
    $main_node->force_open = true;
    $main_node->isexpandable = false;
    $main_node->showinflatnavigation = true;
    // $nav is the global navigation instance.
    // Here you can add to and manipulate the navigation structure as you like.
    // This callback was introduced in 2.0 as nicehack_extends_navigation(global_navigation $nav)
    // In 2.3 support was added for local_nicehack_extends_navigation(global_navigation $nav).
    // In 2.9 the name was corrected to local_nicehack_extend_navigation() for consistency
}
