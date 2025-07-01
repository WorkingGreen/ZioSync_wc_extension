<?php
    /**
    *    Plugin Name: ZioSync - Woocommerce REST API
    *    Description: ZioSync - Woocommerce REST API
    *    Author: ZioSync
    *    Plugin URI: https://ziosync.com
    *    Author URI: https://ziosync.com
    *    Version: 1.0
    *    Text Domain: ziosync-woocommerce-rest-api
    *    Requires PHP: 7.2
    *    Requires at least: 5.8
    *    License URI: https://www.gnu.org/licenses/gpl-2.0.html
    *    Requires Plugins: woocommerce
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
                <p><b><?php esc_html_e('ZioSync - Woocommerce REST API Plugin requires WooCommerce to be installed and active.', 'ziosync-woocommerce-rest-api'); ?></b></p>
            </div>
        <?php
    }

    /**
     * ! CUSTOM FILTERS
     */
    // Filter to add modified_after filter to the listing of all customers endpoints
    function add_modified_after_filter_to_rest_api($prepared_args, $request)
{
    global $wpdb;

    if ($request->get_param('modified_after')) {
        $timestamp = (int) strtotime($request->get_param('modified_after'));

        // Fetch user IDs **before** running WP_User_Query
        $user_ids = $wpdb->get_col($wpdb->prepare("
            SELECT user_id FROM {$wpdb->usermeta} 
            WHERE meta_key = 'last_update' AND meta_value >= %d", 
            $timestamp
        ));

        if (!empty($user_ids)) {
            $prepared_args['include'] = $user_ids; // Filter only relevant users
        } else {
            $prepared_args['include'] = array(0);
        }
    }

    return $prepared_args;
}
//add_filter('woocommerce_rest_customer_query', 'ZioSync\add_modified_after_filter_to_rest_api', 10, 2);


function add_orderby_modified_filter_to_rest_api($prepared_args, $request)
{
    global $wpdb;

    if ($request->get_param('orderby_modified')) {
        if (!in_array($request->get_param('orderby_modified'), ['asc', 'desc'])) {
            $request->set_param('orderby_modified', 'asc');
        }
        $order = strtoupper($request->get_param('orderby_modified'));

        // Fetch sorted user IDs **before** running WP_User_Query
        $user_ids = $wpdb->get_col("
            SELECT user_id FROM {$wpdb->usermeta} 
            WHERE meta_key = 'last_update' 
            ORDER BY meta_value $order
            LIMIT 1000
        ");

        if (!empty($user_ids)) {
            $prepared_args['include'] = $user_ids;
        } else {
            $prepared_args['include'] = array(0);
        }
    }

    return $prepared_args;
}
//    add_filter('woocommerce_rest_customer_query', 'ZioSync\add_orderby_modified_filter_to_rest_api', 10, 2);