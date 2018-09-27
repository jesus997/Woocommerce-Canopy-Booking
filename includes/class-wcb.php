<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://twitter.com/yosoydev
 * @since      1.0.0
 *
 * @package    WCB
 * @subpackage WCB/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WCB
 * @subpackage WCB/includes
 * @author     Jesus Magallon <magallonj23@gmail.com>
 */
class WCB {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WCB_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $wcb    The string used to uniquely identify this plugin.
	 */
	protected $wcb;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WCB_VERSION' ) ) {
			$this->version = WCB_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->wcb = 'wcb';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WCB_Loader. Orchestrates the hooks of the plugin.
	 * - WCB_i18n. Defines internationalization functionality.
	 * - WCB_Admin. Defines all hooks for the admin area.
	 * - WCB_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcb-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcb-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wcb-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wcb-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/woocommerce/class-woocommerce-custom-product.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/woocommerce/class-woocommerce-extra-data.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcb-acf-plugin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcb-post-types.php';

		$this->loader = new WCB_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WCB_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WCB_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	function acf_google_maps_key() {
		acf_update_setting('google_api_key', 'AIzaSyC6aoeblNPqB5ub3RDd5dw17iWRHczZeD0');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WCB_Admin( $this->get_wcb(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		if ($this->is_woocommerce_active()) {
			$woo_ct = new WCB_Woocommerce_CanopyTour_Product_Type( $this->get_wcb(), $this->get_version() );
			$this->loader->add_action( 'init', $woo_ct, 'register_canopytour_product_type' );
			$this->loader->add_filter( 'product_type_selector', $woo_ct, 'add_canopytour_product' );
			$this->loader->add_filter( 'woocommerce_product_class', $woo_ct, 'get_tour_product_class', 10, 2 ); 
			$this->loader->add_action( 'admin_head', $woo_ct, 'wcb_admin_head' );
			$this->loader->add_action( 'admin_footer', $woo_ct, 'wcb_admin_footer' );
			$this->loader->add_filter( 'woocommerce_product_data_tabs', $woo_ct, 'hide_wcb_data_panel' );

			$this->loader->add_action( 'woocommerce_after_product_attribute_settings', $woo_ct, 'wcb_add_product_attribute_is_highlighted', 10, 2);
			$this->loader->add_action( 'wp_ajax_woocommerce_save_attributes', $woo_ct, 'wcb_ajax_woocommerce_save_attributes', 10);

			$this->loader->add_action( 'woocommerce_product_options_pricing', $woo_ct, 'wcb_children_product_field' );
			$this->loader->add_action( 'save_post', $woo_ct, 'wcb_children_price_save_product' );

			$this->loader->add_action( 'woocommerce_canopytour_add_to_cart', $woo_ct, 'wcb_canopytour_add_to_cart', 30);
			$this->loader->add_filter( 'woocommerce_locate_template', $woo_ct, 'wcb_woocommerce_locate_template', 10, 3 );

			$woo_ed = new WCB_Woocommerce_Extra_Data( $this->get_wcb(), $this->get_version() );
			$this->loader->add_filter( 'woocommerce_add_cart_item_data', $woo_ed,'tours_add_item_data', 10, 3);
			$this->loader->add_filter( 'woocommerce_get_item_data', $woo_ed,'tours_add_item_meta',10, 2);
			$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $woo_ed, 'tours_add_custom_order_line_item_meta', 10, 4);
			$this->loader->add_action( 'woocommerce_before_calculate_totals', $woo_ed, 'tours_calculate_totals', 99 );
			$this->loader->add_filter( 'woocommerce_attribute_label', $woo_ed, 'tours_woocommerce_attribute_label', 10, 3 );

		}

		$this->loader->add_action('acf/init', $this, 'acf_google_maps_key');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WCB_Public( $this->get_wcb(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	private function is_woocommerce_active() {
		if(!class_exists("WooCommerce")) {
			if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_wcb() {
		return $this->wcb;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WCB_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
