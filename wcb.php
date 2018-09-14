<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://twitter.com/yosoydev
 * @since             1.0.0
 * @package           WCB
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Canopy Booking
 * Plugin URI:        https://twitter.com/yosoydev
 * Description:       This plugin adds the functionality of a Booking for websites dedicated to the sale of Canopy Tours.
 * Version:           1.0.0
 * Author:            Jes&uacute;s Magall&oacute;n
 * Author URI:        https://twitter.com/yosoydev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wcb
 * Domain Path:       /languages
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
define( 'WCB_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wcb-activator.php
 */
function activate_wcb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcb-activator.php';
	WCB_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wcb-deactivator.php
 */
function deactivate_wcb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcb-deactivator.php';
	WCB_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wcb' );
register_deactivation_hook( __FILE__, 'deactivate_wcb' );

function get_attribute_highlighted($id, $i) {
	global $post;
	$id = sanitize_title($id);
	$id = strtolower($id);
	$val = get_post_meta( $post->ID, "attribute_".$id."_highlighted_".$i, true);
	return !empty($val) ? $val : false;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wcb.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wcb() {

	$plugin = new WCB();
	$plugin->run();

}
run_wcb();
