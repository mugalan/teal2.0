<?php

class GithubAuth
{

    public static function getTealSettings()
    {
        global $DB;
        $settings = $DB->get_records('teal_settings');
        $settings_map = [];
        foreach ($settings as $setting) {
            $settings_map[$setting->name] = $setting->value;
        }
        return $settings_map;
    }
}
