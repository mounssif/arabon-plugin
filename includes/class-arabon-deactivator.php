<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       https://www.arabon.be
 * @since      1.0.0
 * @package    Arabon
 * @subpackage Arabon/admin
 * @author     Aranere <arabon@aranere.be>
 */
class Arabon_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
    {
        update_option( 'arabon_api_key', '');
	}

}
