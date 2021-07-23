<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.arabon.be
 * @since      1.0.0
 * @package    Arabon
 * @subpackage Arabon/admin
 * @author     Aranere <arabon@aranere.be>
 */
class Arabon_Admin_Import
{
    /**
     * Arabon_Admin_Import constructor.
     */
    public function __construct()
    {
        $this->events = [];
    }

    /**
     * @param $timestamp
     *
     * @return string
     *
     * @throws Exception
     */
    public function ts_to_ymd($timestamp)
    {
        $datetimeFormat = 'Y-m-d';
        $date = new \DateTime('now', new \DateTimeZone('Europe/Brussels'));
        $date->setTimestamp($timestamp);
        return $date->format($datetimeFormat);
    }

    /**
     * @param $timestamp
     *
     * @return string
     *
     * @throws Exception
     */
    public function ts_to_hi($timestamp)
    {
        $datetimeFormat = 'H:i';
        $date = new \DateTime('now', new \DateTimeZone('Europe/Brussels'));
        $date->setTimestamp($timestamp);
        return $date->format($datetimeFormat);
    }

    /**
     * @param $timestamp
     *
     * @return string
     *
     * @throws Exception
     */
    public function ts_to_ymdhi($timestamp){
        $datetimeFormat = 'Y-m-d H:i';
        $date = new \DateTime('now', new \DateTimeZone('Europe/Brussels'));
        $date->setTimestamp($timestamp);
        return $date->format($datetimeFormat);
    }

    /**
     * @param $log
     */
    public function write_log ( $log )
    {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }

    /**
     * @param $image_src
     *
     * @return string|null
     */
    public function get_attachment_id_from_src ($image_src)
    {
        global $wpdb;

        $id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->posts} WHERE post_type='attachment' AND guid = %s", $image_src ) );

