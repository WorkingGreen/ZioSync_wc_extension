<?php
    namespace ZioSync;
    if (!defined('ABSPATH')) {
        exit;
    }

    final class Products extends \WC_REST_Products_Controller{
        public function __construct(){
            parent::__construct();
            $this->init();
        }

        public function init()
        {
            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products', [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'override_get_items'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args'                => $this->get_collection_params(),
                ]
            );


            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products', [
                    'methods'             => 'POST',
                    'callback'            => [$this, 'create'],
                    'permission_callback' => [$this, 'create_item_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema('POST'),
                ]
            );

            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products', [
                    'methods'             => 'POST, PUT, PATCH',
                    'callback'            => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema('POST, PUT, PATCH'),
                ]
            );

            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products/batch', [
                    'methods'             => 'POST, PUT, PATCH',
                    'callback'            => [$this, 'batch_items'],
                    'permission_callback' => [$this, 'batch_items_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema('POST, PUT, PATCH'),
                ]
            );

            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products/(?P<id>[\d]+)', [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'get_item'],
                    'permission_callback' => [$this, 'get_item_permissions_check'],
                    'args'                => $this->get_collection_params(),
                ]
            );

            // NOTE: these routes must use get_items_permissions_check(). The inherited
            // get_item_permissions_check() only enforces a capability when it can resolve a
            // product from (int) $request['id']; with no numeric 'id' in the route it resolves
            // nothing and returns true, leaving the endpoint open to unauthenticated callers.
            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products/allby/sku/(?P<sku>[\S]+)', [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args'                => $this->get_collection_params(),
                ]
            );

            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products/search', [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'search'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args'                => $this->get_collection_params(),
                ]
            );

            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products/findby/sku/(?P<id>[\S]+)', [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'findby_sku'],
                    'permission_callback' => [$this, 'get_items_permissions_check'],
                    'args'                => $this->get_collection_params(),
                ]
            );

            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products/(?P<id>[\d]+)', [
                    'methods'             => 'POST, PUT, PATCH',
                    'callback'            => [$this, 'update_item'],
                    'permission_callback' => [$this, 'update_item_permissions_check'],
                    'args'                => $this->get_endpoint_args_for_item_schema('POST, PUT, PATCH'),
                ]
            );

            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products/types', [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'types'],
                    'permission_callback' => '__return_true',
                ]
            );

//            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products/categories', [
//                    'methods'             => 'GET',
//                    'callback'            => [$this, 'categories'],
//                    'permission_callback' => '__return_true',
//                ]
//            );

            register_rest_route('wc-ziosync/'.ZioSync::version(), 'products/categories' . '/(?P<id>[\d]+)', [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'category'],
                    'permission_callback' => '__return_true',
                ]
            );
        }

        public function create($request)
        {
            $file = $this->uploadFile($request);
            if ($file) {
                $request = $this->addFileToRequest($request, $file);
            }

            return parent::create_item($request);
        }

        public function get_item($request){
        
            $result = parent::get_item($request);

            if (isset($result->data['type']) && $result->data['type']=='variable') {
                $result->data['is_variable'] = true;
            } else {
                $result->data['is_variable'] = false;
            }

            return $result;
        }

        public function override_get_items($request)
        {
            $limit          = 50;
            $date_modified  = '1970-01-01 00:00:00';
            $product_only   = 1;

            if($request->has_param('limit'))
                $limit = $request->get_param('limit');
            if($request->has_param('product_only'))
                $product_only = $request->get_param('product_only');
            if($request->has_param('date_modified'))
                $date_modified = $request->get_param('date_modified');

            $products = new \WP_Query(
                [
                    'post_type'      => $product_only ? ['product'] : ['product', 'product_variation'],
                    'posts_per_page' => $limit,
                    'orderby'        => 'post_modified',
                    'order'          => 'ASC',
                    'date_query'     => [
                        [
                            'column'    => 'post_modified_gmt',
                            'after'     => $date_modified,
                            'inclusive' => true,
                        ],
                    ],
                ]
            );

            return array_map(function($product){
                $item = [];
                $product_ = new \WC_Product($product->ID);
                $item['id']            = $product_->get_id();
                $item['sku']           = $product_->get_sku();
                $item['type']          = $product_->get_type();
                $item['parent']        = $product_->get_parent_id();
                $item['date_created']  = $product_->get_date_created();
                $item['date_modified'] = $product_->get_date_modified();

                return $item;
            }, $products->posts);
        }

        public function update_item($request)
        {
            $object = $this->get_object($request['id']);

            if($object instanceof \WP_Error)
                return new \WP_Error('404', 'Item not found', ['status'=>'404']);
            

            if (isset($request['stock_quantity'])) {
                return $this->updateStock($request, $object);
            }

            $file = $this->uploadFile($request);
            if ($file) {
                $request = $this->addFileToRequest($request, $file, $object);
            }

            return parent::update_item($request);
        }

        public function search($request)
        {
            if(!$request->has_param('keyword'))
                return [];

            $keyword = $request->get_param('keyword');

            $query = new \WC_Product_Query( 
                array(
                 'posts_per_page' => -1,
                 's' => esc_attr( $keyword ),
                 ) 
            );
            return $query->get_products();
        }

        public function findby_sku($request)
        {
            $sku = urldecode($request['id']);
            $product_id        = wc_get_product_id_by_sku($sku);

            if (!$product_id) {
                return new \WP_Error('404', 'No item found with SKU: ' . $product_reference, ['status' => '404']);
            }

            $request['id'] = $product_id;
            return $this->get_item($request);
        }

        public function types($request)
        {
            return wc_get_product_types();
        }

