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
            "_transportation_schedules" => __("Transportation schedule", $this->wcb)
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
        }
        return $show;
    }

    function tours_add_item_data($cart_item_data, $product_id, $variation_id) {
        foreach ($this->attrs as $attr => $name) {
            if(isset($_REQUEST[$attr])) {
                if($this->tour_filter($attr)) continue;
                $cart_item_data[$attr] = sanitize_text_field($_REQUEST[$attr]);
            }
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

    function tours_calculate_totals( $cart ) {
        if ( ! empty( $cart->cart_contents ) ) {
            foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
                $adults = $cart_item['_tour_adults'];
                $childs = $cart_item['_tour_children'];
                $product = $cart_item['data'];
                $price_adults = $product->get_price();
                $price_childs = get_post_meta( $product->get_id(), 'price_children', true);
                $price_childs = empty($price_childs) ? 0 : $price_childs;
                $new_price = ($price_adults * $adults) + ($price_childs * $childs);
                $cart_item['data']->set_price( $new_price );
                remove_action( 'woocommerce_before_calculate_totals', array($this, 'tours_calculate_totals'), 99 );
            }
            //debug($cart->cart_contents, true);
        }
    }

    function tours_woocommerce_attribute_label($label, $name, $product) {
        if(array_key_exists($label, $this->attrs)) {
            $label = __($this->attrs[$label], $this->wcb);
        }
        return $label;
    }
}