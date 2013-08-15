<?php

/**
 * Handles the admin setup for the plugin.
 *
 * @package AgeVerify
 * @subpackage Admin
 */

// Don't access this directly, please
if ( ! defined( 'ABSPATH' ) ) exit;

// Call the admin setup on init.
add_action( 'init', 'av_admin_setup' );

/**
 * Sets up the admin.
 *
 * @since 0.1
 */
function av_admin_setup() {
	
	add_action( 'admin_init',                  'av_admin_register_settings' );
	
	add_action( 'admin_menu',                  'av_admin_menu' );
	
	add_filter( 'plugin_action_links',         'av_admin_add_settings_link', 10, 2 );

	add_action( 'admin_enqueue_scripts',       'av_admin_enqueue_scripts' );
	
	// Only mess with post-specific stuff if enabled
	if ( get_option( '_av_require_for' ) == 'content' ) :
		
		add_action( 'post_submitbox_misc_actions', 'av_add_submitbox_checkbox' );
		
		add_action( 'save_post',                   'av_save_post' );
		
	endif;
}
	
/**
 * Add the settings sections and individual settings.
 *
 * @since 0.1
 */
function av_admin_register_settings() {
	
	/* General Section */
	add_settings_section( 'av_settings_general', null, 'av_settings_callback_section_general', 'age-verify' );
 	
 	// What to protect (entire site or specific content)
	add_settings_field( '_av_require_for', __( 'Require verification for', 'age_verify' ), 'av_settings_callback_require_for_field', 'age-verify', 'av_settings_general' );
 	register_setting  ( 'age-verify', '_av_require_for', 'esc_attr' );
 	
 	// Who to verify (logged in or all)
	add_settings_field( '_av_always_verify', __( 'Verify the age of', 'age_verify' ), 'av_settings_callback_always_verify_field', 'age-verify', 'av_settings_general' );
 	register_setting  ( 'age-verify', '_av_always_verify', 'esc_attr' );
 	
 	// Minimum Age
	add_settings_field( '_av_minimum_age', '<label for="_av_minimum_age">' . __( 'Visitors must be', 'age_verify' ) . '</label>', 'av_settings_callback_minimum_age_field', 'age-verify', 'av_settings_general' );
 	register_setting  ( 'age-verify', '_av_minimum_age', 'intval' );
 	
 	// Memory Length
 	add_settings_field( '_av_cookie_duration', '<label for="_av_cookie_duration">' . __( 'Remember visitors for', 'age_verify' ) . '</label>', 'av_settings_callback_cookie_duration_field', 'age-verify', 'av_settings_general' );
 	register_setting  ( 'age-verify', '_av_cookie_duration', 'intval' );
 	
 	add_settings_field( '_av_membership', __( 'Membership', 'age_verify' ), 'av_settings_callback_membership_field', 'age-verify', 'av_settings_general' );
 	register_setting  ( 'age-verify', '_av_membership', 'intval' );
 	
 	/* Display Section */
 	add_settings_section( 'av_settings_display', __( 'Display Options', 'age_verify' ), 'av_settings_callback_section_display', 'age-verify' );
 	
 	// Heading
 	add_settings_field( '_av_heading', '<label for="_av_heading">' . __( 'Overlay Heading', 'age_verify' ) . '</label>', 'av_settings_callback_heading_field', 'age-verify', 'av_settings_display' );
 	register_setting  ( 'age-verify', '_av_heading', 'esc_attr' );
 	
 	// Description
 	add_settings_field( '_av_description', '<label for="_av_description">' . __( 'Overlay Description', 'age_verify' ) . '</label>', 'av_settings_callback_description_field', 'age-verify', 'av_settings_display' );
 	register_setting  ( 'age-verify', '_av_description', 'esc_attr' );
 	
 	// Input Type
 	add_settings_field( '_av_input_type', '<label for="_av_input_type">' . __( 'Verify ages using', 'age_verify' ) . '</label>', 'av_settings_callback_input_type_field', 'age-verify', 'av_settings_display' );
 	register_setting  ( 'age-verify', '_av_input_type', 'esc_attr' );
 	
 	// Enable CSS
 	add_settings_field( '_av_styling', __( 'Styling', 'age_verify' ), 'av_settings_callback_styling_field', 'age-verify', 'av_settings_display' );
 	register_setting  ( 'age-verify', '_av_styling', 'intval' );
 	
 	// Overlay Color
 	add_settings_field( '_av_overlay_color', __( 'Overlay Color', 'age_verify' ), 'av_settings_callback_overlay_color_field', 'age-verify', 'av_settings_display' );
 	register_setting  ( 'age-verify', '_av_overlay_color', 'av_validate_color' );
 	
 	// Background Color
 	add_settings_field( '_av_bgcolor', __( 'Background Color', 'age_verify' ), 'av_settings_callback_bgcolor_field', 'age-verify', 'av_settings_display' );
 	register_setting  ( 'age-verify', '_av_bgcolor', 'av_validate_color' );
	
	do_action( 'av_register_settings' );
}

