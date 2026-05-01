<?php
/**
 * Plugin Name:       Smart Social Contact Panel
 * Description:       A floating social contact button with a configurable popup panel supporting 20 platforms. Lightweight, fast, and fully customizable from the WordPress admin.
 * Version:           1.0.0
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            WPLabTech
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-social-contact-panel
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin constants
define( 'SSCP_VERSION', '1.0.0' );
define( 'SSCP_FILE',    __FILE__ );
define( 'SSCP_DIR',     plugin_dir_path( __FILE__ ) );
define( 'SSCP_URL',     plugin_dir_url( __FILE__ ) );

// Load dependencies
require_once SSCP_DIR . 'includes/helpers.php';
require_once SSCP_DIR . 'includes/admin-settings.php';
require_once SSCP_DIR . 'includes/frontend-render.php';

// Lifecycle hooks (must be at file scope, before class instantiation)
register_activation_hook( __FILE__, [ 'SSCP_Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'SSCP_Plugin', 'deactivate' ] );

// Bootstrap after all plugins are loaded so other plugins can hook in first
add_action( 'plugins_loaded', [ 'SSCP_Plugin', 'get_instance' ] );

/**
 * Main plugin class — singleton bootstrap.
 *
 * Responsible for: wiring subsystems together, storing the option key,
 * providing the canonical get_options() accessor, and plugin lifecycle.
 */
final class SSCP_Plugin {

	/** @var SSCP_Plugin|null */
	private static $instance = null;

	/** WordPress option key for all plugin settings. */
	const OPTION_KEY = 'sscp_settings';

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->init();
	}

	private function init() {
		// "Settings" quick-link on the Plugins list page
		add_filter(
			'plugin_action_links_' . plugin_basename( SSCP_FILE ),
			[ $this, 'add_action_links' ]
		);

		// Retrieve settings once; subsystems share the same array
		$options = self::get_options();

		// Admin subsystem
		$settings = new SSCP_Settings( self::OPTION_KEY, SSCP_VERSION );
		$settings->register_hooks();

		// Frontend subsystem
		$frontend = new SSCP_Frontend( $options );
		$frontend->register_hooks();
	}

	/**
	 * Return the current settings merged with defaults.
	 * One get_option() call per request; WordPress object-cache handles repetition.
	 */
	public static function get_options() {
		$saved = get_option( self::OPTION_KEY, [] );
		if ( ! is_array( $saved ) ) {
			$saved = [];
		}
		return wp_parse_args( $saved, SSCP_Settings::get_default_options() );
	}

	public function add_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=smart-social-contact' ) ),
			esc_html__( 'Settings', 'smart-social-contact-panel' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * On first activation: write default options.
	 * add_option() is a no-op if the option already exists,
	 * so existing settings survive re-activation.
	 */
	public static function activate() {
		add_option( self::OPTION_KEY, SSCP_Settings::get_default_options(), '', false );
	}

	/**
	 * Deactivation intentionally does nothing — data is preserved so that
	 * reactivating the plugin restores all previously saved settings.
	 */
	public static function deactivate() {}
}
