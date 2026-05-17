<?php
/**
 * Plugin Name:       RASA - Social Contact Panel
 * Description:       A floating social contact button with a configurable popup panel supporting 20 platforms. Lightweight, fast, and fully customizable from the WordPress admin.
 * Version:           1.0.2
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            WPLabTech
 * Author URI:        https://www.wplabtech.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rasa-social-contact-panel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RASASCP_VERSION', '1.0.2' );
define( 'RASASCP_FILE', __FILE__ );
define( 'RASASCP_DIR', plugin_dir_path( __FILE__ ) );
define( 'RASASCP_URL', plugin_dir_url( __FILE__ ) );

require_once RASASCP_DIR . 'includes/helpers.php';
require_once RASASCP_DIR . 'includes/admin-settings.php';
require_once RASASCP_DIR . 'includes/frontend-render.php';

register_activation_hook( __FILE__, [ 'RASASCP_Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'RASASCP_Plugin', 'deactivate' ] );

add_action( 'plugins_loaded', [ 'RASASCP_Plugin', 'get_instance' ] );

final class RASASCP_Plugin {

	private static $instance = null;

	const OPTION_KEY = 'rasascp_settings';
	const LEGACY_OPTION_KEY = 'wltsscp_settings';

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
		add_filter(
			'plugin_action_links_' . plugin_basename( RASASCP_FILE ),
			[ $this, 'add_action_links' ]
		);

		$options = self::get_options();

		$settings = new RASASCP_Settings( self::OPTION_KEY, RASASCP_VERSION );
		$settings->register_hooks();

		$frontend = new RASASCP_Frontend( $options );
		$frontend->register_hooks();
	}

	public static function get_options() {
		$saved = get_option( self::OPTION_KEY, null );

		if ( ! is_array( $saved ) ) {
			$legacy = get_option( self::LEGACY_OPTION_KEY, [] );

			if ( is_array( $legacy ) ) {
				$saved = $legacy;
				update_option( self::OPTION_KEY, $legacy, false );
			}
		}

		if ( ! is_array( $saved ) ) {
			$saved = [];
		}

		return wp_parse_args( $saved, RASASCP_Settings::get_default_options() );
	}

	public function add_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=rasa-social-contact-panel' ) ),
			esc_html__( 'Settings', 'rasa-social-contact-panel' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	public static function activate() {
		$legacy = get_option( self::LEGACY_OPTION_KEY, [] );

		if ( is_array( $legacy ) && ! empty( $legacy ) && false === get_option( self::OPTION_KEY, false ) ) {
			add_option( self::OPTION_KEY, $legacy, '', false );
			return;
		}

		add_option( self::OPTION_KEY, RASASCP_Settings::get_default_options(), '', false );
	}

	public static function deactivate() {}
}
