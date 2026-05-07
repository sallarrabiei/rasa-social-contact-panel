<?php
/**
 * Plugin Name:       Smart Social Contact Panel
 * Description:       A floating social contact button with a configurable popup panel supporting 20 platforms. Lightweight, fast, and fully customizable from the WordPress admin.
 * Version:           1.0.2
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            WPLabTech
 * Author URI:        https://www.wplabtech.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-social-contact-panel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WLTSSCP_VERSION', '1.0.2' );
define( 'WLTSSCP_FILE', __FILE__ );
define( 'WLTSSCP_DIR', plugin_dir_path( __FILE__ ) );
define( 'WLTSSCP_URL', plugin_dir_url( __FILE__ ) );

require_once WLTSSCP_DIR . 'includes/helpers.php';
require_once WLTSSCP_DIR . 'includes/admin-settings.php';
require_once WLTSSCP_DIR . 'includes/frontend-render.php';

register_activation_hook( __FILE__, [ 'WLTSSCP_Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'WLTSSCP_Plugin', 'deactivate' ] );

add_action( 'plugins_loaded', [ 'WLTSSCP_Plugin', 'get_instance' ] );

final class WLTSSCP_Plugin {

	private static $instance = null;

	const OPTION_KEY = 'wltsscp_settings';

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
			'plugin_action_links_' . plugin_basename( WLTSSCP_FILE ),
			[ $this, 'add_action_links' ]
		);

		$options = self::get_options();

		$settings = new WLTSSCP_Settings( self::OPTION_KEY, WLTSSCP_VERSION );
		$settings->register_hooks();

		$frontend = new WLTSSCP_Frontend( $options );
		$frontend->register_hooks();
	}

	public static function get_options() {
		$saved = get_option( self::OPTION_KEY, [] );

		if ( ! is_array( $saved ) ) {
			$saved = [];
		}

		return wp_parse_args( $saved, WLTSSCP_Settings::get_default_options() );
	}

	public function add_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=smart-social-contact-panel' ) ),
			esc_html__( 'Settings', 'smart-social-contact-panel' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	public static function activate() {
		add_option( self::OPTION_KEY, WLTSSCP_Settings::get_default_options(), '', false );
	}

	public static function deactivate() {}
}
