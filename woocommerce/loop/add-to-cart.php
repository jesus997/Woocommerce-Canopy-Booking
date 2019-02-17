<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $product;
$enable_booking_data = value("enable_booking_data", false, $product->get_id());
$button_text = $product->add_to_cart_text();
if($enable_booking_data || $product->is_type( 'canopytour' ) || $product->is_type( 'variable_canopytour' )) {
	$button_text = __("Book now", "wcb");
}
$button_link = $product->add_to_cart_url();
if($enable_booking_data || $product->is_type( 'canopytour' ) || $product->is_type( 'variable_canopytour' )) {
	$button_link = get_permalink( $product->get_id() );
}
echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
	sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
		esc_url( $button_link ),
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
		esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		esc_html( $button_text )
	),
$product, $args );