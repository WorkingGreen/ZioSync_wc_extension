<?php
    namespace ZioSync;
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
                    'callback'            => array($this, 'get_items'),
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

        /**
         * User meta keys that may be queried through /customers/meta. Anything outside
         * this list is rejected, so the endpoint cannot be used to read or probe
         * sensitive meta such as session_tokens, capabilities or password reset keys.
         */
        private const QUERYABLE_META_KEYS = array(
            'billing_email',
            'billing_phone',
            'shipping_phone',
            'ziosync_customer_id',
        );

        public function meta($request)
        {
            $meta_key   = $request->get_param('meta_key');
            $meta_value = $request->get_param('meta_value');

            // Without both parameters the meta clause is dropped and WP_User_Query
            // returns every user on the site, so require them explicitly.
            if (empty($meta_key) || null === $meta_value || '' === $meta_value) {
                return new \WP_Error(
                    'ziosync_rest_missing_meta_query',
                    __('Both meta_key and meta_value are required.', 'ziosync-connection-rest-api'),
                    array('status' => 400)
                );
            }

            if (!in_array($meta_key, self::QUERYABLE_META_KEYS, true)) {
                return new \WP_Error(
                    'ziosync_rest_meta_key_not_allowed',
                    __('The requested meta_key may not be queried through this endpoint.', 'ziosync-connection-rest-api'),
                    array('status' => 403)
                );
            }

            $args = array(
                'order'      => 'ASC',
                'orderby'    => 'display_name',
                'meta_query' => array(
                    array(
                        'key'     => $meta_key,
                        'value'   => $meta_value,
                        'compare' => '='
                    )
                )
            );

            $wp_user_query = new \WP_User_Query($args);
            $result        = $wp_user_query->get_results();

            return array_map(array($this, 'prepare_user_for_response'), $result);
        }

        /**
         * WP_User exposes the raw wp_users row - including the user_pass hash and
         * user_activation_key - through its public $data property, so a WP_User must
         * never be serialised into a REST response as-is. Return an explicit field list.
         */
        private function prepare_user_for_response($user)
        {
            if (!$user instanceof \WP_User) {
                return null;
            }

            return array(
                'data'  => array(
                    'ID'              => $user->data->ID,
                    'user_login'      => $user->data->user_login,
                    'user_nicename'   => $user->data->user_nicename,
                    'user_email'      => $user->data->user_email,
                    'user_url'        => $user->data->user_url,
                    'user_registered' => $user->data->user_registered,
                    'user_status'     => $user->data->user_status,
                    'display_name'    => $user->data->display_name,
                ),
                'ID'    => $user->ID,
                'roles' => $user->roles,
            );
        }

        public function items($request)
        {
            return get_users(['fields'=>'ID,user_registered']);
        }
    }