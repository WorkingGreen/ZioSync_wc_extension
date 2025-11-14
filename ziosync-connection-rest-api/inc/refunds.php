<?php
    namespace ZioSync;
    if (!defined('ABSPATH')) {
        exit;
    }

    final class Refunds extends \WC_REST_Refunds_Controller{
        public function __construct(){
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'refunds',                // Endpoint
                array(
                    'methods'             => \WP_REST_Server::READABLE, // Use constant for 'GET'
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(), // Calls the overridden method below
                )
            );
        }

//        /**
//         * Get the query params for collections.
//         *
//         * Overrides parent to add 'modified_after' and 'orderby=modified' support.
//         *
//         * @return array
//         */
//        public function get_collection_params() {
//            // Get all parent params (like pagination, search, etc.)
//            $params = parent::get_collection_params();
//
//            // Add our custom parameter for modification date
//            $params['modified_after'] = array(
//                'description'       => __( 'Limit response to resources modified after a given ISO8601 compliant date.', 'woocommerce' ),
//                'type'              => 'string',
//                'format'            => 'date-time',
//                'validate_callback' => 'wc_rest_validate_date_query',
//            );
//
//            // Add 'modified' to the 'orderby' enum if it's not already there
//            if ( ! empty( $params['orderby']['enum'] ) && ! in_array( 'modified', $params['orderby']['enum'], true ) ) {
//                $params['orderby']['enum'][] = 'modified';
//            }
//
//            return $params;
//        }
//
//        /**
//         * Prepare objects query.
//         *
//         * Overrides parent to add the date_query for 'modified_after'.
//         * This method is called by the 'get_items' callback.
//         *
//         * @param \WP_REST_Request $request Full details about the request.
//         * @return array Query arguments for WP_Query.
//         */
//        protected function prepare_objects_query( $request ) {
//            // Get the default query args from the parent class
//            $query_args = parent::prepare_objects_query( $request );
//
//            // Check if our 'modified_after' parameter is set
//            if ( ! empty( $request['modified_after'] ) ) {
//
//                // Initialize date_query if it doesn't exist
//                if ( ! isset( $query_args['date_query'] ) ) {
//                    $query_args['date_query'] = array();
//                }
//
//                // Add our modified date query.
//                // We use 'post_modified_gmt' for accuracy.
//                $query_args['date_query'][] = array(
//                    'column' => 'post_modified_gmt',
//                    'after'  => $request['modified_after'],
//                    'inclusive' => true, // Include refunds modified at the exact time
//                );
//            }
//
//            // Allow sorting by modification date
//            if ( isset( $request['orderby'] ) && 'modified' === $request['orderby'] ) {
//                $query_args['orderby'] = 'modified';
//            }
//
//            return $query_args;
//        }
    }