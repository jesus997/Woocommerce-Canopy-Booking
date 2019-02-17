<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */
defined( 'ABSPATH' ) || exit;
global $product;
if ( ! $product->is_purchasable() ) {
	return;
}
echo wc_get_stock_html( $product ); // WPCS: XSS ok.

$schedule = value("schedule", false, $product->get_id());
$transportation = getTransportationSchedule($schedule);
$ddate = wcb_request("date", "");
$dadults = wcb_request("adults", 1);
$dchildren = wcb_request("children", 0);

if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php do_action( 'woocommerce_before_add_to_cart_quantity' );
		wc_get_template("global/product-canopy-inputs.php", [
			"is_vehicle_tour" => false,
			"schedule" => $schedule,
            "product" => $product,
            "ddate" => $ddate,
			"dadults" => $dadults,
			"dchildren" => $dchildren,
			"transportation" => $transportation
		]);
		do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

    <?php do_action( 'woocommerce_after_add_to_cart_form' );
    
    wc_get_template("global/product-canopy-js.php", [
        "transportation" => $transportation,
        "product" => $product,
        "ddate" => $ddate
	]);
    
endif;