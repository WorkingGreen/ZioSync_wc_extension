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

        return $prepared_args;
    }
    add_filter('woocommerce_rest_customer_query', 'ZioSync\add_modified_after_filter_to_rest_api', 10, 2);

    function add_orderby_modified_filter_to_rest_api($prepared_args, $request)
    {
        if($request->get_param('orderby_modified')){

            // check if the orderby_modified is set to either asc or desc, otherwise set it to asc
            if($request->get_param('orderby_modified') !== 'asc' && $request->get_param('orderby_modified') !== 'desc') {
                $request->set_param('orderby_modified', 'asc');
            }
            $prepared_args['order'] = $request->get_param('orderby_modified');
            $prepared_args['orderby'] = 'meta_value';
            $prepared_args['meta_key'] = 'last_update'; // Ensure the key exists in meta
        }

        return $prepared_args;
    }
    add_filter('woocommerce_rest_customer_query', 'ZioSync\add_orderby_modified_filter_to_rest_api', 10, 2);