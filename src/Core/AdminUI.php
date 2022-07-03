<?php

namespace WPCSWooSubscriptions\Core;

use WPCS\API\CreateTenantRequest;

class AdminUI
{
    public const WPCS_PRODUCT_VERSION = 'WPCS_PRODUCT_VERSION';

    public function __construct(VersionsService $productsService)
    {
        add_action('admin_menu', [$this, 'add_wpcs_admin_page'], 11);
        add_action('admin_init', [$this, 'add_wpcs_admin_settings']);

        add_action('add_meta_boxes', [$this, 'create_woocommerce_wpcs_versions_selector']);
        add_action('save_post', [$this, 'save_woocommerce_wpcs_versions_selector']);
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

    public function create_woocommerce_wpcs_versions_selector()
    {
        add_meta_box(
            'wpcs_product_version_selector',
            'WPCS Version Selector',
            [$this, 'render_woocommerce_wpcs_versions_selector'],
            'product',
            'side',
            'high'
        );
    }

    public function render_woocommerce_wpcs_versions_selector($post)
    {
        $response = wp_remote_get('https://api.' . WPCS_API_REGION .'.wpcs.io/v1/versions', [
            'headers' => [
                'Authorization' => "Basic " . base64_encode(WPCS_API_KEY . ":" . WPCS_API_SECRET),
            ]
        ]);

        $versions = json_decode($response['body']);

        $available_versions = array_filter($versions, function ($version) {
            return $version->statusName === 'Done';
        });

        $current_version = get_post_meta($post->ID, AdminUI::WPCS_PRODUCT_VERSION, true);

        echo '<label for="wporg_field">WPCS Version</label>';
        echo "<select name=" . AdminUi::WPCS_PRODUCT_VERSION . " class='postbox'>";
        foreach ($available_versions as $version) {
            echo selected($version->name, $current_version);
            echo "<option " . selected($version->id, $current_version) . "value='$version->id'>$version->name</option>";
        }
        echo '</select>';
    }

    public function save_woocommerce_wpcs_versions_selector($post_id)
    {
        if (array_key_exists(AdminUI::WPCS_PRODUCT_VERSION, $_POST) && $_POST['post_type'] === 'product') {
            update_post_meta(
                $post_id,
                AdminUI::WPCS_PRODUCT_VERSION,
                $_POST[AdminUI::WPCS_PRODUCT_VERSION]
            );
        }
    }
}



