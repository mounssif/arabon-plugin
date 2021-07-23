<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://www.arabon.be
 * @since      1.0.0
 * @package    Arabon
 * @subpackage Arabon/admin
 * @author     Aranere <arabon@aranere.be>
 */
class Arabon_Public
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
    {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
    {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Arabon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Arabon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/arabon-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
    {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Arabon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Arabon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/arabon-public.js', array( 'jquery' ), $this->version, false );
	}


    /**
     * @param $args
     * @param $post_type
     *
     * @return mixed
     */
    public function arabon_change_portfolio_item_slug ($args, $post_type)
    {
        $portfolio_item_slug = get_option('arabon_portfolio_item_slug');

        if ('featured_item' === $post_type) {
            $args['rewrite']['slug'] = $portfolio_item_slug;
        }

        return $args;
    }

    /**
     * @param $args
     * @param $post_type
     *
     * @return mixed
     */
    public function arabon_change_portfolio_slug ($args, $post_type)
    {
        $portfolio_slug = get_option('arabon_portfolio_slug');

        if ('featured_item_category' === $post_type) {
            $args['rewrite']['slug'] = $portfolio_slug;
        }

        return $args;
    }

    public function arabon_custom_gravity_message($message, $form) {
        return "Oeps er ging iets mis! Probeer het later opnieuw.";
    }

    /**
     * @param $validation_result
     *
     * @return mixed
     */
    public function arabon_register_company( $validation_result )
    {
        $form  = $validation_result['form'];
        $entry = GFFormsModel::get_current_lead();


        $api_host               = get_option( 'arabon_api_host' );
        $api_key                = get_option( 'arabon_api_key' );
        $form_id                = get_option( 'arabon_register_gf_form_id' );
        $form_default_values    = get_option( 'arabon_register_gf_form_default_values' );


        // arabon name => gravity form field id
        $form_field_mapping = [
            'first_name'				=> 18,
            'last_name'				    => 19,
            'email'					    => 3,
            'name'					    => 9,
            'address_street'			=> 10,
            'address_number'			=> 11,
            'address_postal_code'		=> 3570,
            'vat_number'				=> 14,
            'bank_account'			    => 15,
            'bank_bic'				    => 16,
            'description'			    => 17,
            'phone'					    => 21,
        ];

        // https://docs.gravityforms.com/gform_validation/

        // COMPANY signup
        if ($form_id == $form['id']) {
            $input_data = $entry;

            // register_gf_form_default_values: address_postal_code=3400,address_city=Landen,address_country=BE
            $payload = [
                // 'only_validate'			=> true,
                'first_name'			=> $input_data[$form_field_mapping['first_name']],
                'last_name'				=> $input_data[$form_field_mapping['last_name']],
                'email'					=> $input_data[$form_field_mapping['email']],
                'name'					=> $input_data[$form_field_mapping['name']],
                'address_street'		=> $input_data[$form_field_mapping['address_street']],
                'address_number'		=> $input_data[$form_field_mapping['address_number']],
//                'address_postal_code'	=> 3400,
//                'address_city'			=> 'Landen',
//                'address_country'		=> 'BE',
                'language'				=> 'nl',
                'vat_number'			=> str_replace(' ', '', $input_data[$form_field_mapping['vat_number']]),
                'bank_account'			=> str_replace(' ', '', $input_data[$form_field_mapping['bank_account']]),
                'bank_bic'				=> str_replace(' ', '', $input_data[$form_field_mapping['bank_bic']]),
                'description'			=> $input_data[$form_field_mapping['description']],
                'phone'					=> $input_data[$form_field_mapping['phone']],
            ];

            // do we have defaults?
            if (strlen($form_default_values) > 0) {
                $defaults = explode(',', $form_default_values);

                // let's overwrite
                if (count($defaults) > 0) {
                    foreach ($defaults as $default) {
                        list($key, $value) = explode('=', $default);

                        $payload[$key] = $value;
                    }
                }
            }

            $result = wp_remote_request('https://' .$api_host. '/api/company?api_key=' .$api_key, [
                'method'      			=> 'POST',
                'headers'				=> [
                    'accept'                => 'application/json'
                ],
                'body'					=> $payload
            ]);

            if(!is_wp_error($result)){

                $body = json_decode($result['body'], true);



    //            echo '<pre>';
    //            var_dump([
    //                $api_host,
    //                $api_key,
    //                $form_id,
    //                $form_default_values,
    //            ]);
    //            echo '</pre>';
    //            echo '<pre>';
    //            var_dump($defaults);
    //            echo '</pre>';
    //            echo '<pre>';
    //            var_dump($payload);
    //            echo '</pre>';
    //            echo '<pre>';
    //            var_dump($body);
    //            echo '</pre>';
    //            exit;
                
                if (is_array($body) && array_key_exists('errors', $body) && count($body['errors']) > 0) {
                    $validation_result['is_valid'] = false;

                    // normalise errors first
                    $errors = [];
                    $errors_txt = [];
                    foreach ($body['errors'] as $field_name=>$error) {
                        $errors[$field_name] = $form_field_mapping[$field_name];
                        $errors_txt[$form_field_mapping[$field_name]] = $error;
                    }

                    // match errors with GF fields
                    foreach ( $form['fields'] as &$field ) {

                        if ( in_array($field->id, $errors) ) {
                            $field->failed_validation  = true;
                            $field->validation_message = implode(' ', $errors_txt[$field->id]);
                        }
                    }
                }
            } else {
                $validation_result['is_valid'] = false;
                add_filter( 'gform_validation_message', [$this, 'arabon_custom_gravity_message'], 10, 2 );
            }
        }

        return $validation_result;
    }
}
