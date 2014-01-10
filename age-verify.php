<?php

/**
 * Plugin Name: Age Verify
 * Description: A simple way to ask visitors for their age before viewing your site.
 * Author:      Chase Wiseman
 * Author URI:  http://chasewiseman.com
 * Version:     0.2.5
 * Text Domain: age_verify
 * Domain Path: /languages/
 *
 * @package   AgeVerify
 * @version   0.2.5
 * @author    Chase Wiseman <contact@chasewiseman.com>
 * @copyright Copyright (c) 2014, Chase Wiseman
 * @link      http://chasewiseman.com/plugins/age-verify/
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

// Don't access this directly, please
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The main Age Verify class.
 *
 * @since 0.1
 */
class Age_Verify {
	
	/**
	 * Sets up everything.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function __construct() {
		
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}
	
	/**
	 * Sets up the globals.
	 *
	 * @since 0.1
	 * @access private
	 */
	private function setup_globals() {
		
		// Directories and URLs
		$this->file            = __FILE__;
		$this->basename        = plugin_basename( $this->file );
		$this->plugin_dir      = plugin_dir_path( $this->file );
		$this->plugin_url      = plugin_dir_url ( $this->file );
		$this->lang_dir        = trailingslashit( $this->plugin_dir . 'languages' );
		
		$this->admin_url      = $this->plugin_url . 'admin';
		$this->admin_dir      = $this->plugin_dir . 'admin';
		
		// Min age and cookie duration
		$this->minimum_age     = get_option( '_av_minimum_age',     '21' );
		$this->cookie_duration = get_option( '_av_cookie_duration', '720' );
	}
	
	/**
	 * Require the necessary files
	 *
	 * @since 0.1
	 * @access private
	 */
	private function includes() {
		
		// This file defines all of the common functions
		require( $this->plugin_dir . 'functions.php' );
		
		// If in the admin, this file sets up the admin functions
		if ( is_admin() ) :
			
			require( $this->admin_dir . '/admin.php' );
			
			require( $this->admin_dir . '/settings.php' );
			
		endif;
	}
	
	/**
	 * Sets up the actions and filters.
	 *
	 * @since 0.1
	 * @access private
	 */
	private function setup_actions() {
		
		// Load the text domain for i18n
		add_action( 'init', array( $this, 'load_textdomain' ) );
		
		// If checked in the settings, load the default and custom styles
		if ( get_option( '_av_styling', 1 ) == 1 ) :
			
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			
			add_action( 'wp_head', array( $this, 'custom_styles' ) );
			
		endif;
		
		// Maybe display the overlay
		add_action( 'wp_footer', array( $this, 'verify_overlay' ) );
		
		// Maybe hide the content of a restricted content type
		add_action( 'the_content', array( $this, 'restrict_content' ) );
		
		// Verify the visitor's input
		add_action( 'template_redirect', array( $this, 'verify' ) );
		
		// If checked in the settings, add to the registration form
		if ( av_confirmation_required() ) :
			
			add_action( 'register_form', 'av_register_form' );
			
			add_action( 'register_post', 'av_register_check', 10, 3 );
			
		endif;
	}
	
