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
