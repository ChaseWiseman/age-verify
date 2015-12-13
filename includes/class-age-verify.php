<?php
/**
 * Define the main plugin class
 *
 * @since 0.2.6
 *
 * @package Age_Verify
 */

// Don't allow this file to be accessed directly.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * The main class.
 *
 * @since 0.1.0
 */
final class Age_Verify {

	/**
	 * The plugin version.
	 *
	 * @since 0.2.6
	 */
	const VERSION = '0.3.0';

	/**
	 * The only instance of this class.
	 *
	 * @since 0.2.6
	 * @access protected
	 */
	protected static $instance = null;

	/**
	 * Get the only instance of this class.
	 *
	 * @since 0.2.6
	 *
	 * @return object $instance The only instance of this class.
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Prevent cloning of this class.
	 *
	 * @since 0.2.6
	 *
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'age-verify' ), self::VERSION );
	}

	/**
	 * Prevent unserializing of this class.
	 *
	 * @since 0.2.6
	 *
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'age-verify' ), self::VERSION );
	}

	/**
	 * Construct the class!
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function __construct() {

		/**
		 * Require the necessary files.
		 */
		$this->require_files();

		/**
		 * Add the necessary action hooks.
		 */
		$this->add_actions();
	}

	/**
	 * Require the necessary files.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function require_files() {

		/**
		 * The helper functions.
		 */
		require( plugin_dir_path( __FILE__ ) . 'functions.php' );
	}

	/**
	 * Add the necessary action hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function add_actions() {

		// Load the text domain for i18n.
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// If checked in the settings, load the default and custom styles.
		if ( get_option( '_av_styling', 1 ) == 1 ) {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			add_action( 'wp_head', array( $this, 'custom_styles' ) );

		}

		// Maybe display the overlay.
		add_action( 'wp_footer', array( $this, 'verify_overlay' ) );

		// Maybe hide the content of a restricted content type.
		add_action( 'the_content', array( $this, 'restrict_content' ) );

		// Verify the visitor's input.
		add_action( 'template_redirect', array( $this, 'verify' ) );

		// If checked in the settings, add to the registration form.
		if ( av_confirmation_required() ) {

			add_action( 'register_form', 'av_register_form' );

			add_action( 'register_post', 'av_register_check', 10, 3 );

		}
	}

	/**
	 * Load the text domain.
	 *
	 * Based on the bbPress implementation.
	 *
	 * @since 0.1.0
	 *
	 * @return The textdomain or false on failure.
	 */
	public function load_textdomain() {

		$locale = get_locale();
		$locale = apply_filters( 'plugin_locale',  $locale, 'age-verify' );
		$mofile = sprintf( 'age-verify-%s.mo', $locale );

		$mofile_local  = plugin_dir_path( dirname( __FILE__ ) ) . 'languages/' . $mofile;
		$mofile_global = WP_LANG_DIR . '/age-verify/' . $mofile;

		if ( file_exists( $mofile_local ) )
			return load_textdomain( 'age-verify', $mofile_local );

		if ( file_exists( $mofile_global ) )
			return load_textdomain( 'age-verify', $mofile_global );

		load_plugin_textdomain( 'age-verify' );

		return false;
	}

	/**
	 * Enqueue the styles.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'av-styles', plugin_dir_url( __FILE__ ) . 'assets/styles.css' );
	}

	/**
	 * Print the custom colors, as defined in the admin.
	 *
	 * @since 0.1.0
	 *
	 * @return void
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

		<?php
		/**
		* Trigger action after setting the custom color styles.
		*/
		do_action( 'av_custom_styles' );
	}

	/**
	 * Print the actual overlay if the visitor needs verification.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function verify_overlay() {

		if ( ! av_needs_verification() ) {
			return;
		}

		// Disable page caching by W3 Total Cache.
		define( 'DONOTCACHEPAGE', true ); ?>

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
	 * Hide the content if it is age restricted.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $content The object content.
	 * @return string $content The object content or an age-restricted message if needed.
	 */
	 public function restrict_content( $content ) {

		if ( ! av_only_content_restricted() ) {
			return $content;
		}

		if ( is_singular() ) {
			return $content;
		}

		if ( ! av_content_is_restricted() ) {
			return $content;
		}

		return sprintf( apply_filters( 'av_restricted_content_message', __( 'You must be %1s years old to view this content.', 'age-verify' ) . ' <a href="%2s">' . __( 'Please verify your age', 'age-verify' ) . '</a>.' ),
			esc_html( av_get_minimum_age() ),
			esc_url( get_permalink( get_the_ID() ) )
		);
	 }

	/**
	 * Verify the visitor if the form was submitted.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function verify() {

		if ( ! isset( $_POST['av-nonce'] ) || ! wp_verify_nonce( $_POST['av-nonce'], 'verify-age' ) )
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

			wp_redirect( esc_url_raw( $redirect_url ) . '?age-verified=' . wp_create_nonce( 'age-verified' ) );
			exit;

		else :

			do_action( 'av_was_not_verified' );

			wp_redirect( esc_url_raw( add_query_arg( 'verify-error', $error, $redirect_url ) ) );
			exit;

		endif;
	}
}
