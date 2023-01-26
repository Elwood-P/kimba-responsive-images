<?php

/*
 * @wordpress-plugin
 * Plugin Name: Kimba Responsive Images
 * Plugin URI: https://kimba.design
 * Description: TODO
 * Version: 0.1
 * Author: Paul Littlewood
 * Author URI: https://kimba.design
 * License: GPL v2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: km-responsive-images
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}


// Main Class
class KM_Responsive_Images
{

	public function __construct()
	{
		// Set constants
		self::define_constants();

		// Hooks
		add_action('after_setup_theme', array($this, 'load_composer_deps'));
		register_uninstall_hook(__FILE__, array('KM_Responsive_Images', 'uninstall'));
		add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
		add_filter( 'script_loader_tag', function ( $tag, $handle ) {
			if ( 'lazysizes.min.js' !== $handle ) {
				return $tag;
			}
			return str_replace( ' src', ' async="" src', $tag ); // OR async the script
		}, 10, 2 );


		// Init Classes and functions
		require_once KM_RESPONSIVE_IMAGES_PATH . 'includes/class-km-responsive-images-defaults.php';
		$plugin_defaults = new KM_Responsive_Images_Defaults();
		require_once KM_RESPONSIVE_IMAGES_PATH . 'includes/class-km-responsive-images-admin.php';
		$plugin_admin = new KM_Responsive_Images_Admin();
		require_once KM_RESPONSIVE_IMAGES_PATH . 'includes/class-km-responsive-images-image.php';

		// Init public functions
		require_once KM_RESPONSIVE_IMAGES_PATH . 'functions/km-responsive-images-functions.php';
	}

	public function define_constants()
	{
		define('KM_RESPONSIVE_IMAGES_PATH', plugin_dir_path(__FILE__));
		define('KM_RESPONSIVE_IMAGES_URL', plugin_dir_url(__FILE__));
		define('KM_RESPONSIVE_IMAGES_VERSION', '0.0.1');
	}

	// Load Composer dependencies
	public static function load_composer_deps()
	{
		if (is_readable(__DIR__ . '/vendor/autoload.php')) {
			require __DIR__ . '/vendor/autoload.php';
			// Start Carbon Fields (used for admin options) - autoloaded by composer 
			// @link https://carbonfields.net/docs/carbon-fields-quickstart/
			\Carbon_Fields\Carbon_Fields::boot();
		}
	}

	public static function frontend_scripts() {
		wp_enqueue_script( 'lazysizes.min.js', KM_RESPONSIVE_IMAGES_URL . 'assets/js/vendor/lazysizes.min.js', array(), 'v5.3.2' );
	}

	public static function uninstall()
	{
		// Remove all Carbon Field options stored in wp_options table
		delete_option('_km_imgix_domain');
		delete_option('_km_min_width');
		delete_option('_km_max_width');
		delete_option('_km_steps');
		delete_option('_km_min_step');
	}
}


// Start Plugin
if (class_exists('KM_Responsive_Images')) {
	//echo console_log($output);
	add_filter( 'intermediate_image_sizes', '__return_empty_array' );
	$km_responsive_images = new KM_Responsive_Images();
}

// $km_responsive_images = new KM_Responsive_Images();

// function console_log($output, $with_script_tags = true)
// {
// 	$js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
// 	if ($with_script_tags) {
// 		$js_code = '<script>' . $js_code . '</script>';
// 	}
// 	echo $js_code;
// }
// $output = var_export(KM_Responsive_Images_Defaults::$user_defaults, true);
