<?php

use function ZioSync\register_rest_route;
use function ZioSync\wp_parse_list;

if (!defined('ABSPATH')) {
        exit;
    }

    final class OrdersGetWithoutMetaData extends \WC_REST_Orders_Controller{
        public function __construct(){
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'orders',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
        }
//
//        public function get_fields_for_response( $request ) {
//            $schema     = $this->get_item_schema();
//            $properties = isset( $schema['properties'] ) ? $schema['properties'] : array();
//
////            $additional_fields = $this->get_additional_fields();
////
////            foreach ( $additional_fields as $field_name => $field_options ) {
////                /*
////                 * For back-compat, include any field with an empty schema
////                 * because it won't be present in $this->get_item_schema().
////                 */
////                if ( is_null( $field_options['schema'] ) ) {
////                    $properties[ $field_name ] = $field_options;
////                }
////            }
//
//            // Exclude fields that specify a different context than the request context.
//            $context = $request['context'];
//            if ( $context ) {
//                foreach ( $properties as $name => $options ) {
//                    if ( ! empty( $options['context'] ) && ! in_array( $context, $options['context'], true ) ) {
//                        unset( $properties[ $name ] );
//                    }
//                }
//            }
//
//            $fields = array_keys( $properties );
//
//            /*
//             * '_links' and '_embedded' are not typically part of the item schema,
//             * but they can be specified in '_fields', so they are added here as a
//             * convenience for checking with rest_is_field_included().
//             */
//            $fields[] = '_links';
//            if ( $request->has_param( '_embed' ) ) {
//                $fields[] = '_embedded';
//            }
//
//            $fields = array_unique( $fields );
//
//            if ( ! isset( $request['_fields'] ) ) {
//                return $fields;
//            }
//            $requested_fields = wp_parse_list( $request['_fields'] );
//            if ( 0 === count( $requested_fields ) ) {
//                return $fields;
//            }
//            // Trim off outside whitespace from the comma delimited list.
//            $requested_fields = array_map( 'trim', $requested_fields );
//            // Always persist 'id', because it can be needed for add_additional_fields_to_object().
//            if ( in_array( 'id', $fields, true ) ) {
//                $requested_fields[] = 'id';
//            }
//            // Return the list of all requested fields which appear in the schema.
//            return array_reduce(
//                $requested_fields,
//                static function ( $response_fields, $field ) use ( $fields ) {
//                    if ( in_array( $field, $fields, true ) ) {
//                        $response_fields[] = $field;
//                        return $response_fields;
//                    }
//                    // Check for nested fields if $field is not a direct match.
//                    $nested_fields = explode( '.', $field );
//                    /*
//                     * A nested field is included so long as its top-level property
//                     * is present in the schema.
//                     */
//                    if ( in_array( $nested_fields[0], $fields, true ) ) {
//                        $response_fields[] = $field;
//                    }
//                    return $response_fields;
//                },
//                array()
//            );
//        }


    }