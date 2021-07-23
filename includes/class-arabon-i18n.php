<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.arabon.be
 * @since      1.0.0
 * @package    Arabon
 * @subpackage Arabon/admin
 * @author     Aranere <arabon@aranere.be>
 */
class Arabon_i18n
{
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
    {
		load_plugin_textdomain(
			'arabon',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
