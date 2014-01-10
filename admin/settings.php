<?php

// Don't access this directly, please
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define the settings page.
 *
 * @since 0.1
 */
function av_settings_page() { ?>

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php esc_html_e( 'Age Verify Settings', 'age_verify' ) ?></h2>

		<form action="options.php" method="post">

			<?php settings_fields( 'age-verify' ); ?>

			<?php do_settings_sections( 'age-verify' ); ?>

			<?php submit_button(); ?>
			
		</form>
	</div>

<?php }


/**********************************************************/
/******************** General Settings ********************/
/**********************************************************/

/**
 * Prints the general settings section heading.
 *
 * @since 0.1
 */
function av_settings_callback_section_general() {
	
	// Something should go here
}

/**
 * Prints the "require for" settings field.
 *
 * @since 0.2
 */
function av_settings_callback_require_for_field() { ?>
	
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Require verification for', 'age_verify' ); ?></span>
		</legend>
		<label>
			<input type="radio" name="_av_require_for" value="site" <?php checked( 'site', get_option( '_av_require_for', 'site' ) ); ?>/>
			 <?php esc_html_e( 'Entire site', 'age_verify' ); ?><br />
		</label>
		<br />
		<label>
			<input type="radio" name="_av_require_for" value="content" <?php checked( 'content', get_option( '_av_require_for', 'site' ) ); ?>/>
			 <?php esc_html_e( 'Specific content', 'age_verify' ); ?>
		</label>
	</fieldset>
	
<?php }

/**
 * Prints the "who to verify" settings field.
 *
 * @since 0.1
 */
function av_settings_callback_always_verify_field() { ?>
	
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Verify the age of', 'age_verify' ); ?></span>
		</legend>
		<label>
			<input type="radio" name="_av_always_verify" value="guests" <?php checked( 'guests', get_option( '_av_always_verify', 'guests' ) ); ?>/>
			 <?php esc_html_e( 'Guests only', 'age_verify' ); ?> <span class="description"><?php esc_html_e( 'Logged-in users will not need to verify their age.', 'age_verify' ); ?></span><br />
		</label>
		<br />
		<label>
			<input type="radio" name="_av_always_verify" value="all" <?php checked( 'all', get_option( '_av_always_verify', 'guests' ) ); ?>/>
			 <?php esc_html_e( 'All visitors', 'age_verify' ); ?>
		</label>
	</fieldset>
	
<?php }

/**
 * Prints the minimum age settings field.
 *
 * @since 0.1
 */
function av_settings_callback_minimum_age_field() { ?>
	
	<input name="_av_minimum_age" type="number" id="_av_minimum_age" step="1" min="10" class="small-text" value="<?php echo esc_attr( get_option( '_av_minimum_age', '21' ) ); ?>" /> <?php esc_html_e( 'years old or older to view this site', 'age_verify' ); ?>
	
<?php }

/**
 * Prints the cookie duration settings field.
 *
 * @since 0.1
 */
function av_settings_callback_cookie_duration_field() { ?>
	
	<input name="_av_cookie_duration" type="number" id="_av_cookie_duration" step="15" min="15" class="small-text" value="<?php echo esc_attr( get_option( '_av_cookie_duration', '720' ) ); ?>" /> <?php esc_html_e( 'minutes', 'age_verify' ); ?>
	
<?php }

/**
 * Prints the membership settings field.
 *
 * @since 0.1
 */
function av_settings_callback_membership_field() { ?>
	
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Membership', 'age_verify' ); ?></span>
		</legend>
		<label for="_av_membership">
			<input name="_av_membership" type="checkbox" id="_av_membership" value="1" <?php checked( 1, get_option( '_av_membership', 1 ) ); ?>/>
			 <?php esc_html_e( 'Require users to confirm their age before registering to this site', 'age_verify' ); ?>
		</label>
	</fieldset>
	
<?php }


/**********************************************************/
/******************** Display Settings ********************/
/**********************************************************/

/**
 * Prints the display settings section heading.
 *
 * @since 0.1
 */
function av_settings_callback_section_display() {
	
	echo '<p>' . esc_html__( 'These settings change the look of your overlay. You can use <code>%s</code> to display the minimum age number from the setting above.', 'age_verify' ) . '</p>';
}

/**
 * Prints the modal heading settings field.
 *
 * @since 0.1
 */
function av_settings_callback_heading_field() { ?>
	
	<input name="_av_heading" type="text" id="_av_heading" value="<?php echo esc_attr( get_option( '_av_heading', __( 'You must be %s years old to visit this site.', 'age_verify' ) ) ); ?>" class="regular-text" />
	
<?php }

/**
 * Prints the modal description settings field.
 *
 * @since 0.1
 */
function av_settings_callback_description_field() { ?>
	
	<input name="_av_description" type="text" id="_av_description" value="<?php echo esc_attr( get_option( '_av_description', __( 'Please verify your age', 'age_verify' ) ) ); ?>" class="regular-text" />
	
<?php }

/**
 * Prints the input type settings field.
 *
 * @since 0.1
 */
function av_settings_callback_input_type_field() { ?>
	
	<select name="_av_input_type" id="_av_input_type">
		<option value="dropdowns" <?php selected( 'dropdowns', get_option( '_av_input_type', 'dropdowns' ) ); ?>><?php esc_html_e( 'Date dropdowns', 'age_verify' ); ?></option>
		<option value="inputs" <?php selected( 'inputs', get_option( '_av_input_type', 'dropdowns' ) ); ?>><?php esc_html_e( 'Inputs', 'age_verify' ); ?></option>
		<option value="checkbox" <?php selected( 'checkbox', get_option( '_av_input_type', 'dropdowns' ) ); ?>><?php esc_html_e( 'Confirm checkbox', 'age_verify' ); ?></option>
	</select>
	
<?php }

/**
 * Prints the styling settings field.
 *
 * @since 0.1
 */
function av_settings_callback_styling_field() { ?>
	
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Styling', 'age_verify' ); ?></span>
		</legend>
		<label for="_av_styling">
			<input name="_av_styling" type="checkbox" id="_av_styling" value="1" <?php checked( 1, get_option( '_av_styling', 1 ) ); ?>/>
			 <?php esc_html_e( 'Use built-in CSS on the front-end (recommended)', 'age_verify' ); ?>
		</label>
	</fieldset>
	
<?php }

/**
 * Prints the overlay color settings field.
 *
 * @since 0.1
 */
function av_settings_callback_overlay_color_field() { ?>
	
	<fieldset>
		
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Overlay Color', 'age_verify' ); ?></span>
		</legend>
		
		<?php $default_color = ' data-default-color="#fff"'; ?>
			
		<input type="text" name="_av_overlay_color" id="_av_overlay_color" value="#<?php echo esc_attr( av_get_overlay_color() ); ?>"<?php echo $default_color ?> />
		
	</fieldset>
	
<?php }

/**
 * Prints the background color settings field.
 *
 * @since 0.1
 */
function av_settings_callback_bgcolor_field() { ?>
	
	<fieldset>
		
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Background Color' ); ?></span>
		</legend>
		
		<?php $default_color = '';
		
		if ( current_theme_supports( 'custom-background', 'default-color' ) )
			$default_color = ' data-default-color="#' . esc_attr( get_theme_support( 'custom-background', 'default-color' ) ) . '"'; ?>
			
		<input type="text" name="_av_bgcolor" id="_av_bgcolor" value="#<?php echo esc_attr( av_get_background_color() ); ?>"<?php echo $default_color ?> />
		
	</fieldset>
	
<?php }
