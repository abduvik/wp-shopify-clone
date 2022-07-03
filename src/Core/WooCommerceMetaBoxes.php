<?php
namespace WPCSWooSubscriptions\Core;

class WooCommerceMetaBoxes {
    private VersionsService $versionsService;
    public const WPCS_PRODUCT_VERSION = 'WPCS_PRODUCT_VERSION';

    public function __construct(VersionsService $versionsService)
    {
        $this->versionsService = $versionsService;

        add_action('add_meta_boxes', [$this, 'create_woocommerce_wpcs_versions_selector']);
        add_action('save_post', [$this, 'save_woocommerce_wpcs_versions_selector']);
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
        $versions = $this->versionsService->getAll();

        $available_versions = array_filter($versions, function ($version) {
            return $version->statusName === 'Done';
        });

        $current_version = get_post_meta($post->ID, static::WPCS_PRODUCT_VERSION, true);

        echo '<label for="wporg_field">WPCS Version</label>';
        echo "<select name=" . static::WPCS_PRODUCT_VERSION . " class='postbox'>";
        foreach ($available_versions as $version) {
            echo selected($version->name, $current_version);
            echo "<option " . selected($version->id, $current_version) . "value='$version->id'>$version->name</option>";
        }
        echo '</select>';
    }

    public function save_woocommerce_wpcs_versions_selector($post_id)
    {
        if (array_key_exists(static::WPCS_PRODUCT_VERSION, $_POST) && $_POST['post_type'] === 'product') {
            update_post_meta(
                $post_id,
                static::WPCS_PRODUCT_VERSION,
                $_POST[static::WPCS_PRODUCT_VERSION]
            );
        }
    }
}