<?php
    namespace ZioSync\inc;
    use function ZioSync\get_users;
    use function ZioSync\register_rest_route;

    if (!defined('ABSPATH')) {
        exit;
    }

    final class Customers extends \WC_REST_Customers_Controller{
        public function __construct(){
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'customers',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
    
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'customers/meta',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'meta'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
    
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'customers',
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    'args'                => $this->get_endpoint_args_for_item_schema('POST'),
                )
            );
    
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'customers/(?P<id>[\d]+)',
                array(
                    'methods'             => 'POST,PUT,PATCH',
                    'callback'            => array($this, 'update_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                    'args'                => $this->get_endpoint_args_for_item_schema('POST,PUT,PATCH'),
                )
            );
    
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'customers/roles',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'roles'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
    
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'customers/(?P<id>[\d]+)',
                array(
                    array(
                        'methods'             => 'GET',
                        'callback'            => array($this, 'get_item'),
                        'permission_callback' => array($this, 'get_item_permissions_check'),
                    ),
                )
            );
        }


        public function roles($request)
        {
            global $wp_roles;
            return $wp_roles->role_names;
        }

        public function meta($request)
        {
            $meta_key = $request->get_param('meta_key');
            $meta_value = $request->get_param('meta_value');

            $args = array(
                'order'      => 'ASC',
                'orderby'    => 'display_name',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => $meta_key,
                        'value'   => $meta_value,
                        'compare' => '='
                    )
                )
            );

            $wp_user_query = new \WP_User_Query($args);
            $result        = $wp_user_query->get_results();

            return array_map(function($item){
                return $item;
            }, $result);
        }

        public function items($request)
        {
            return get_users(['fields'=>'ID,user_registered']);
        }
    }