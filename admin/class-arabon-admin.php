<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://www.arabon.be
 * @since      1.0.0
 * @package    Arabon
 * @subpackage Arabon/admin
 * @author     Aranere <arabon@aranere.be>
 */
class Arabon_Admin {

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
     * @var 
     */
	private $default_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
    {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$this->default_options = [
		    'api_key'               => '',
		    'api_host'              => '',
		    'portfolio_slug'        => '',
		    'portfolio_item_slug'   => '',
		    'register_gf_form_id'   => '',
        ];
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/arabon-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/arabon-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Register the menu for the admin area.
     *
     * @since    1.0.0
     */
    public function plugin_menu()
    {
        add_options_page('Arabon', 'Arabon', 'activate_plugins', 'arabon_options', [
            $this,
            'plugin_options',
        ]);

        add_menu_page(
            __( 'Arabon', 'arabon' ),
            'Arabon',
            'manage_options',
            'arabon',
            [$this, 'arabon_page_callback'],
            'dashicons-list-view',
            6
        );

    }

    public function arabon_autocomplete_orders( $order_id )
    {
        if ( ! $order_id ) {
            return;
        }
        
        $order = wc_get_order( $order_id );
        $order->update_status( 'completed' );
    }


    /**
     * Flush permalinks on update
     *
     * @since    1.0.0
     */
    public function arabon_plugin_settings_flush_rewrite()
    {
        if ( get_option('arabon_plugin_settings_changed') == 'true' ) {
            flush_rewrite_rules();
            update_option('arabon_plugin_settings_changed', '0');
        }
    }

    /**
     * Import products by ajax call
     *
     * @since    1.0.0
     */
    public function arabon_import_all()
    {
        $arabon_importer = new Arabon_Admin_Import();
        $sync_type = $sync_identifier = $identifier = null;

        if (array_key_exists('sync_type', $_POST)) {
            $sync_type = sanitize_text_field( $_POST['sync_type'] );
        }
        if (array_key_exists('sync_identifier', $_POST)) {
            $identifier = sanitize_text_field( $_POST['sync_identifier'] );
        }

        $arabon_importer->import_categories();
//        $arabon_importer->import_products();


        if ('company' == $sync_type && strlen($identifier) > 0) {
            $arabon_importer->import_companies($identifier);
        } else {
            $arabon_importer->import_companies();
        }

        echo "Success";
    }

    /**
     *
     */
    public function arabon_page_callback()
    {
        wp_enqueue_script($this->plugin_name . '-import', plugin_dir_url(__FILE__) . 'js/arabon-admin-import.js', ['jquery'], $this->version, false);
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/arabon-admin-dashboard-display.php';
    }

    /**
     * Check the input settings
     *
     * @param array $new_options
     *
     * @return string
     */
    private function check_input_settings( array $new_options )
    {
        $error = '';

        if ( trim( $new_options['api_host'] ) === '' ) {
            $error = "Please fill in the API host.";
        } elseif ( trim($new_options['api_key'] ) === '' ) {
            $error = "Please fill in an API key.";
        }

        return $error;
    }

    /**
     * Register the view for the admin area.
     *
     * @since    1.0
     */
    public function plugin_options()
    {
        if ( $_POST ) {

            if ( isset( $_POST['submit'] ) ) {

                $new_options['api_key'] = sanitize_text_field($_POST['api_key']);
                $new_options['api_host'] = sanitize_text_field($_POST['api_host']);
                $new_options['portfolio_slug'] = sanitize_text_field($_POST['portfolio_slug']);
                $new_options['portfolio_item_slug'] = sanitize_text_field($_POST['portfolio_item_slug']);
                $new_options['register_gf_form_id'] = sanitize_text_field($_POST['register_gf_form_id']);
                $new_options['register_gf_form_default_values'] = sanitize_text_field($_POST['register_gf_form_default_values']);

                $error = $this->check_input_settings($new_options);

                if (!empty($error)) {

                    $arabon_options['api_key'] = sanitize_text_field($_POST['api_key']);
                    $arabon_options['api_host'] = sanitize_text_field($_POST['api_host']);
                    $arabon_options['portfolio_slug'] = sanitize_text_field($_POST['portfolio_slug']);
                    $arabon_options['portfolio_item_slug'] = sanitize_text_field($_POST['portfolio_item_slug']);
                    $arabon_options['register_gf_form_id'] = sanitize_text_field($_POST['register_gf_form_id']);
                    $arabon_options['register_gf_form_default_values'] = sanitize_text_field($_POST['register_gf_form_default_values']);

                    $arabon_options = wp_parse_args( array_filter( $arabon_options ), $this->default_options );

                    $message = '<div id="message" class="error"><p>' . $error . '</p></div>';

                } else {

                    $arabon_options       = wp_parse_args( array_filter( $new_options ), $this->default_options );

                    update_option( 'arabon_api_key', $arabon_options['api_key'] );
                    update_option( 'arabon_api_host', $arabon_options['api_host'] );
                    update_option( 'arabon_portfolio_slug', $arabon_options['portfolio_slug'] );
                    update_option( 'arabon_portfolio_item_slug', $arabon_options['portfolio_item_slug'] );
                    update_option( 'arabon_register_gf_form_id', $arabon_options['register_gf_form_id'] );
                    update_option( 'arabon_register_gf_form_default_values', $arabon_options['register_gf_form_default_values'] );

                    update_option('arabon_plugin_settings_changed', 'true');

                    $message = '<div id="message" class="updated"><p>Arabon settings updated</p></div>';


                }
            }

        } else {

            $arabon_options       = $this->get_arabon_options();

        }

        require_once plugin_dir_path( dirname(__FILE__) ) . 'admin/partials/arabon-admin-display.php';
    }
    
    /**
     * Get the values of the easy publisher options
     *
     * @return array
     */
    private function get_arabon_options()
    {
        $ep_options['api_key']                          = get_option( 'arabon_api_key' );
        $ep_options['api_host']                         = get_option( 'arabon_api_host' );
        $ep_options['portfolio_slug']                   = get_option( 'arabon_portfolio_slug' );
        $ep_options['portfolio_item_slug']              = get_option( 'arabon_portfolio_item_slug' );
        $ep_options['register_gf_form_id']              = get_option( 'arabon_register_gf_form_id' );
        $ep_options['register_gf_form_default_values']  = get_option( 'arabon_register_gf_form_default_values' );

        return wp_parse_args( array_filter($ep_options), $this->default_options);
    }

    /**
     * @param $args
     * @param $post_type
     *
     * @return mixed
     */
    public function change_portfolio_item_slug ($args, $post_type)
    {
        $portfolio_item_slug = get_option('portfolio_item_slug');

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
    public function change_portfolio_slug ($args, $post_type)
    {
        $portfolio_slug = get_option('portfolio_slug');

        if ('featured_item_category' === $post_type) {
            $args['rewrite']['slug'] = $portfolio_slug;
        }

        return $args;
    }
}
