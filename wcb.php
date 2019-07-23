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
 * Version:           2.3.6
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
define( 'WCB_VERSION', '2.3.6' );

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
        if(is_bool($tmp) && is_bool($default)) {
            return $tmp;
        }
		return !$tmp ? $default : $tmp;
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

function dd($data, $die=false) {
	echo "<pre>"; print_r($data); echo "</pre>";
	if($die) die();
}

function humanize_souvenirs_array(array $souvenirs) {
    $souvenirs_humanized = [];
    foreach ($souvenirs as $key => $souvenir) {
        $souvenirs_humanized[] = $souvenir["amount"]." ".$souvenir["name"];
    }
    return humanize_array_list($souvenirs_humanized);
}

function humanize_array_list(array $array) {
    $glue = __("and", "wcb");
    array_splice($array, -2, 2, implode(" </strong>$glue<strong> ", array_slice($array, -2)));
    return "<strong>".implode('</strong>, <strong>', $array)."</strong>";
}

/**
 * Add a discount to an Orders programmatically
 * (Using the FEE API - A negative fee)
 * Source: https://stackoverflow.com/a/52657462
 *
 * @since  3.2.0
 * @param  int     $order_id  The order ID. Required.
 * @param  string  $title  The label name for the discount. Required.
 * @param  mixed   $amount  Fixed amount (float) or percentage based on the subtotal. Required.
 * @param  string  $tax_class  The tax Class. '' by default. Optional.
 */
function wc_order_add_discount( $order_id, $title, $amount, $tax_class = '' ) {
    $order    = wc_get_order($order_id);
    $subtotal = $order->get_subtotal();
    $item     = new WC_Order_Item_Fee();

    if ( strpos($amount, '%') !== false ) {
        $percentage = (float) str_replace( array('%', ' '), array('', ''), $amount );
        $percentage = $percentage > 100 ? -100 : -$percentage;
        $discount   = $percentage * $subtotal / 100;
    } else {
        $discount = (float) str_replace( ' ', '', $amount );
        $discount = $discount > $subtotal ? -$subtotal : -$discount;
    }

    $item->set_tax_class( $tax_class );
    $item->set_name( $title );
    $item->set_amount( $discount );
    $item->set_total( $discount );

    if ( '0' !== $item->get_tax_class() && 'taxable' === $item->get_tax_status() && wc_tax_enabled() ) {
        $tax_for   = array(
            'country'   => $order->get_shipping_country(),
            'state'     => $order->get_shipping_state(),
            'postcode'  => $order->get_shipping_postcode(),
            'city'      => $order->get_shipping_city(),
            'tax_class' => $item->get_tax_class(),
        );
        $tax_rates = WC_Tax::find_rates( $tax_for );
        $taxes     = WC_Tax::calc_tax( $item->get_total(), $tax_rates, false );
        print_pr($taxes);

        if ( method_exists( $item, 'get_subtotal' ) ) {
            $subtotal_taxes = WC_Tax::calc_tax( $item->get_subtotal(), $tax_rates, false );
            $item->set_taxes( array( 'total' => $taxes, 'subtotal' => $subtotal_taxes ) );
            $item->set_total_tax( array_sum($taxes) );
        } else {
            $item->set_taxes( array( 'total' => $taxes ) );
            $item->set_total_tax( array_sum($taxes) );
        }
        $has_taxes = true;
    } else {
        $item->set_taxes( false );
        $has_taxes = false;
    }
    $item->save();

    $order->add_item( $item );
    $order->calculate_totals( $has_taxes );
    $order->save();
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wcb.php';

/**
 * Add updates system
 */
require plugin_dir_path( __FILE__ ) . 'includes/updater/plugin-update-checker.php';

$wcbUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jesus997/Woocommerce-Canopy-Booking/',
	__FILE__,
	'Woocommerce-Canopy-Booking'
);
$wcbUpdateChecker->setBranch('production');

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
