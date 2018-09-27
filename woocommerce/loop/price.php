<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product;
?>

<?php if ( $price_html = $product->get_price_html() ) :
	$children_price = value("price_children", 0, $product->get_id());
	if($product->is_type( 'canopytour' ) && $children_price > 0) : ?>
		<span class="price"><?= __("From ", "wcb")." ".wc_price($children_price); ?></span> <?php
	else: ?>
		<span class="price"><?= __("From ", "wcb")." ".$price_html; ?></span> <?php
	endif; ?>
<?php endif; ?>