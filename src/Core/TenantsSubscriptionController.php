<?php

namespace WPCSWooSubscriptions\Core;

use WPCS\API\CreateTenantRequest;
use WPCS\API\DeleteTenantRequest;
use WPCS\API\GetTenantsRequest;

class TenantsSubscriptionController
{
    public const WPCS_WEBSITE_NAME = 'WPCS_WEBSITE_NAME';

    public function __construct()
    {
        add_filter('woocommerce_checkout_fields', [$this, 'render_wpcs_checkout_fields']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'add_wpcs_checkout_fields']);


        add_action('wps_sfw_after_created_subscription', [$this, 'create_tenant_when_subscription_created'], 10, 2);

        add_action('wps_sfw_subscription_cancel', [$this, 'remove_tenant_when_subscription_expired']);
        // add_action('wps_sfw_expire_subscription_scheduler', [$this, 'remove_tenant_when_subscription_expired']);
    }

    public function render_wpcs_checkout_fields($fields)
    {
        $fields['billing'][TenantsSubscriptionController::WPCS_WEBSITE_NAME] = [
            'label' => 'Website Name',
            'required' => true,
            'priority' => 20,
        ];

        return $fields;
    }

    function add_wpcs_checkout_fields($order_id)
    {
        update_post_meta($order_id, TenantsSubscriptionController::WPCS_WEBSITE_NAME, sanitize_text_field($_POST[TenantsSubscriptionController::WPCS_WEBSITE_NAME]));
    }

    public function create_tenant_when_subscription_created($subscription_id, $order_id)
    {
        $order = new \WC_Order($order_id);
        $product = reset($order->get_items());
        $wpcs_version_id = get_post_meta($product->get_product_id(), WooCommerceMetaBoxes::WPCS_PRODUCT_VERSION, true);
        $websiteName = get_post_meta($order_id, TenantsSubscriptionController::WPCS_WEBSITE_NAME, true);
        $password = wp_generate_password();

        $response = (new CreateTenantRequest())
            ->setVersionId($wpcs_version_id)
            ->setTenantName($order->get_formatted_billing_full_name())
            ->setTenantEmail($order->get_billing_email())
            ->setTenantPassword($password)
            ->setName($websiteName)
            ->setTenantUserRole('administrator')
            ->send();

        update_post_meta($subscription_id, 'WPCS_TENANT_ID', $response->id);

        echo $response->id;

        sleep(5);

        $tenant = (new GetTenantsRequest())
            ->setTenantId($response->id)
            ->send();

        $tenant = $tenant[0];

        wp_mail( $order->get_billing_email(), 'Your website is being created', "
        <div>
        <p>Hello,</p>
        <p>You can now login here to your new website</p>
        <p><strong>Admin Url</strong>: <a href='https://{$tenant->domainName}/wp-admin'>https://{$tenant->domainName}/wp-admin</a></p>
        <p><strong>Email</strong> : {$order->get_billing_email()}</p>
        <p><strong>Password</strong> : {$password}</p>
        </div>
        ");
    }

    public function remove_tenant_when_subscription_expired($subscription_id)
    {
        $tenant_id = get_post_meta($subscription_id, 'WPCS_TENANT_ID', true);
        (new DeleteTenantRequest())
            ->setTenantId($tenant_id)
            ->send();
    }
}