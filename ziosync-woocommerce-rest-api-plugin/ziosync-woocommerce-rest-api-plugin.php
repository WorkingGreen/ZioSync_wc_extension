<?php
    /**
    *    Plugin Name: ZioSync - Woocommerce REST API Plugin
    *    Description: ZioSync - Woocommerce REST API Plugin
    *    Author: ZioSync
    *    Plugin URI: https://ziosync.com
    *    Author URI: https://ziosync.com
    *    Version: 1.0
    *    Text Domain: ziosync-woocommerce
    *    Requires PHP: 7.2
    *    Requires at least: 5.8
    */

    namespace ZioSync;

    require_once __DIR__.'/inc/ziosync.php';

    if (!defined('ABSPATH')) {
        exit;
    }

    

    if (!is_plugin_active('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action('admin_notices', 'ZioSync\ziosync_no_woocommerce_notice');
        return;
    }

    add_action('rest_api_init', 'ZioSync\ZioSync::init');


    function ziosync_no_woocommerce_notice(){
        ?>
            <div class="error notice">
                <p><b><?php _e('ZioSync - Woocommerce REST API Plugin requires WooCommerce to be installed and active.', 'ziosync-woocommerce'); ?></b></p>
            </div>
        <?php
    }

    /**
     * ! CUSTOM FILTERS
     */
    // Filter to add modified_after filter to the listing of all customers endpoints
    function add_modified_after_filter_to_rest_api($prepared_args, $request)
    {
        if($request->get_param('modified_after')){
            $prepared_args['meta_query'] = array(
                array(
                    'key'     => 'last_update',
                    'value'   => (int) strtotime($request->get_param('modified_after')),
                    'compare' => '>=',
                ),
            );
        }

        // Allow sorting by modified_date
        if ($request->get_param('orderby') === 'modified_date') {
            $prepared_args['orderby'] = 'meta_value';
            $prepared_args['meta_key'] = 'last_update'; // Ensure the key exists in meta
        }

        return $prepared_args;
    }
    add_filter('woocommerce_rest_customer_query', 'ZioSync\add_modified_after_filter_to_rest_api', 10, 2);

    // Allow 'modified_date' as a valid orderby value
    function add_custom_orderby_to_rest_api($orderby_values) {
        $orderby_values[] = 'modified_date';
        return $orderby_values;
    }
    add_filter('woocommerce_rest_customers_collection_params', 'ZioSync\add_custom_orderby_to_rest_api');