        return $id;
    }

    /**
     * Import categories from the Arabon API
     */
    public function import_categories()
    {
        $page = 1;
        $identifiers = [];
        $term_ids = [];
        $api_host               = get_option( 'arabon_api_host' );
        $api_key                = get_option( 'arabon_api_key' );

        do {
            $got_results = false;
            $request = wp_remote_get( 'https://' .$api_host. '/api/category?api_key=' .$api_key. '&size=10&page=' .$page );

            if( is_wp_error( $request ) ) {
                die('Something went wrong (1)');
            }

            $body = wp_remote_retrieve_body( $request );
            $json = json_decode($body);

            if (count($json) > 0) {
                foreach ($json as $json_object) {
                    $identifiers[] = $json_object->identifier;

                    $my_term = [
                        'name'  => $json_object->name,
                        'taxonomy'  => 'featured_item_category',
                        'slug'  => $json_object->slug,
                        'description'  => $json_object->name,
                    ];

                    // FIND term
                    $term = get_term_by('slug', $json_object->slug, 'featured_item_category');

                    if (false === $term) {
                        $result = wp_insert_term($json_object->name, 'featured_item_category', $my_term);

                        $this->write_log('inserted term_id = ' .$result['term_id']);
                    } else {
                        $result = wp_update_term($term->term_id, 'featured_item_category', $my_term);

                        $this->write_log('updated term_id = ' .$result['term_id']);
                    }

                    $term_ids[] = $result['term_id'];
                }

                // continue -> next page
                if (10 === count($json)) {
                    $got_results = true;

                    $page++;
                }
            }

        } while ($got_results);

        // we could remove unused terms here, but let's not do this for now
    }

    /**
     * Import products from the Arabon API
     */
    public function import_products()
    {
        $got_results = true;
        $page = 1;
        $identifiers = [];
        $product_ids = [];
        $api_host            = get_option( 'arabon_api_host' );
        $api_key             = get_option( 'arabon_api_key' );

        do {
            $request = wp_remote_get( 'https://' .$api_host. '/api/product?api_key=' .$api_key. '&size=10&page=' .$page );

            if( is_wp_error( $request ) ) {
                die('Something went wrong (1)');
            }

            $body = wp_remote_retrieve_body( $request );
            $json = json_decode($body);

            if (count($json) > 0) {
                foreach ($json as $json_object) {
                    $identifiers[] = $json_object->identifier;

                    // https://docs.woocommerce.com/wc-apidocs/source-class-WC_REST_Products_V1_Controller.html#731-772
                    $data = [
                        'virtual' => true,
                        'name' => $json_object->name,
                        'description' => $json_object->name,
                        'reviews_allowed' => false,
                        'sku' => $json_object->identifier,
                        'regular_price' => $json_object->amount,
                        'short_description' => $json_object->name,
//                        'categories' => [
//                            [
//                                'id' => 37
//                            ],
//                            [
//                                'id' => 38
//                            ]
//                        ],
                        'images' => [
                            [
                                'src' => $json_object->thumbnail,
                                'position' => 0
                            ]
                        ],
//                        'attributes' => [
//                            [
//                                'name' => 'Color',
//                                'position' => 0,
//                                'visible' => true,
//                                'variation' => true,
//                                'options' => [
//                                    'Black',
//                                    'Green'
//                                ]
//                            ],
//                            [
//                                'name' => 'Size',
//                                'position' => 0,
//                                'visible' => true,
//                                'variation' => true,
//                                'options' => [
//                                    'S',
//                                    'M'
//                                ]
//                            ]
//                        ],
//                        'default_attributes' => [
//                            [
//                                'name' => 'Color',
//                                'option' => 'Black'
//                            ],
//                            [
//                                'name' => 'Size',
//                                'option' => 'S'
//                            ]
//                        ],
//                        'variations' => [
//                            [
//                                'regular_price' => '29.98',
//                                'attributes' => [
//                                    [
//                                        'slug'=>'color',
//                                        'name'=>'Color',
//                                        'option'=>'Black'
//                                    ]
//                                ]
//                            ],
//                            [
//                                'regular_price' => '69.98',
//                                'attributes' => [
//                                    [
//                                        'slug'=>'color',
//                                        'name'=>'Color',
//                                        'option'=>'Green'
//                                    ]
//                                ]
//                            ]
//                        ]
                    ];
                    /*
                    $request = new WP_REST_Request( 'POST' );
                    $request->set_body_params( $data );
                    $products_controller = new WC_REST_Products_Controller;
                    $response = $products_controller->create_item( $request );

                    echo '<pre>';
                    var_dump($response);
                    echo '</pre>';
                    exit;
                    */

//                    $my_term = [
////                        'name'  => $json_object->name,
////                        'taxonomy'  => 'featured_item_category',
////                        'slug'  => $json_object->slug,
////                        'description'  => $json_object->name,
////                    ];
////
////                    // FIND term
////                    $term = get_term_by('slug', $json_object->slug, 'featured_item_category');
////
////                    if (false === $term) {
////                        $result = wp_insert_term($json_object->name, 'featured_item_category', $my_term);
////
////                        $this->write_log('inserted term_id = ' .$result['term_id']);
////                    } else {
////                        $result = wp_update_term($term->term_id, 'featured_item_category', $my_term);
////
////                        $this->write_log('updated term_id = ' .$result['term_id']);
////                    }
//
//                    $product_ids[] = $result['term_id'];
                }

                // continue -> next page
                if (10 === count($json)) {
                    $got_results = true;

                    $page++;
                }
            }

        } while ($got_results);

        // we could remove unused terms here, but let's not do this for now
    }

    /**
     * Import companies from the Arabon API
     *
     * @param null $identifier
     */
    public function import_companies($identifier = null)
    {
        $page = 1;
        $identifiers = [];
        $post_ids = [];
        $api_host            = get_option( 'arabon_api_host' );
        $api_key             = get_option( 'arabon_api_key' );

        do {
            $got_results = false;
            $request = wp_remote_get( 'https://' .$api_host. '/api/company?api_key=' .$api_key. '&identifier=' .$identifier. '&size=10&page=' .$page );

            if( is_wp_error( $request ) ) {
                die('Something went wrong (2)');
            }

            $body = wp_remote_retrieve_body( $request );
            $json = json_decode($body);

            if (count($json) > 0) {
                foreach ($json as $json_object) {

                    $identifiers[] = $json_object->identifier;

                    // hardcoded fallback logo!
                    if (0 == strlen($json_object->logo)) {
                        $json_object->logo = 'https://cdn.arabon.be/wp-content/uploads/2020/08/geen-logo.png';
                    }

                    // prep company
                    $description = "<h4>Adres:</h4>\n" .$json_object->address->street. " " .$json_object->address->number. "<br/>\n" .$json_object->address->postal_code. " " .$json_object->address->city. "<br/>";

                    if (strlen($json_object->email) > 0 || strlen($json_object->email) > 0 || strlen($json_object->email) > 0) {
                        $description .= "<h4>Contactgegevens</h4>";
                    }
                    if (strlen($json_object->phone) > 0) {
                        $description .= "\nTel: " .$json_object->phone. "<br/>";
                    }
                    if (strlen($json_object->website) > 0) {
                        $description .= "\nWebsite: <a href=\"" .$json_object->website. "\" target=\"blank\">" .$json_object->website. "</a><br/>";
                    }
                    if (strlen($json_object->email) > 0) {
                        $description .= "\nE-mail: <a href=\"mailto:" .$json_object->email. "\">" .$json_object->email. "</a><br/>";
                    }

                    if (strlen($json_object->description) > 0) {
                        $description .= "\n<h4>Omschrijving:</h4>\n" .$json_object->description. "<br/>";
                    }

                    // PREP company
                    $my_post = array(
                        'post_title'    => wp_strip_all_tags( $json_object->name ),
                        'post_content'  => '',
                        'post_status'   => 'publish',
                        'post_author'   => 1,
                        'post_type'     => 'featured_item',
                        'post_excerpt'  => $description
                    );


                    // FIND company
                    $find_post = new WP_Query([
                        'post_type' => 'featured_item',
                        'meta_query' => [
                            [
                                'key' => '_arabon_identifier',
                                'value' => $json_object->identifier,
                                'compare' => '=',
                            ],
                        ]
                    ]);

                    if ($find_post->have_posts()) {
                        $my_post['ID'] = $find_post->posts[0]->ID;

                        // ------------------------------------
                        // UPDATE company
                        // ------------------------------------
                        $post_id = wp_update_post( $my_post );
                        add_post_meta($post_id, '_arabon_identifier', $json_object->identifier, true);
                        $meta = get_post_meta($my_post['ID']);

                        $this->write_log('updated post_id = ' .$post_id);

                        // detect if new logo
                        if (strlen($json_object->logo) > 0 && $meta['_thumbnail_src'][0] !== $json_object->logo) {
                            $image = media_sideload_image( $json_object->logo, $post_id, $json_object->name, 'src' );
                            $attachment_id = $this->get_attachment_id_from_src($image);

                            update_post_meta($post_id, '_thumbnail_id', $attachment_id);
                            update_post_meta($post_id, '_thumbnail_src', $json_object->logo);

                            $this->write_log('updated image for post_id = ' .$post_id);
//                            var_dump('- UPDATED image for post_id = ' .$post_id);
//                            var_dump('- ' .$json_object->logo);
//                            var_dump('- ' .$image);
//                            var_dump('- ' .$attachment_id);
                        }
                    } else {
                        // ------------------------------------
                        // INSERT company
                        // ------------------------------------
                        $post_id = wp_insert_post( $my_post );
                        add_post_meta($post_id, '_arabon_identifier', $json_object->identifier, true);

                        $this->write_log('inserted post_id = ' .$post_id);

                        if (strlen($json_object->logo) > 0) {
                            $image = media_sideload_image( $json_object->logo, $post_id, $json_object->name, 'src' );
                            $attachment_id = $this->get_attachment_id_from_src($image);

                            add_post_meta($post_id, '_thumbnail_id', $attachment_id);
                            add_post_meta($post_id, '_thumbnail_src', $json_object->logo);

                            $this->write_log('inserted image for post_id = ' .$post_id);
//                            var_dump('- INSERTED image for post_id = ' .$post_id);
//                            var_dump('- ' .$json_object->logo);
//                            var_dump('- ' .$image);
//                            var_dump('- ' .$attachment_id);

                        }
                    }

                    // UPDATE company CATEGORIES
                    $terms = [];
                    if (count($json_object->categories) > 0) {
                        foreach ($json_object->categories as $category) {
                            $term = get_term_by('slug', $category->slug, 'featured_item_category');
                            $terms[] = $term->term_id;
                        }
                    }
                    wp_set_post_terms($post_id, $terms, 'featured_item_category', true);
                    $this->write_log('updated terms for post_id = ' .$post_id, ' => (' .implode(',', $terms). ')');

                    $post_ids[] = $post_id;
                }

                // continue -> next page
                if (10 === count($json)) {
                    $got_results = true;

                    $page++;
                }
            } else {
//                $got_results = false;

                if (strlen($identifier) > 0) {
//                    $got_results = false;

                    // FIND company
                    $find_post = new WP_Query([
                        'post_type' => 'featured_item',
                        'meta_query' => [
                            [
                                'key' => '_arabon_identifier',
                                'value' => $identifier,
                                'compare' => '=',
                            ],
                        ]
                    ]);

                    if ($find_post->have_posts()) {
                        wp_delete_post($find_post->posts[0]->ID);
                    }
                }
            }

        } while ($got_results);

        // remove companies for identifiers we no longer found, but only if identifier=null (no filter)
        if (count($post_ids) > 0 && is_null($identifier)) {

            $delete_posts = new WP_Query([
                'post__not_in' => $post_ids,
                'post_type' => 'featured_item',
            ]);

            if ( $delete_posts->have_posts() ) {
                foreach ($delete_posts->get_posts() as $delete_this_post) {

//                    var_dump('DELETING post_id = ' .$delete_this_post->ID);
                    $this->write_log('deleted post_id = ' .$delete_this_post->ID);

                    wp_delete_post($delete_this_post->ID, true);
                }
            }
        }
    }
}
?>