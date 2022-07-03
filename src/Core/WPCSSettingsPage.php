<?php

namespace WPCSWooSubscriptions\Core;

class WPCSSettingsPage
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_wpcs_admin_page'], 11);
        add_action('admin_init', [$this, 'add_wpcs_admin_settings']);
    }

    public function add_wpcs_admin_page()
    {
        add_menu_page('WPCS.io', 'WPCS.io', 'manage_options', 'wpcs-admin', [$this, 'render_wpcs_admin_page'], 'dashicons-networking', 10);
    }

    public function render_wpcs_admin_page()
    {
        echo '<h1>WPCS.io Admin</h1><form method="POST" action="options.php">';
        settings_fields('wpcs-admin');
        do_settings_sections('wpcs-admin');
        submit_button();
        echo '</form>';
    }

    public function add_wpcs_admin_settings()
    {
        add_settings_section(
            'wpcs_credentials',
            'WPCS Credentials',
            fn() => "<p>Intro text for our settings section</p>",
            'wpcs-admin'
        );

        register_setting('wpcs-admin', 'wpcs_credentials_region_setting');
        add_settings_field(
            'wpcs_credentials_region_setting',
            'WPCS Region',
            [$this, 'render_settings_field'],
            'wpcs-admin',
            'wpcs_credentials',
            [
                "id" => "wpcs_credentials_region_setting",
                "title" => "WPCS Region",
                "type" => "text"
            ]
        );

        register_setting('wpcs-admin', 'wpcs_credentials_api_key_setting');
        add_settings_field(
            'wpcs_credentials_api_key_setting',
            'WPCS API Key',
            [$this, 'render_settings_field'],
            'wpcs-admin',
            'wpcs_credentials',
            [
                "id" => "wpcs_credentials_api_key_setting",
                "title" => "WPCS API Key",
                "type" => "text"
            ]
        );

        register_setting('wpcs-admin', 'wpcs_credentials_api_secret_setting');
        add_settings_field(
            'wpcs_credentials_api_secret_setting',
            'WPCS API Secret',
            [$this, 'render_settings_field'],
            'wpcs-admin',
            'wpcs_credentials',
            [
                "id" => "wpcs_credentials_api_secret_setting",
                "title" => "WPCS API Secret",
                "type" => "password"
            ]
        );


    }

    function render_settings_field($args)
    {
        echo "<input type='{$args["type"]}' id'{$args["id"]}' name='{$args["id"]}' value=" . get_option($args["id"]) . ">";
    }
}