//        public function categories($request)
//        {
//
//            $terms = get_terms('product_cat', [
//                'hide_empty'=>false,
//                'fields'=>'ids',
//            ]);
//            $temp = $this->get_product_category;
//            return array_map(function($term)use($temp, $request){
//                return $temp($request, $term);
//            }, $terms);
//
//        }

        public function category($request, $term_id = null)
        {
            if ($term_id === null) {
                $term_id = $request['id'];
            }

            $term = get_term($term_id, 'product_cat');

            $display_type = get_term_meta($term_id, 'display_type', true);

            $image    = '';
            $image_id = get_term_meta($term_id, 'thumbnail_id', true);
            if ($image_id) {
                $image = wp_get_attachment_url($image_id);
            }

            return [
                'id'          => $term_id,
                'name'        => $term->name,
                'slug'        => $term->slug,
                'parent'      => $term->parent,
                'description' => $term->description,
                'display'     => $display_type,
                'image'       => $image,
                'count'       => $term->count,
            ];
        }


        /**
         * Image types accepted for product media, keyed by the MIME type detected from
         * the decoded bytes. The stored extension is always taken from this map and
         * never from caller supplied input, so a caller cannot place a .php (or any
         * other executable) file inside the uploads directory.
         */
        private const ALLOWED_MEDIA_TYPES = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];

        private function uploadFile($request)
        {
            if (!isset($request['ziosync_media'])) {
                return false;
            }

            $media = $request['ziosync_media'];

            if (!is_array($media) || empty($media['media_data_base64_encoded'])) {
                return false;
            }

            $data = base64_decode($media['media_data_base64_encoded'], true);
            if (false === $data || '' === $data) {
                return false;
            }

            // Derive the type from the actual bytes rather than trusting the caller.
            $info = @getimagesizefromstring($data);
            if (!$info || empty($info['mime']) || !isset(self::ALLOWED_MEDIA_TYPES[$info['mime']])) {
                return false;
            }
            $extension = self::ALLOWED_MEDIA_TYPES[$info['mime']];

            // basename() drops any directory traversal, sanitize_file_name() the rest,
            // and the trailing strip removes a caller supplied extension such as ".php".
            $name = isset($media['formatted_sku']) ? (string) $media['formatted_sku'] : '';
            $name = sanitize_file_name(basename($name));
            $name = preg_replace('/\.[^.]*$/', '', $name);
            if ('' === $name) {
                $name = 'product-image';
            }

            // wp_upload_bits() writes into the uploads dir, guarantees a unique filename
            // and refuses any extension WordPress does not allow.
            $upload = wp_upload_bits($name . '.' . $extension, null, $data);

            if (!empty($upload['error']) || empty($upload['url'])) {
                return false;
            }

            return $upload['url'];
        }

        private function updateStock($request, $product)
        {
            $product->set_stock_quantity($request['stock_quantity']);
            $product->save();

            return new \WP_REST_Response($request['stock_quantity'], 200);
        }

        private function addFileToRequest($request, $file, $product = null)
        {
            $images    = $product ? $this->get_images( $product ) : [];
            $images[0] = [ "src" => $file ];

            $request->set_param( 'images', $images );

            return $request;
        }
    }