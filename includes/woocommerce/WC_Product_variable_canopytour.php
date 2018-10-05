<?php

class WC_Product_Variable_CanopyTour extends WC_Product_Variable {
    public function __construct( $product ) {
        $this->product_type = 'variable_canopytour';
        parent::__construct( $product );
    }

    public function get_type() {
        return 'variable_canopytour';
    }

    public function get_variation_prices( $for_display = false ) {
		$prices = $this->data_store->read_price_data( $this, $for_display );
		if(is_array($prices)) {
            foreach ( $prices as $price_key => $variation_prices ) {
                $prices[ $price_key ] = $this->sort_variation_prices( $variation_prices );
            }
        }
		return $prices;
	}
}