<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://twitter.com/yosoydev
 * @since      1.0.0
 *
 * @package    WCB
 * @subpackage WCB/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    WCB
 * @subpackage WCB/public
 * @author     Jesus Magallon <magallonj23@gmail.com>
 */
class WCB_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wcb    The ID of this plugin.
	 */
	private $wcb;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wcb       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wcb, $version ) {

		$this->wcb = $wcb;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WCB_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WCB_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->wcb, plugin_dir_url( __FILE__ ) . 'css/wcb-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WCB_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WCB_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->wcb, plugin_dir_url( __FILE__ ) . 'js/wcb-public.js', array( 'jquery' ), $this->version, false );

	}

}
