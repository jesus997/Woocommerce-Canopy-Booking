<?php

class WC_Product_Variable_CanopyTour extends WC_Product_Variable {
    public function __construct( $product=0 ) {
        $this->product_type = 'variable_canopytour';
        parent::__construct( $product );
    }

    public function get_type() {
        return 'variable_canopytour';
    }
}