	/**
	 * Load the text domain. Big thanks to bbPress for giving a great example of implementation.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function load_textdomain() {
		
		$locale = get_locale();
		$locale = apply_filters( 'plugin_locale',  $locale, 'age_verify' );
		$mofile = sprintf( 'age_verify-%s.mo', $locale );

		$mofile_local  = $this->lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/age-verify/' . $mofile;

		if ( file_exists( $mofile_local ) )
			return load_textdomain( 'age_verify', $mofile_local );
			
		if ( file_exists( $mofile_global ) )
			return load_textdomain( 'age_verify', $mofile_global );
		
		load_plugin_textdomain( 'age_verify' );
		
		return false;
	}
	
	/**
	 * Enqueue the styles.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function enqueue_styles() {
		
		wp_enqueue_style( 'av-styles', $this->plugin_url . 'assets/styles.css' );
	}
	
	/**
	 * Print the custom colors, as defined in the admin.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function custom_styles() { ?>
		
		<style type="text/css">
			
			#av-overlay-wrap { 
				background: #<?php echo esc_attr( av_get_background_color() ); ?>;
			}
			
			#av-overlay {
				background: #<?php echo esc_attr( av_get_overlay_color() ); ?>;
			}
			
		</style>
		
		<?php do_action( 'av_custom_styles' );
	}
	
	/**
	 * Print the actual overlay if the visitor needs verification.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function verify_overlay() {
		
		if ( ! av_needs_verification() )
			return; ?>
		
		<div id="av-overlay-wrap">
			
			<?php do_action( 'av_before_modal' ); ?>
			
			<div id="av-overlay">
				
				<h1><?php esc_html_e( av_get_the_heading() ); ?></h1>
				
				<?php if ( av_get_the_desc() )
					echo '<p>' . esc_html( av_get_the_desc() ). '</p>'; ?>
				
				<?php do_action( 'av_before_form' ); ?>
				
				<?php av_verify_form(); ?>
					
				<?php do_action( 'av_after_form' ); ?>
				
			</div>
			
			<?php do_action( 'av_after_modal' ); ?>
			
		</div>
	<?php }
	
	/**
	 * Hide the content if it is age restricted
	 *
	 * @since 0.2
	 * @access public
	 */
	 public function restrict_content( $content ) {
		 global $post;
		 
		 if ( ! av_only_content_restricted() )
		 	return $content;
		 	
		 if ( is_singular() )
		 	return $content;
		 	
		 if ( ! av_content_is_restricted() )
		 	return $content;
		 	
		 return sprintf( apply_filters( 'av_restricted_content_message', __( 'You must be %1s years old to view this content.', 'age_verify' ) . ' <a href="%2s">' . __( 'Please verify your age', 'age_verify' ) . '</a>.' ),
		 	esc_html( av_get_minimum_age() ),
		 	esc_url( get_permalink( $post->ID ) )
		 );
	 }
	
	/**
	 * Verify the visitor if the form was submitted.
	 * There are various filters and actions to change this method's behavior.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function verify() {
		
		if ( empty( $_POST ) || ! wp_verify_nonce( $_POST['av-nonce'], 'verify-age' ) )
			return;
		
		$redirect_url = remove_query_arg( array( 'age-verified', 'verify-error' ), wp_get_referer() );
		
		$is_verified  = false;
		
		$error = 1; // Catch-all in case something goes wrong
		
		$input_type   = av_get_input_type();
		
		switch ( $input_type ) {
			
			
			case 'checkbox' :
				
				if ( isset( $_POST['av_verify_confirm'] ) && (int) $_POST['av_verify_confirm'] == 1 )
					$is_verified = true;
				else
					$error = 2; // Didn't check the box
				
				break;
			
			default :
				
				if ( checkdate( (int) $_POST['av_verify_m'], (int) $_POST['av_verify_d'], (int) $_POST['av_verify_y'] ) ) :
					
					$age = av_get_visitor_age( $_POST['av_verify_y'], $_POST['av_verify_m'], $_POST['av_verify_d'] );
					
				    if ( $age >= av_get_minimum_age() )
						$is_verified = true;
					else
						$error = 3; // Not old enough
						
				else :
					
					$error = 4; // Invalid date
					
				endif;
				
				break;
		}
		
		$is_verified = apply_filters( 'av_passed_verify', $is_verified );
		
		if ( $is_verified == true ) :
			
			do_action( 'av_was_verified' );
			
			if ( isset( $_POST['av_verify_remember'] ) )
				$cookie_duration = time() +  ( av_get_cookie_duration() * 60 );
			else
				$cookie_duration = 0;
			
			setcookie( 'age-verified', 1, $cookie_duration, COOKIEPATH, COOKIE_DOMAIN, false );
			
			wp_redirect( $redirect_url . '?age-verified=' . wp_create_nonce( 'age-verified' ) );
			exit;
			
		else :
			
			do_action( 'av_was_not_verified' );
			
			wp_redirect( add_query_arg( 'verify-error', $error, $redirect_url ) );
			exit;
			
		endif;
	}
}

$age_verify = new Age_Verify();