<?php

class WCB_Woocommerce_Extra_Data {
	private $wcb;
    private $version;
    private $attrs;

	public function __construct( $wcb, $version ) {
		$this->wcb = $wcb;
        $this->version = $version;
        $this->attrs = [
            "_tour_date"        => __("Date of activity", $this->wcb),
            "_tour_schedule"   => __("Activity schedule", $this->wcb),
            "_tour_adults"      => __("Adults", $this->wcb),
            "_tour_children"      => __("Children", $this->wcb),
            "_need_transportation"  => __("Pick-up place", $this->wcb),
            "_transportation_schedules" => __("Pick-up schedule", $this->wcb),
            "_early_discount" => __("Discount", $this->wcb)
        ];
    }
    
    function tour_filter($attr) {
        $show = false;
        switch($attr) {
            case "_transportation_schedules":
                $show = !isset($_REQUEST["_need_transportation"]) || $_REQUEST["_need_transportation"] === "No";
                break;
            case "_tour_children":
                $show = !isset($_REQUEST["_tour_children"]) || $_REQUEST["_tour_children"] === "0";
                break;
            case "_early_discount":
                $show = !isset($_REQUEST["__early_discount"]) || $_REQUEST["__early_discount"] === false;
                break;
        }
        return $show;
    }

    function tours_add_item_data($cart_item_data, $product_id, $variation_id) {
        $tour_date = date("d/m/Y");
        foreach ($this->attrs as $attr => $name) {
            if(isset($_REQUEST[$attr])) {
                if($this->tour_filter($attr))continue;
                $cart_item_data[$attr] = sanitize_text_field($_REQUEST[$attr]);
                if($attr === "_tour_date") {
                    $tour_date = sanitize_text_field($_REQUEST[$attr]);
                }
            }
        }
        $discount = $this->calculate_date_discount($tour_date);
        if($discount > 0) {
            $cart_item_data["_early_discount"] = "-$discount% OFF";
        }
        return $cart_item_data;
    }

    function tours_add_item_meta($item_data, $cart_item) {
        foreach ($this->attrs as $attr => $name) {
            if(array_key_exists($attr, $cart_item)) {
                $custom_details = $cart_item[$attr];
                $item_data[] = array(
                    'key'   => __($name, $this->wcb),
                    'value' => $custom_details
                );
            }
        }
        return $item_data;
    }

    function tours_add_custom_order_line_item_meta($item, $cart_item_key, $values, $order) {
        foreach ($this->attrs as $attr => $name) {
            if(array_key_exists($attr, $values)) {
                $item->add_meta_data($attr,$values[$attr]);
            }
        }
    }

    function calculate_date_discount($tour_date) {
        $edeb = value("enable_discount_for_early_booking", true, "wcb-options");
        $d = 0;
        if($edeb) {
            $dbr = value("days_before_the_reservation", 30, "wcb-options");
            $ptd = value("percentage_to_discount", 15, "wcb-options");
            $today = DateTime::createFromFormat("d/m/Y", date("d/m/Y"));
            $tour_date = DateTime::createFromFormat("d/m/Y", $tour_date);

            if($today && $tour_date) {
                $diff = $tour_date->diff($today)->format("%a");
                if(intval($diff) >= $dbr) {
                    return intval($ptd);
                }
            }
        }
        return $d;
    }

    function get_variation_price_by_id($product_id, $variation_id){
        $product = new WC_Product_Variable($product_id);
        $variations = $product->get_available_variations();
        foreach ($variations as $variation) {
            if($variation['variation_id'] == $variation_id){
                $regular_price = $variation['display_regular_price'];
                $price = $variation['display_price'];
            }
        }
     
        $priceArray = array(
            'regular_price' => $regular_price,
            'price' => $price
        );
        $priceObject = (object)$priceArray;
        return $priceObject;
    }

    function tours_calculate_totals( $cart ) {
        if ( ! empty( $cart->cart_contents ) ) {
            foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
                $product = $cart_item['data'];
                if($product->is_type( 'canopytour' )) {
                    $adults = isset($cart_item['_tour_adults']) ? $cart_item['_tour_adults'] : 1;
                    $childs = isset($cart_item['_tour_children']) ? $cart_item['_tour_children'] : 0;
                    $rdate = isset($cart_item['_tour_date']) ? $cart_item['_tour_date'] : date("d/m/Y");
                    $discount = $this->calculate_date_discount($rdate);
                    $regular_price = $product->get_price();
                    $second_price = get_post_meta( $product->get_id(), 'second_price', true);
                    $second_price = empty($second_price) ? 0 : $second_price;
                    $new_price = ($regular_price * $adults) + ($second_price * $childs);
                    if($discount > 0) {
                        $new_price -= ($new_price * $discount)/100;
                    }
                    $cart_item['data']->set_price( $new_price );
                    remove_action( 'woocommerce_before_calculate_totals', array($this, 'tours_calculate_totals'), 99 );
                } else if( get_post_type($product->get_id()) === "product_variation" ) {
                    $rdate = isset($cart_item['_tour_date']) ? $cart_item['_tour_date'] : date("d/m/Y");
                    $discount = $this->calculate_date_discount($rdate);
                    $pid = $cart_item['product_id'];
                    $vid = $cart_item['variation_id'];
                    $varitem = $this->get_variation_price_by_id($pid, $vid);
                    $regular_price = $varitem->regular_price;
                    $qty = $cart_item['quantity'];
                    $new_price = ($regular_price * $qty);
                    if($discount > 0) {
                        $new_price -= ($new_price * $discount)/100;
                    }
                    $cart_item['data']->set_price( $new_price );
                    remove_action( 'woocommerce_before_calculate_totals', array($this, 'tours_calculate_totals'), 99 );
                }
            }
            //echo "<pre>"; print_r($cart->cart_contents); echo "</pre>";die();
        }
    }

    function tours_woocommerce_attribute_label($label, $name, $product) {
        if(array_key_exists($label, $this->attrs)) {
            $label = __($this->attrs[$label], $this->wcb);
        }
        return $label;
    }

    function wcb_currency_symbol( $format, $currency_pos ) {
        $currency = get_woocommerce_currency();
        switch ( $currency_pos ) {
            case 'left' :
                $format = '%1$s%2$s&nbsp;'.$currency;
                break;
            case 'right':
                $format = $currency.'%1$s%2$s&nbsp;';
                break;
            default:
                $format = '%1$s%2$s&nbsp;';
        }
        return $format;
    }
}