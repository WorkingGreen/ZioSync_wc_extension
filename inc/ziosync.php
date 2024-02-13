<?php
    namespace ZioSync;

    

    if (!defined('ABSPATH')) {
        exit;
    }

    final class ZioSync{

        public static function include(){
            require_once(__DIR__.'/customers.php');
            require_once(__DIR__.'/orders.php');
//            require_once(__DIR__.'/OrdersGetWithoutMetaData.php');
            require_once(__DIR__.'/payment-methods.php');
            require_once(__DIR__.'/product-attributes.php');
            require_once(__DIR__.'/product-attribute-terms.php');
            require_once(__DIR__.'/product-variations.php');
            require_once(__DIR__.'/products.php');
            require_once(__DIR__.'/refunds.php');
            require_once(__DIR__.'/shipping-methods.php');
            require_once(__DIR__.'/tax.php');
        }

        public static function instances(){
            new Products;
            new Orders;
            new OrdersGetWithoutMetaData;
            new PaymentMethods;
            new ProductAttributes;
            new ProductAttributeTerms;
            new ProductVariations;
            new Refunds;
            new ShippingMethods;
            new Tax;
            new Customers;
        }

        public static function init(){
            self::include();
            self::instances();
        }

        public static function version(){
            return 'v1';
        }
    }