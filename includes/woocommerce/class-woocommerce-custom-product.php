<?php

class WCB_Woocommerce_Canopy_Tour_Product_Type {
	private $wcb;
	private $version;

	public function __construct( $wcb, $version ) {
		$this->wcb = $wcb;
		$this->version = $version;
	}

	public function register_canopy_tour_product_type() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'woocommerce/WC_Product_canopy_tour.php';
	}

	public function add_canopy_tour_product( $types ) {
		$types[ 'canopy_tour' ] = __( 'Canopy Tour', $this->wcb );
		return $types;
	}

	public function wcb_admin_head() {
		//
	}

	public function wcb_admin_footer() {
		if ( 'product' != get_post_type() ) :
			return;
		endif;
	
		?><script type='text/javascript'>
			jQuery( document ).ready( function() {
				jQuery( '.options_group.pricing' ).addClass( 'show_if_canopy_tour show_if_variable_canopy_tour show_if_simple show_if_external' ).show();
				jQuery( 'li.general_options.general_tab' ).addClass( 'show_if_canopy_tour show_if_variable_canopy_tour show_if_simple show_if_external' ).show();
			});
		</script><?php
	}

	public function add_canopy_tour_tab($tabs) {
		$tabs['canopy_tour'] = array(
			'label'		=> __( 'Canopy Tour', 'woocommerce' ),
			'target'	=> 'canopy_tour_options',
			'class'		=> array( 'show_if_canopy_tour', 'show_if_variable_canopy_tour'  ),
		);
		return $tabs;
	}

	public function canopy_tour_options_product_tab_content() {
		global $post; ?>

		<div id='canopy_tour_options' class='panel woocommerce_options_panel'><?php
			?><div class='options_group'><?php
				woocommerce_wp_textarea_input(
					array(
					'id' => '_activity_schedules',
					'label' => __( 'Schedules', 'dfd-native_child' ),
					'placeholder' => '',
					'description' => __( 'Enter the time available for this activity. Separated by comma.', 'dfd-native_child' ),
					'custom_attributes' => array(
						'rows' => "2"
					)
					)
				);
			
				woocommerce_wp_textarea_input(
					array(
						'id' => '_need_transportation',
						'label' => __( 'Transportation stops', 'dfd-native_child' ),
						'placeholder' => '{"place": ["hour", "other hour", "more hour"]}',
						'description' => __( 'Enter transportation stops for this tour. In JSON format.', 'dfd-native_child' ),
						'custom_attributes' => array(
							'rows' => "2"
						)
					)
				); ?>
			</div>
		</div><?php
	}

	function save_canopy_tour_option_field( $post_id ) {
		$new = $_POST['_activity_schedules'];
		$old = get_post_meta($post_id, "_activity_schedules", true );
		if ($new !== $schedule_old) {
			update_post_meta($post_id, '_activity_schedules', esc_html($new));
		}

		$new = $_POST['_need_transportation'];
		$old = get_post_meta($post_id, "_need_transportation", true );
		if ($new !== $old) {
			update_post_meta($post_id, '_need_transportation', $new);
		}
	}

	function hide_wcb_data_panel( $tabs) {
		// Other default values for 'attribute' are; general, inventory, shipping, linked_product, variations, advanced
		$tabs['shipping']['class'][] = 'hide_if_canopy_tour hide_if_variable_canopy_tour';
		return $tabs;
	}

	/**
	 * FIELDS
	 */

	function wcb_children_product_field() {
		woocommerce_wp_text_input( array( 'id' => 'price_children', 'class' => 'wc_input_price short', 'label' => __( 'Price Children', $this->wcb ) . ' (' . get_woocommerce_currency_symbol() . ')' ) );
	}

	function wcb_children_price_save_product( $product_id ) {

		// stop the quick edit interferring as this will stop it saving properly, when a user uses quick edit feature
		if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))
		   return;
   
	   // If this is a auto save do nothing, we only save when update button is clicked
	   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		   return;
	   if ( isset( $_POST['price_children'] ) ) {
		   if ( is_numeric( $_POST['price_children'] ) )
			   update_post_meta( $product_id, 'price_children', $_POST['price_children'] );
	   } else delete_post_meta( $product_id, 'price_children' );
   }
}