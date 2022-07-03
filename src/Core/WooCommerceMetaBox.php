<?php

use WPCSWooSubscriptions\Core\VersionsService;

class WooCommerceMetaBox {
    public function __construct(VersionsService $versionsService)
    {
        add_action('admin_menu', [$this, 'add_wpcs_admin_page'], 11);
        add_action('admin_init', [$this, 'add_wpcs_admin_settings']);

        add_action('add_meta_boxes', [$this, 'create_woocommerce_wpcs_versions_selector']);
        add_action('save_post', [$this, 'save_woocommerce_wpcs_versions_selector']);
    }
}