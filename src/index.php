<?php
require_once 'vendor/autoload.php';

use WPCSWooSubscriptions\Core\WPCSSettingsPage;
use WPCSWooSubscriptions\Core\VersionsService;
use WPCSWooSubscriptions\Core\TenantsService;
use WPCSWooSubscriptions\Core\TenantsSubscriptionController;
use WPCSWooSubscriptions\Core\WooCommerceMetaBoxes;

/**
 * @package WPCSWooSubscriptions
 * @version 1.0.0
 */
/*
Plugin Name: WPCS WooCommerce Subscriptions
Plugin URI: https://github.com/abduvik/wp-shopify-clone
Description: This plugin is used to create tenants on WPCS.io with support of WordPress, WooCommerce and YITH Subscriptions
Author: Abdu Tawfik
Version: 1.0.0
Author URI: https://www.abdu.dev
*/

define('WPCS_API_REGION', get_option('wpcs_credentials_region_setting')); // Or eu1, depending on your region.
define('WPCS_API_KEY', get_option('wpcs_credentials_api_key_setting')); // The API Key you retrieved from the console
define('WPCS_API_SECRET', get_option('wpcs_credentials_api_secret_setting')); // The API Secret you retrieved from the console

$tenantsService = new TenantsService();
new TenantsSubscriptionController($tenantsService);

new WPCSSettingsPage();

$versionsService = new VersionsService();
new WooCommerceMetaBoxes($versionsService);
