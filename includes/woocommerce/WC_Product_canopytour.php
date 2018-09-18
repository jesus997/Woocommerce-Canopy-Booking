<?php

class WC_Product_CanopyTour extends WC_Product {
    public function __construct( $product ) {
        $this->product_type = 'canopytour';
        $this->virtual      = 'yes';
        parent::__construct( $product );
    }

    public function get_type() {
        return 'canopytour';
    }
}