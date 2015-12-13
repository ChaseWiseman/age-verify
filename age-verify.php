<?php
/**
 * The main plugin file.
 *
 * This file loads the main plugin class and gets things running.
 *
 * @since 0.2.6
 *
 * @package Age_Verify
 */

/**
 * Plugin Name: Age Verify
 * Description: A simple way to ask visitors for their age before viewing your site.
 * Author:      Chase Wiseman
 * Author URI:  http://chasewiseman.com
 * Version:     0.3.0
 * Text Domain: age-verify
 * Domain Path: /languages
 */

// Don't allow this file to be accessed directly.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * The main class definition.
 */
require( plugin_dir_path( __FILE__ ) . 'includes/class-age-verify.php' );

// Get the plugin running.
add_action( 'plugins_loaded', array( 'Age_Verify', 'get_instance' ) );

// Check that the admin is loaded.
if ( is_admin() ) {

	/**
	 * The admin class definition.
	 */
	require( plugin_dir_path( __FILE__ ) . 'includes/admin/class-age-verify-admin.php' );

	// Get the plugin's admin running.
	add_action( 'plugins_loaded', array( 'Age_Verify_Admin', 'get_instance' ) );
}
