<?php
    namespace ZioSync;
    if (!defined('ABSPATH')) {
        exit;
    }

    final class Data extends \WC_REST_Data_Countries_Controller{
        public function __construct(){
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(). '/data',
                'countries',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
    

        }
    }