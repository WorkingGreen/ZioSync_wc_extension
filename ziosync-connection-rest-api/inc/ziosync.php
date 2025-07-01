<?php
    namespace ZioSync;

    

    if (!defined('ABSPATH')) {
        exit;
    }

    final class ZioSync{

        public static function include(){
            require_once(__DIR__.'/customers.php');
            require_once(__DIR__.'/orders.php');
            require_once(__DIR__.'/payment-methods.php');
            require_once(__DIR__.'/product-attributes.php');
            require_once(__DIR__.'/product-attribute-terms.php');
            require_once(__DIR__.'/product-variations.php');
            require_once(__DIR__.'/products.php');
            require_once(__DIR__.'/refunds.php');
            require_once(__DIR__.'/shipping-methods.php');
            require_once(__DIR__.'/tax.php');
            require_once(__DIR__.'/data.php');
        }

        public static function instances(){
            new Products;
            new Orders;
            new PaymentMethods;
            new ProductAttributes;
            new ProductAttributeTerms;
            new ProductVariations;
            new Refunds;
            new ShippingMethods;
            new Tax;
            new Customers;
            new Data;
        }

        public static function init(){
            self::include();
            self::instances();

            add_action('rest_api_init', function () {
                register_rest_route(
                    'wc-ziosync/'.self::version(),
                    'ping',
                    array(
                        'methods'             => 'GET',
                        'callback'            => array($this, 'version'),
                        'permission_callback' => '__return_true',
                    )
                );
            });
        }

        public static function version(){
            return 'v1';
        }
    }