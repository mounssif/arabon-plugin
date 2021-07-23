<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.aranere.be
 * @since             1.0.0
 * @package           Arabon
 *
 * @wordpress-plugin
 * Plugin Name:       Arabon
 * Description:       Arabon Wordpress plugin
 * Author:            Arabon
 * Author URI:        https://www.arabon.be
 * Text Domain:       arabon
 * Domain Path:       /languages
 * Version:           1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ARABON_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-arabon-activator.php
 */
function activate_arabon()
{
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arabon-activator.php';
	Arabon_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-arabon-deactivator.php
 */
function deactivate_arabon()
{
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arabon-deactivator.php';
	Arabon_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_arabon' );
register_deactivation_hook( __FILE__, 'deactivate_arabon' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-arabon.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_arabon()
{

	$plugin = new Arabon();
	$plugin->run();

}

run_arabon();
