<?php
    namespace ZioSync;
    if (!defined('ABSPATH')) {
        exit;
    }

    final class Refunds extends \WC_REST_Refunds_Controller{
        public function __construct(){
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'refunds',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
        }
    }