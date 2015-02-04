<?php
/*
Plugin Name: WC Wholesale Categories
Plugin URI: http://patternsinthecloud.com
Description: Create wholesale product categories in WooCommerce
Version: 1.0
Author: Patterns in the Cloud
Author URI: http://patternsinthecloud.com
License: Single-site
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	
	/**
	 * Activate hook
	 */
	function wc_wholesale_categories_activate() {
		
	}
	register_activation_hook( __FILE__, 'wc_wholesale_categories_activate' );
	
	/**
	 * Deactivate hook
	 */
	function wc_wholesale_categories_deactivate() {
		
	}
	register_deactivation_hook( __FILE__, 'wc_wholesale_categories_deactivate' );
	
	/**
	 * Uninstall hook
	 */
	function wc_wholesale_categories_uninstall() {
		
	}
	register_uninstall_hook( __FILE__, 'wc_wholesale_categories_uninstall' );
	
	/**
	 * Filter wholesale categories based on user role
	 * @param array $args
	 */
	function wc_wholesale_categories_args( $args ) {
		
	}
	add_filter( 'woocommerce_product_subcategories_args', 'wc_wholesale_categories_args' );
}
