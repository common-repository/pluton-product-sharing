<?php
/**
 * Plugin Name:			Pluton Product Sharing
 * Plugin URI:			https://plutonwp.com/extension/pluton-product-sharing/
 * Description:			A simple plugin to add social share buttons to your product page.
 * Version:				1.0.1
 * Author:				PlutonWP
 * Author URI:			https://plutonwp.com/
 * Requires at least:	4.0.0
 * Tested up to:		4.6
 *
 * Text Domain: pluton-product-sharing
 * Domain Path: /languages/
 *
 * @package Pluton_Product_Sharing
 * @category Core
 * @author PlutonWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Pluton_Product_Sharing to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Pluton_Product_Sharing
 */
function Pluton_Product_Sharing() {
	return Pluton_Product_Sharing::instance();
} // End Pluton_Product_Sharing()

Pluton_Product_Sharing();

/**
 * Main Pluton_Product_Sharing Class
 *
 * @class Pluton_Product_Sharing
 * @version	1.0.0
 * @since 1.0.0
 * @package	Pluton_Product_Sharing
 */
final class Pluton_Product_Sharing {
	/**
	 * Pluton_Product_Sharing The single instance of Pluton_Product_Sharing.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'pluton-product-sharing';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.1';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'pps_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'pps_setup' ) );
	}

	/**
	 * Main Pluton_Product_Sharing Instance
	 *
	 * Ensures only one instance of Pluton_Product_Sharing is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Pluton_Product_Sharing()
	 * @return Main Pluton_Product_Sharing instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function pps_load_plugin_textdomain() {
		load_plugin_textdomain( 'pluton-product-sharing', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * Only executes if Pluton or a child theme using Pluton as a parent is active and the extension specific filter returns true.
	 * Child themes can disable this extension using the pluton_Product_Sharing filter
	 * @return void
	 */
	public function pps_setup() {
		$theme = wp_get_theme();

		if ( 'Pluton' == $theme->name || 'pluton' == $theme->template && apply_filters( 'pluton_product_sharing', true ) ) {
			add_action( 'customize_register', array( $this, 'pps_customizer_register' ) );
			add_action( 'customize_preview_init', array( $this, 'pps_customize_preview_js' ) );
			add_action( 'customize_controls_print_styles', array( $this, 'pps_customize_controls_print_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'pps_style' ), 999 );
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'pps_social_share' ), 11 );
			add_filter( 'pluton_head_css', array( $this, 'pps_head_css' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'pps_install_pluton_notice' ) );
		}
	}

