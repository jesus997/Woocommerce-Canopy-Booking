<?php

class WC_Product_canopy_tour extends WC_Product {
    public function __construct( $product ) {
        $this->product_type = 'canopy_tour';
        parent::__construct( $product );
    }
}