<?php
    namespace ZioSync;
    if (!defined('ABSPATH')) {
        exit;
    }

    final class Tax extends \WC_REST_Tax_Classes_Controller{
        public function __construct(){
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'tax_classes',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'tax_classes/(?P<slug>[\S]+)/(?P<region>[\S]+)',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_item_country'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
            register_rest_route(
                'wc-ziosync/'.ZioSync::version(),
                'tax_classes/(?P<slug>[\S]+)',
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                )
            );
            
        }

        public function get_item($request){
            if(!$request->has_param('slug')){
                return [];
            }
            $taxes = \WC_Tax::get_rates_for_tax_class( $request->get_param('slug') );
            return $taxes;
        }

        public function get_item_country($request){
            if(!$request->has_param('slug') || !$request->has_param('region')){
                return [];
            }
            $taxes = \WC_Tax::get_rates_for_tax_class( $request->get_param('slug') );
            $country_state = explode('_', $request->get_param('region'));
            $result = [];
            foreach($taxes as $tax){
                if(count($country_state)==1){//if filtered only by country
                    if($tax->tax_rate_country == $country_state[0]){
                        $result[] = $tax; 
                    }
                }elseif(count($country_state)==2){//if filtered by country and state
                    if($tax->tax_rate_country == $country_state[0] && $tax->tax_rate_state == $country_state[1]){
                        $result[] = $tax; 
                    }
                }else{
                    //nothing to compare
                    $result[] = $tax;
                }
            }
            return $result;
        }
    }