/**
 * Validates the color inputs from the settings.
 *
 * @since 0.1
 * @access public
 * @return string
 */
function av_validate_color( $color ) {
	
	$color = preg_replace( '/[^0-9a-fA-F]/', '', $color );
	
	if ( strlen( $color ) == 6 || strlen( $color ) == 3 )
		$color = $color;
	else
		$color = '';
	
	return $color;
}

/**
 * Add to the settings menu.
 *
 * @since 0.1
 * @access public
 */
function av_admin_menu() {

	add_options_page ( __( 'Age Verify',  'age_verify' ), __( 'Age Verify',  'age_verify' ), 'manage_options', 'age-verify', 'av_settings_page' );
}

/**
 * Add a direct link to the Age Verify settings page from the plugins page.
 *
 * @since 0.1
 * @access public
 * @return string
 */
function av_admin_add_settings_link( $links, $file ) {
	global $age_verify;
	
	if ( $age_verify->basename == $file ) :
		
		$settings_link = '<a href="' . add_query_arg( 'page', 'age-verify', admin_url( 'options-general.php' ) ) . '">' . __( 'Settings', 'age_verify' ) . '</a>';
		array_unshift( $links, $settings_link );
		
	endif;
	
	return $links;
}

/**
 * Enqueue the scripts.
 *
 * @since 0.1
 */
function av_admin_enqueue_scripts( $page ) {
	global $age_verify;
	
	if ( 'settings_page_age-verify' != $page )
		return;
	
	wp_enqueue_style('wp-color-picker');
	
	wp_enqueue_script( 'av-admin-scripts', $age_verify->admin_url . '/assets/scripts.js', array( 'jquery', 'wp-color-picker' ) );
}

/**
 * Adds the meta box for posts and pages.
 *
 * @since 0.2
 */
function av_add_submitbox_checkbox() {
	global $post; ?>
	
	<div class="misc-pub-section verify-age">
		
		<?php wp_nonce_field( 'av_save_post', 'av_nonce' ); ?>
		
		<input type="checkbox" name="_av_needs_verify" id="_av_needs_verify" value="1" <?php checked( 1, get_post_meta( $post->ID, '_av_needs_verify', true ) ); ?> />
		<label for="_av_needs_verify" class="selectit"><?php esc_html_e( 'Require age verification for this content', 'age_verify' ); ?></label>
		
	</div><!-- .misc-pub-section -->
<?php }

/**
 * Saves the post|page meta
 *
 * @since 0.2
 */
function av_save_post( $post_id ) {
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	$nonce = ( isset( $_POST['av_nonce'] ) ) ? $_POST['av_nonce'] : '';
	
	if ( ! wp_verify_nonce( $nonce, 'av_save_post' ) )
		return;
		
	$needs_verify = ( isset( $_POST['_av_needs_verify'] ) ) ? (int) $_POST['_av_needs_verify'] : 0;
	
	update_post_meta( $post_id, '_av_needs_verify', $needs_verify );
	
	return $post_id;
}