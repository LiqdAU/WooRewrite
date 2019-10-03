<?php
/**
 * Woocommerce Hierarchical URLs
 *
 * Plugin Name: WooRewrite
 * Plugin URI:  https://www.liqd.com.au/wordpress/
 * Description: Rewrites WooCommerce URLs to use a hierarchical format.
 * Version:     1.0
 * Author:      Bryce Gough
 * Author URI:  https://www.liqd.com.au/
 * Text Domain: woo-urls
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

require_once(__DIR__ . '/woorewrite.php');
WooRewrite::set(new WooRewrite());
