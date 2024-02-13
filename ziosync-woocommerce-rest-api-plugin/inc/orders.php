<?php
    namespace ZioSync\inc;
    use function ZioSync\register_rest_route;
    use function ZioSync\wc_get_order_statuses;

    if (!defined('ABSPATH')) {
        exit;
    }

    final class Orders extends \WC_REST_Orders_Controller{
        public function __construct(){
//            register_rest_route(
//                'wc-ziosync/'.ZioSync::version(),
//                'orders',
//                array(
//                    'methods'             => 'GET',
//                    'callback'            => array($this, 'get_items'),
//                    'permission_callback' => array($this, 'get_items_permissions_check'),
//                    'args'                => $this->get_collection_params(),
//                )
//            );

            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'orders/statuses',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'statuses'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );

            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'orders/batch/(?P<ids>[\S]+)',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_batch_items'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );


            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'orders/(?P<id>[\d]+)',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );


            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'orders/(?P<id>[\d]+)',
                array(
                    'methods'             => 'POST,PUT,PATCH',
                    'callback'            => array($this, 'update_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                    'args'                => $this->get_endpoint_args_for_item_schema('POST,PUT,PATCH'),
                )
            );

            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'orders',
                array(
                    'methods'             => 'POST,PUT,PATCH',
                    'callback'            => array($this, 'batch_items'),
                    'permission_callback' => array($this, 'batch_items_permissions_check'),
                    'args'                => $this->get_endpoint_args_for_item_schema('POST,PUT,PATCH'),
                )
            );
        }

        public function statuses(){
            return wc_get_order_statuses();
        }

        public function get_batch_items($request){
            $ids = $request->get_param('ids');
            $items = [];
            if(!empty($ids)){
                $ids = explode(',', $ids);
                foreach($ids as $id){
                    $items[] = parent::get_item(['id'=>$id]);
                }
            }
            return $items;
        }
    }