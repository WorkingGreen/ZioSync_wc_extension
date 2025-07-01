<?php
    namespace ZioSync;
    if (!defined('ABSPATH')) {
        exit;
    }

    final class ProductVariations extends \WC_REST_Product_Variations_Controller{
        public function __construct(){
            parent::__construct();
        }
    }