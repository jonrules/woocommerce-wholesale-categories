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
		// Wholesale category
		if ( ! term_exists( 'wholesale', 'product_cat' ) ) {
			wp_insert_term(
				'Wholesale',
				'product_cat',
				array(
					'description'=> 'Wholesale products',
					'slug' => 'wholesale',
				)
			);
		}
		
		// Wholesaler role
		$role = 'wholesaler';
		$display_name = 'Wholesaler';
		$customer_role = get_role( 'customer' );
		$capabilities = $customer_role->capabilities;
		add_role( $role, $display_name, $capabilities );
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
		if ( wc_wholesale_categories_user_is_wholesaler( get_current_user_id() ) ) {
			return $args;
		}
		$exclude = array();
		$category_slugs = wc_wholesale_categories_get_category_slugs();
		foreach ( $category_slugs as $slug ) {
			$category = get_category_by_slug( $slug );
			$exclude[] = $category->term_id;
		}
		$args['exclude'] = implode( ',', $exclude );
		return $args;
	}
	add_filter( 'woocommerce_product_categories_widget_args', 'wc_wholesale_categories_args', 10, 1 );
	add_filter( 'woocommerce_product_categories_widget_dropdown_args', 'wc_wholesale_categories_args', 10, 1 );
	
	/**
	 * Get the wholesale category slugs
	 * @return string[]
	 */
	function wc_wholesale_categories_get_category_slugs() {
		return array( 'wholesale' );
	}
	
	/**
	 * Product is visible
	 * @param boolean $visible
	 * @param int $product_id
	 * @return boolean
	 */
	function wc_wholesale_categories_product_is_visible( $visible, $product_id ) {
		if ( wc_wholesale_categories_user_is_wholesaler( get_current_user_id() ) ) {
			return $visible;
		}
		$category_slugs = wc_wholesale_categories_get_category_slugs();
		if ( wc_wholesale_categories_is_in_category( $product_id, $category_slugs ) ) {
			return false;
		}
		return $visible;
	}
	add_filter( 'woocommerce_product_is_visible', 'wc_wholesale_categories_product_is_visible', 10, 2 );
	
	/**
	 * Product is purchasable
	 * @param boolean $purchasable
	 * @param WC_Product $product
	 * @return boolean
	 */
	function wc_wholesale_categories_product_is_purchasable( $purchasable, $product ) {
		if ( wc_wholesale_categories_user_is_wholesaler( get_current_user_id() ) ) {
			return $purchasable;
		}
		$category_slugs = wc_wholesale_categories_get_category_slugs();
		if ( wc_wholesale_categories_is_in_category( $product->id, $category_slugs ) ) {
			return false;
		}
		return $purchasable;
	}
	add_filter( 'woocommerce_is_purchasable', 'wc_wholesale_categories_product_is_purchasable', 10, 2 );
	
	/**
	 * Product is in category
	 * @param int $product_id
	 * @param array $category_slugs
	 * @return boolean
	 */
	function wc_wholesale_categories_is_in_category( $product_id, $category_slugs ) {
		$terms = wp_get_post_terms( $product_id, 'product_cat' );
		foreach ( $terms as $term ) {
			if ( in_array( $term->slug, $category_slugs ) ) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Check if user has the wholesaler role
	 * @param int $user_id
	 * @return boolean
	 */
	function wc_wholesale_categories_user_is_wholesaler( $user_id ) {
		$user = get_userdata( $user_id );
		if ( empty( $user ) ) {
			return false;
		}
		return ( 
			in_array( 'wholesaler', (array) $user->roles )
				|| in_array( 'shop_manager', (array) $user->roles )
				|| user_can( $user_id, 'manage_options' )
		);
	}
	
}
