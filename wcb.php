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
 * Version:           2.2.0
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
define( 'WCB_VERSION', '2.2.0' );

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

function wcb_plugin_path() {
	return untrailingslashit( plugin_dir_path( __FILE__ ) );
}

function get_attribute_highlighted($id, $i) {
	global $post;
	$id = sanitize_title($id);
	$id = strtolower($id);
	$val = "";
	if(!empty($post->ID)) {
		$val = get_post_meta( $post->ID, "attribute_".$id."_highlighted_".$i, true);
	}
	return !empty($val) ? $val : false;
}

function woocommerce_canopytour_add_to_cart() {
	wc_get_template( "single-product/add-to-cart/canopytour.php" );
}

function woocommerce_variable_canopytour_add_to_cart() {
	wc_get_template( "single-product/add-to-cart/variable-canopytour.php" );
}

function value($key, $default=false, $id=false) {
	if (substr($key, 0, 1) === '_') {
		if(!$id) {
			global $post;
			$id = $post->ID;
		}
		$tmp = get_post_meta($id, $key, true);
		return trim($tmp) !== "" ? $tmp : $default;
	} else if(function_exists("get_field")) {
		$tmp = get_field($key, $id);
		return $tmp ? $tmp : $default;
	}
	return $default;
}

function getDatesFromRange($first, $last) {
	$fromdate = \DateTime::createFromFormat('d/m/Y', $first);
    $todate = \DateTime::createFromFormat('d/m/Y', $last);
    $datarr = new \DatePeriod(
        $fromdate,
        new \DateInterval('P1D'),
        $todate->modify('+1 day')
	);
	$dates = [];
	foreach($datarr as $date) {
		$dates[] = $date->format('d/m/Y');
	}
	return $dates;
}

function simplificyDates($dates) {
	$simplificy = [];
	foreach($dates as $date) {
		if($date["type"] === "single") {
			if(empty($date["date"])) continue;
			array_push($simplificy, $date["date"]);
		} else {
			$arrdate = getDatesFromRange($date["date_range"]["start"], $date["date_range"]["end"]);
			if(empty($arrdate)) continue;
			$simplificy = array_merge($simplificy, $arrdate);
		}
	}

	return $simplificy;
}

function getTransportationSchedule($schedule=[]) {
	$transportation = [];
	if($schedule && is_array($schedule)) {
		foreach($schedule as $time) {
			$stops = [];
			foreach($time["stops"] as $stop) {
				$stops[ get_the_title( $stop["stop"] ) ] = $stop["schedule"];
			}
			$transportation[$time['schedule']] = $stops;
		}
	}
	return $transportation;
}

function getBlockedDays($product) {
	$blocked_days = value("blocked_days", [], "wcb-options");
	$sbd = value("blocked_days", [], $product->get_id());
	$blocked_days = array_merge($blocked_days, $sbd);
	$blocked_days = array_unique($blocked_days);
	return $blocked_days;
}

function getDatesBlocked($product) {
	$dates_blocked = value("dates_blocked", [], "wcb-options");
	$sdb = value("dates_blocked", [], $product->get_id());
	$dates_blocked = array_merge($dates_blocked, $sdb);
	return $dates_blocked;
}

function wcb_request($key, $default) {
	if(array_key_exists($key, $_GET)) {
		return $_GET[$key];
	} else if(array_key_exists($key, $_POST)) {
		return $_POST[$key];
	} else if(array_key_exists($key, $_REQUEST)) {
		return $_REQUEST[$key];
	}
	return $default;
}

if( ! function_exists('wc_display_item_meta') ) {

function wc_display_item_meta( $item, $args = array() ) {
    $strings = array();
    $html    = '';
    $args    = wp_parse_args( $args, array(
        'before'    => '<ul class="wc-item-meta"><li>',
        'after'     => '</li></ul>',
        'separator' => '</li><li>',
        'echo'      => true,
        'autop'     => false,
    ) );
    foreach ( $item->get_formatted_meta_data("__") as $meta_id => $meta ) {
        $value     = $args['autop'] ? wp_kses_post( $meta->display_value ) : wp_kses_post( make_clickable( trim( $meta->display_value ) ) );
        $strings[] = '<strong class="wc-item-meta-label">' . wp_kses_post( $meta->display_key ) . ':</strong> ' . $value;
    }
    if ( $strings ) {
        $html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
    }
    $html = apply_filters( 'woocommerce_display_item_meta', $html, $item, $args );
    if ( $args['echo'] ) {
        echo $html; // WPCS: XSS ok.
    } else {
        return $html;
    }
}

}

if( ! function_exists('woocommerce_template_loop_add_to_cart') ) {

function woocommerce_template_loop_add_to_cart( $args = array() ) { 
    global $product; 
 
    if ( $product ) {
        $defaults = array( 
            'quantity' => 1,  
            'class' => implode( ' ', array_filter( array( 
                    'button',  
                    'product_type_' . $product->get_type(),  
                    $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                    //$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
            ) ) )
        );
 
        $args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );
 
        wc_get_template( 'loop/add-to-cart.php', $args ); 
    } 
}

}

function request($id, $default=false, $method="post") {
	$requests = ($method === "get") ? $_GET : ($method === "request") ? $_REQUEST : $_POST;
	return isset($requests[$id]) ? $requests[$id] : $default;
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
