<?php

class WCB_Woocommerce_CanopyTour_Product_Type {
	private $wcb;
	private $version;

	public function __construct( $wcb, $version ) {
		$this->wcb = $wcb;
		$this->version = $version;
	}

	public function register_canopytour_product_type() {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		
		include_once(plugin_dir_path( dirname( __FILE__ ) ) . 'woocommerce/WC_Product_canopytour.php');
		//include_once(plugin_dir_path( dirname( __FILE__ ) ) . 'woocommerce/WC_Product_variable_canopytour.php');
	}

	public function add_canopytour_product( $types ) {
		$types[ 'canopytour' ] = __( 'Canopy Tour', $this->wcb );
		//$types[ 'variable_canopytour' ] = __( 'Variable Canopy Tour', $this->wcb );
		return $types;
	}

	public function get_tour_product_class($classname, $product_type) {
		if ( $product_type === "canopytour" ) {
			$classname = 'WC_Product_CanopyTour';
		} else if ( $product_type === "variable_canopytour" ) {
			$classname = 'WC_Product_Variable_CanopyTour';
		}
		return $classname;
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
				jQuery( '.options_group.pricing' ).addClass( 'show_if_canopytour show_if_simple show_if_external' ).show();
				jQuery( 'li.general_options.general_tab' ).addClass( 'show_if_canopytour show_if_simple show_if_external' ).show();
				jQuery( '.variations_options.variations_tab' ).addClass( 'show_if_variable_canopytour' ).show();
				jQuery( '.enable_variation' ).addClass( 'show_if_variable_canopytour' ).show();
				jQuery( '#acf-group_5b9a99cc823ec' ).addClass( 'show_if_canopytour show_if_variable_canopytour' );
			});
		</script><?php
	}

	function hide_wcb_data_panel( $tabs) {
		// Other default values for 'attribute' are; general, inventory, shipping, linked_product, variations, advanced
		$tabs['shipping']['class'][] = 'hide_if_canopytour hide_if_variable_canopytour';
		return $tabs;
	}

	function wcb_add_product_attribute_is_highlighted($attribute, $i=0) {
		$value = get_attribute_highlighted($attribute->get_name(), $i); ?>
		<tr>
			<td>
				<div class="enable_highlighted show_if_canopytour show_if_variable_canopytour">
					<label><input type="hidden" name="attribute_highlighted[<?php echo esc_attr( $i ); ?>]" value="0" /><input type="checkbox" class="checkbox" <?php checked( $value, true ); ?> name="attribute_highlighted[<?php echo esc_attr( $i ); ?>]" value="1" /> <?php esc_html_e( 'Highlight attribute', $this->wcb ); ?></label>
				</div>
			</td>
		</tr>
	<?php
	}

	function wcb_add_product_enable_booking_data_to_variation_product_type() {
		global $post;
		$product = wc_get_product($post->ID);
		if($product->is_type("variable")) {
			woocommerce_wp_checkbox([
				'id' => 'enable_booking_data',
				'label' => __( 'Enable Booking Data', $this->wcb ),
				'class' => 'cwb-enable-booking-data',
				//'desc_tip' => true,
				'description' => __( 'Allows you to add fields for the selection of date, timetables and transport stop.', $this->wcb ),
			]);
		}
	}

	function wcb_save_enable_booking_data_field( $post_id ) {
		$product = wc_get_product( $post_id );
		$check = isset( $_POST['enable_booking_data'] ) ? $_POST['enable_booking_data'] : '0';
		$product->update_meta_data( 'enable_booking_data', sanitize_text_field( $check ) );
		$product->save();
	}

	function wcb_ajax_woocommerce_save_attributes() {
		check_ajax_referer( 'save-attributes', 'security' );
		parse_str( $_POST['data'], $data );
		$post_id = absint( $_POST['post_id'] );
		if(array_key_exists("attribute_highlighted", $data) && is_array($data["attribute_highlighted"])) {
			foreach($data["attribute_highlighted"] as $i => $val) {
				$attr_name = sanitize_title($data["attribute_names"][$i]);
				$attr_name = strtolower($attr_name);
				update_post_meta( $post_id, "attribute_".$attr_name."_highlighted_".$i, wc_string_to_bool($val) );
			}
		}
	}
 
	function wcb_admin_meta_boxes_prepare_attribute($attribute, $data, $i=0) {
		if(array_key_exists("attribute_highlighted", $data) && is_array($data["attribute_highlighted"])) {
			global $post;
			update_post_meta( $post->ID, "attribute_".$attribute->get_id()."_highlighted_".$i, wc_string_to_bool($data["attribute_highlighted"][$i]) );
		}
		return $attribute;
	}

	/**
	 * FIELDS
	 */

	function wcb_children_product_field() {
		woocommerce_wp_text_input( array( 'id' => 'second_price', 'class' => 'wc_input_price short', 'label' => __( 'Second Price', $this->wcb ) . ' (' . get_woocommerce_currency_symbol() . ')' ) );
	}

	function wcb_children_price_save_product( $product_id ) {

		// stop the quick edit interferring as this will stop it saving properly, when a user uses quick edit feature
		if (isset($_POST['_inline_edit']) && wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))
		   return;
   
	   // If this is a auto save do nothing, we only save when update button is clicked
	   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		   return;
	   if ( isset( $_POST['second_price'] ) ) {
		   if ( is_numeric( $_POST['second_price'] ) )
			   update_post_meta( $product_id, 'second_price', $_POST['second_price'] );
	   } else delete_post_meta( $product_id, 'second_price' );
	}

	function wcb_canopytour_add_to_cart() {
		woocommerce_canopytour_add_to_cart();
	}

	function wcb_variable_canopytour_add_to_cart() {
		woocommerce_variable_canopytour_add_to_cart();
	}

	function wcb_variable_canopytour_add_to_cart_button() {
		wc_get_template( 'single-product/add-to-cart/variable-add-to-cart-button.php' );
	}

	function wcb_woocommerce_locate_template( $template, $template_name, $template_path ) {
		global $woocommerce;
		$_template = $template;
		if ( ! $template_path ) $template_path = $woocommerce->template_url;
		$plugin_path  = wcb_plugin_path() . '/woocommerce/';

		$template = locate_template(
		  array(
			$template_path . $template_name,
			$template_name
		  )
		);

		if ( ! $template && file_exists( $plugin_path . $template_name ) )
		  $template = $plugin_path . $template_name;
	  
		if ( ! $template )
		  $template = $_template;
	  
		return $template;
	}

	function wcb_add_woocommerce_data_store ($stores){
		//$stores['product-variable_canopytour'] = 'WC_Product_Variable_Data_Store_CPT';
		return $stores;
	}
}