<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       https://www.arabon.be
 * @since      1.0.0
 * @package    Arabon
 * @subpackage Arabon/admin
 * @author     Aranere <arabon@aranere.be>
 */
class Arabon_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
    {
        global $wp_rewrite;

        add_option('arabon_api_host', 'scan.arabon.be');
        add_option('arabon_api_key', 'REPLACEME');
        add_option('arabon_portfolio_slug', 'categorie');
        add_option('arabon_portfolio_item_slug', 'winkel');
        add_option('arabon_register_gf_form_default_values', '');

        $wp_rewrite->flush_rules();
	}

}
