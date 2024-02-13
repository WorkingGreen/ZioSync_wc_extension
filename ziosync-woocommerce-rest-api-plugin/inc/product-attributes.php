<?php
    namespace ZioSync\inc;
    use function ZioSync\register_rest_route;

    if (!defined('ABSPATH')) {
        exit;
    }

    final class ProductAttributes extends \WC_REST_Product_Attributes_Controller
    {
        public function __construct(){
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'products/attributes',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
    
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'products/attributes/(?P<id>[\S]+)',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                )
            );
        }
    }