<?php
    namespace ZioSync;
    if (!defined('ABSPATH')) {
        exit;
    }

    final class OrderRefunds extends \WC_REST_Order_Refunds_Controller{
        public function __construct(){
    
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'orders/(?P<order_id>[\d]+)/refunds',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );

            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'orders/(?P<order_id>[\d]+)/refunds/(?P<id>[\d]+)',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
        }

        public function get_all_items($request){
            $refunds = new \WP_Query(
                array(
                    'numberposts'    => -1,
                    'post_type'      => 'shop_order_refund',
                    'orderby'        => 'post_modified',
                    'order'          => 'ASC',
                )
            );

            return array_map(function($refund){
                return [
                    'id'=>$refund->ID,
                    'order_id'=>$refund->parent_id,
                    'date_created'=>$refund->date_created_gmt,
                    'date_modified'=>$refund->date_modified_gmt,
                ];
            }, $refunds->posts);
        }
    }