	/**
	 * Pluton install
	 * If the user activates the plugin while having a different parent theme active, prompt them to install Pluton.
	 * @since   1.0.0
	 * @return  void
	 */
	public function pps_install_pluton_notice() {
		echo '<div class="notice is-dismissible updated">
				<p>' . esc_html__( 'Pluton Product Sharing requires that you use Pluton as your parent theme.', 'pluton-product-sharing' ) . ' <a href="https://plutonwp.com/">' . esc_html__( 'Install Pluton now', 'pluton-product-sharing' ) . '</a></p>
			</div>';
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function pps_customizer_register( $wp_customize ) {

		/**
	     * Add a new section
	     */
		$wp_customize->add_section( 'pps_section' , array(
		    'title'      	=> esc_html__( 'Pluton Product Sharing', 'pluton-product-sharing' ),
		    'priority'   	=> 161,
		) );

		/**
	     * Borders color
	     */
        $wp_customize->add_setting( 'pps_sharing_borders_color', array(
			'default'			=> '#e9e9e9',
			'sanitize_callback'	=> 'sanitize_hex_color',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new Pluton_Customizer_Color_Control( $wp_customize, 'pps_sharing_borders_color', array(
			'label'			=> esc_html__( 'Borders Color', 'pluton-product-sharing' ),
			'section'		=> 'pps_section',
			'settings'		=> 'pps_sharing_borders_color',
			'priority'		=> 5,
		) ) );

		/**
	     * Icons background color
	     */
        $wp_customize->add_setting( 'pps_sharing_icons_bg', array(
			'default'			=> '#333333',
			'sanitize_callback'	=> 'sanitize_hex_color',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new Pluton_Customizer_Color_Control( $wp_customize, 'pps_sharing_icons_bg', array(
			'label'			=> esc_html__( 'Icons Background Color', 'pluton-product-sharing' ),
			'section'		=> 'pps_section',
			'settings'		=> 'pps_sharing_icons_bg',
			'priority'		=> 5,
		) ) );

		/**
	     * Icons color
	     */
        $wp_customize->add_setting( 'pps_sharing_icons_color', array(
			'default'			=> '#ffffff',
			'sanitize_callback'	=> 'sanitize_hex_color',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new Pluton_Customizer_Color_Control( $wp_customize, 'pps_sharing_icons_color', array(
			'label'			=> esc_html__( 'Icons Color', 'pluton-product-sharing' ),
			'section'		=> 'pps_section',
			'settings'		=> 'pps_sharing_icons_color',
			'priority'		=> 5,
		) ) );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	public function pps_customize_preview_js() {
		wp_enqueue_script( 'pps-customizer', plugins_url( '/assets/js/customizer.min.js', __FILE__ ), array( 'customize-preview' ), '1.1', true );
	}

	/**
	 * Adds CSS for customizer custom controls
	 */
	public static function pps_customize_controls_print_styles() { ?>

		 <style type="text/css" id="pluton-customizer-controls-css">

			/* Icons */
			#accordion-section-pps_section > h3:before { display: inline-block; font-family: "dashicons"; content: "\f108"; width: 20px; height: 20px; font-size: 20px; line-height: 1; text-decoration: inherit; font-weight: 400; font-style: normal; vertical-align: top; text-align: center; -webkit-transition: color .1s ease-in; transition: color .1s ease-in; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; color: #298cba; margin-right: 10px; }

			#accordion-section-pps_section > h3:before { content: "\f237" }

		 </style>

	<?php
	}

	/**
	 * Enqueue style.
	 * @since   1.0.0
	 * @return  void
	 */
	public function pps_style() {

		// Load main stylesheet
		wp_enqueue_style( 'pps-style', plugins_url( '/assets/css/style.min.css', __FILE__ ) );

		// If rtl
		if ( is_RTL() ) {
			wp_enqueue_style( 'pps-style-rtl', plugins_url( '/assets/css/rtl.css', __FILE__ ) );
		}

	}

	/**
	 * Product sharing links
	 */
	public function pps_social_share() {

		$file 		= $this->plugin_path . 'template/social-share.php';
		$theme_file = get_stylesheet_directory() . '/templates/pps/social-share.php';

		if ( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if ( file_exists( $file ) ) {
			include $file;
		}

	}

	/**
	 * Add css in head tag.
	 */
	public function pps_head_css( $output ) {
		
		// Global vars
		$borders 		= get_theme_mod( 'pps_sharing_borders_color', '#e9e9e9' );
		$icons_bg 		= get_theme_mod( 'pps_sharing_icons_bg', '#333333' );
		$icons_color 	= get_theme_mod( 'pps_sharing_icons_color', '#ffffff' );

		// Define css var
		$css = '';

		// Add borders color
		if ( ! empty( $borders ) && '#e9e9e9' != $borders ) {
			$css .= '.woocommerce div.product .entry-share,.woocommerce div.product .entry-share ul li{border-color:'. $borders .';}';
		}

		// Add icon background
		if ( ! empty( $icons_bg ) && '#333333' != $icons_bg ) {
			$css .= '.woocommerce div.product .entry-share ul li a .fa{background-color:'. $icons_bg .';}';
		}

		// Add icon color
		if ( ! empty( $icons_color ) && '#ffffff' != $icons_color ) {
			$css .= '.woocommerce div.product .entry-share ul li a .fa{color:'. $icons_color .';}';
		}
			
		// Return CSS
		if ( ! empty( $css ) ) {
			$output .= '/*PRODUCT SHARE CSS*/'. $css;
		}

		// Return output css
		return $output;

	}

} // End Class
