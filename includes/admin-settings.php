<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RASASCP_Settings {

	private $option_key;

	private $version;

	private static $tabs = [ 'general', 'platforms', 'design', 'advanced' ];

	private static $platform_meta = [
		'whatsapp'  => [ 'label' => 'WhatsApp', 'type' => 'phone' ],
		'telegram'  => [ 'label' => 'Telegram', 'type' => 'text' ],
		'messenger' => [ 'label' => 'Messenger', 'type' => 'text' ],
		'instagram' => [ 'label' => 'Instagram', 'type' => 'text' ],
		'facebook'  => [ 'label' => 'Facebook', 'type' => 'url' ],
		'twitter'   => [ 'label' => 'X / Twitter', 'type' => 'text' ],
		'linkedin'  => [ 'label' => 'LinkedIn', 'type' => 'url' ],
		'tiktok'    => [ 'label' => 'TikTok', 'type' => 'text' ],
		'youtube'   => [ 'label' => 'YouTube', 'type' => 'url' ],
		'snapchat'  => [ 'label' => 'Snapchat', 'type' => 'text' ],
		'pinterest' => [ 'label' => 'Pinterest', 'type' => 'text' ],
		'discord'   => [ 'label' => 'Discord', 'type' => 'text' ],
		'skype'     => [ 'label' => 'Skype', 'type' => 'text' ],
		'viber'     => [ 'label' => 'Viber', 'type' => 'phone' ],
		'line'      => [ 'label' => 'Line', 'type' => 'text' ],
		'wechat'    => [ 'label' => 'WeChat', 'type' => 'url' ],
		'email'     => [ 'label' => 'Email', 'type' => 'email' ],
		'phone'     => [ 'label' => 'Phone Call', 'type' => 'phone' ],
		'sms'       => [ 'label' => 'SMS', 'type' => 'phone' ],
		'website'   => [ 'label' => 'Website', 'type' => 'url' ],
	];

	public function __construct( $option_key, $version ) {
		$this->option_key = $option_key;
		$this->version    = $version;
	}

	public function register_hooks() {
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_action( 'admin_post_rasascp_save', [ $this, 'handle_save' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
	}

	public static function get_default_options() {
		return [
			'enabled'             => false,
			'button_position'     => 'bottom-right',
			'button_text'         => 'Contact Us',
			'button_side_text'    => '',
			'button_color'        => '#25d366',
			'button_text_color'   => '#ffffff',
			'popup_title'         => 'Contact Us',
			'popup_description'   => '',
			'popup_width'         => 420,
			'popup_border_radius' => 12,
			'popup_bg_color'      => '#ffffff',
			'popup_text_color'    => '#333333',
			'popup_box_shadow'    => true,
			'popup_columns'       => 2,
			'card_bg_color'       => '#f8f9fa',
			'card_hover_color'    => '#e9ecef',
			'card_icon_color'     => '',
			'card_border_radius'  => 8,
			'animation_style'     => 'slide-up',
			'mobile_columns'      => 1,
			'mobile_full_width'   => true,
			'show_branding'       => false,
			'platforms'           => [
				'whatsapp'  => [ 'enabled' => false, 'label' => 'WhatsApp', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 1, 'new_tab' => true ],
				'telegram'  => [ 'enabled' => false, 'label' => 'Telegram', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 2, 'new_tab' => true ],
				'messenger' => [ 'enabled' => false, 'label' => 'Messenger', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 3, 'new_tab' => true ],
				'instagram' => [ 'enabled' => false, 'label' => 'Instagram', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 4, 'new_tab' => true ],
				'facebook'  => [ 'enabled' => false, 'label' => 'Facebook', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 5, 'new_tab' => true ],
				'twitter'   => [ 'enabled' => false, 'label' => 'X / Twitter', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 6, 'new_tab' => true ],
				'linkedin'  => [ 'enabled' => false, 'label' => 'LinkedIn', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 7, 'new_tab' => true ],
				'tiktok'    => [ 'enabled' => false, 'label' => 'TikTok', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 8, 'new_tab' => true ],
				'youtube'   => [ 'enabled' => false, 'label' => 'YouTube', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 9, 'new_tab' => true ],
				'snapchat'  => [ 'enabled' => false, 'label' => 'Snapchat', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 10, 'new_tab' => true ],
				'pinterest' => [ 'enabled' => false, 'label' => 'Pinterest', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 11, 'new_tab' => true ],
				'discord'   => [ 'enabled' => false, 'label' => 'Discord', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 12, 'new_tab' => true ],
				'skype'     => [ 'enabled' => false, 'label' => 'Skype', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 13, 'new_tab' => true ],
				'viber'     => [ 'enabled' => false, 'label' => 'Viber', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 14, 'new_tab' => true ],
				'line'      => [ 'enabled' => false, 'label' => 'Line', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 15, 'new_tab' => true ],
				'wechat'    => [ 'enabled' => false, 'label' => 'WeChat', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 16, 'new_tab' => false ],
				'email'     => [ 'enabled' => false, 'label' => 'Email', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 17, 'new_tab' => false ],
				'phone'     => [ 'enabled' => false, 'label' => 'Phone Call', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 18, 'new_tab' => false ],
				'sms'       => [ 'enabled' => false, 'label' => 'SMS', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 19, 'new_tab' => false ],
				'website'   => [ 'enabled' => false, 'label' => 'Website', 'value' => '', 'message' => '', 'description' => '', 'sort_order' => 20, 'new_tab' => true ],
			],
		];
	}

	public function add_settings_page() {
		add_menu_page(
			esc_html__( 'RASA - Social Contact Panel', 'rasa-social-contact-panel' ),
			esc_html__( 'RASA Contact', 'rasa-social-contact-panel' ),
			'manage_options',
			'rasa-social-contact-panel',
			[ $this, 'render_page' ],
			'dashicons-share',
			80
		);
	}

	public function enqueue_admin_assets( $hook ) {
		if ( 'toplevel_page_rasa-social-contact-panel' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'rasascp-admin', RASASCP_URL . 'assets/css/admin.css', [], RASASCP_VERSION );
		wp_enqueue_script( 'rasascp-admin', RASASCP_URL . 'assets/js/admin.js', [], RASASCP_VERSION, true );
		wp_localize_script(
			'rasascp-admin',
			'rasascpAdmin',
			[
				'confirmReset' => __( 'Reset all settings to defaults? This cannot be undone.', 'rasa-social-contact-panel' ),
				'importInvalid' => __( 'Invalid settings file. Please export from this plugin.', 'rasa-social-contact-panel' ),
				'importLoaded' => __( 'Valid settings file loaded. Click "Save Imported Settings" to apply.', 'rasa-social-contact-panel' ),
			]
		);
	}

	public function handle_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'rasa-social-contact-panel' ) );
		}

		check_admin_referer( 'rasascp_save_settings', 'rasascp_nonce' );

		$reset = filter_input( INPUT_POST, 'rasascp_reset', FILTER_SANITIZE_NUMBER_INT );
		if ( '1' === (string) $reset ) {
			update_option( $this->option_key, self::get_default_options(), false );
			$this->redirect_after_save( 'general' );
		}

		$import_json = filter_input( INPUT_POST, 'rasascp_import_json', FILTER_UNSAFE_RAW );
		if ( is_string( $import_json ) && '' !== trim( $import_json ) ) {
			$parsed = json_decode( wp_unslash( $import_json ), true );

			if ( is_array( $parsed ) ) {
				update_option( $this->option_key, $this->sanitize_settings( $parsed ), false );
			}

			$this->redirect_after_save( 'advanced' );
		}

		$current_tab = filter_input( INPUT_POST, 'rasascp_current_tab', FILTER_UNSAFE_RAW );
		$current_tab = is_string( $current_tab ) ? sanitize_key( wp_unslash( $current_tab ) ) : 'general';
		if ( ! in_array( $current_tab, self::$tabs, true ) ) {
			$current_tab = 'general';
		}

		$input = filter_input( INPUT_POST, 'rasascp', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$input = is_array( $input ) ? wp_unslash( $input ) : [];

		$clean = $this->sanitize_settings( $input );
		$clean = $this->merge_tab_settings( RASASCP_Plugin::get_options(), $clean, $current_tab );

		update_option( $this->option_key, $clean, false );
		$this->redirect_after_save( $current_tab );
	}

	public function sanitize_settings( array $input ) {
		$defaults = self::get_default_options();
		$clean    = [];

		$clean['enabled']             = ! empty( $input['enabled'] );
		$clean['button_position']     = RASASCP_Helpers::sanitize_select( $input['button_position'] ?? '', [ 'bottom-left', 'bottom-right' ], $defaults['button_position'] );
		$clean['button_text']         = RASASCP_Helpers::sanitize_text( $input['button_text'] ?? '' );
		$clean['button_side_text']    = RASASCP_Helpers::sanitize_text( $input['button_side_text'] ?? '' );
		$clean['button_color']        = RASASCP_Helpers::sanitize_hex_color( $input['button_color'] ?? '' ) ?: $defaults['button_color'];
		$clean['button_text_color']   = RASASCP_Helpers::sanitize_hex_color( $input['button_text_color'] ?? '' ) ?: $defaults['button_text_color'];
		$clean['popup_title']         = RASASCP_Helpers::sanitize_text( $input['popup_title'] ?? '' );
		$clean['popup_description']   = wp_kses_post( $input['popup_description'] ?? '' );
		$clean['popup_width']         = RASASCP_Helpers::sanitize_integer( $input['popup_width'] ?? 420, 200, 800, $defaults['popup_width'] );
		$clean['popup_border_radius'] = RASASCP_Helpers::sanitize_integer( $input['popup_border_radius'] ?? 12, 0, 50, $defaults['popup_border_radius'] );
		$clean['popup_bg_color']      = RASASCP_Helpers::sanitize_hex_color( $input['popup_bg_color'] ?? '' ) ?: $defaults['popup_bg_color'];
		$clean['popup_text_color']    = RASASCP_Helpers::sanitize_hex_color( $input['popup_text_color'] ?? '' ) ?: $defaults['popup_text_color'];
		$clean['popup_box_shadow']    = ! empty( $input['popup_box_shadow'] );
		$clean['popup_columns']       = RASASCP_Helpers::sanitize_integer( $input['popup_columns'] ?? 2, 1, 3, $defaults['popup_columns'] );
		$clean['card_bg_color']       = RASASCP_Helpers::sanitize_hex_color( $input['card_bg_color'] ?? '' ) ?: $defaults['card_bg_color'];
		$clean['card_hover_color']    = RASASCP_Helpers::sanitize_hex_color( $input['card_hover_color'] ?? '' ) ?: $defaults['card_hover_color'];
		$clean['card_icon_color']     = RASASCP_Helpers::sanitize_hex_color( $input['card_icon_color'] ?? '' );
		$clean['card_border_radius']  = RASASCP_Helpers::sanitize_integer( $input['card_border_radius'] ?? 8, 0, 50, $defaults['card_border_radius'] );
		$clean['animation_style']     = RASASCP_Helpers::sanitize_select( $input['animation_style'] ?? '', [ 'none', 'fade', 'slide-up', 'zoom' ], $defaults['animation_style'] );
		$clean['mobile_columns']      = RASASCP_Helpers::sanitize_integer( $input['mobile_columns'] ?? 1, 1, 2, $defaults['mobile_columns'] );
		$clean['mobile_full_width']   = ! empty( $input['mobile_full_width'] );
		$clean['show_branding']       = ! empty( $input['show_branding'] );
		$clean['platforms']           = [];

		$raw_platforms = isset( $input['platforms'] ) && is_array( $input['platforms'] ) ? $input['platforms'] : [];

		foreach ( $defaults['platforms'] as $id => $default_platform ) {
			$raw = isset( $raw_platforms[ $id ] ) && is_array( $raw_platforms[ $id ] ) ? $raw_platforms[ $id ] : [];

			$clean['platforms'][ $id ] = [
				'enabled'     => ! empty( $raw['enabled'] ),
				'label'       => RASASCP_Helpers::sanitize_text( $raw['label'] ?? $default_platform['label'] ) ?: $default_platform['label'],
				'value'       => $this->sanitize_platform_value( $id, $raw['value'] ?? '' ),
				'message'     => RASASCP_Helpers::sanitize_textarea( $raw['message'] ?? '' ),
				'description' => RASASCP_Helpers::sanitize_text( $raw['description'] ?? '' ),
				'sort_order'  => RASASCP_Helpers::sanitize_sort_order( $raw['sort_order'] ?? $default_platform['sort_order'] ),
				'new_tab'     => ! empty( $raw['new_tab'] ),
			];
		}

		return $clean;
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$active_tab = filter_input( INPUT_GET, 'tab', FILTER_UNSAFE_RAW );
		$active_tab = is_string( $active_tab ) ? sanitize_key( wp_unslash( $active_tab ) ) : 'general';
		if ( ! in_array( $active_tab, self::$tabs, true ) ) {
			$active_tab = 'general';
		}

		$saved_notice = filter_input( INPUT_GET, 'rasascp_saved', FILTER_SANITIZE_NUMBER_INT );
		$saved_notice = '1' === (string) $saved_notice;
		$options      = RASASCP_Plugin::get_options();
		?>
		<div class="wrap rasascp-wrap">
			<h1 class="rasascp-page-title">
				<span class="rasascp-logo">&#9993;</span>
				<?php esc_html_e( 'RASA - Social Contact Panel', 'rasa-social-contact-panel' ); ?>
				<span class="rasascp-version">v<?php echo esc_html( $this->version ); ?></span>
			</h1>

			<?php if ( $saved_notice ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Settings saved successfully.', 'rasa-social-contact-panel' ); ?></p>
				</div>
			<?php endif; ?>

			<nav class="rasascp-tabs" aria-label="<?php esc_attr_e( 'Settings tabs', 'rasa-social-contact-panel' ); ?>">
				<?php foreach ( $this->get_tab_labels() as $slug => $label ) : ?>
					<?php $url = add_query_arg( [ 'page' => 'rasa-social-contact-panel', 'tab' => $slug ], admin_url( 'admin.php' ) ); ?>
					<a
						href="<?php echo esc_url( $url ); ?>"
						class="rasascp-tab<?php echo $active_tab === $slug ? ' rasascp-tab--active' : ''; ?>"
						aria-current="<?php echo $active_tab === $slug ? 'page' : 'false'; ?>"
					>
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="rasascp-form" id="rasascp-settings-form">
				<?php wp_nonce_field( 'rasascp_save_settings', 'rasascp_nonce' ); ?>
				<input type="hidden" name="action" value="rasascp_save">
				<input type="hidden" name="rasascp_current_tab" value="<?php echo esc_attr( $active_tab ); ?>" id="rasascp-current-tab">

				<div class="rasascp-tab-content">
					<?php
					switch ( $active_tab ) {
						case 'platforms':
							$this->render_tab_platforms( $options );
							break;

						case 'design':
							$this->render_tab_design( $options );
							break;

						case 'advanced':
							$this->render_tab_advanced( $options );
							break;

						default:
							$this->render_tab_general( $options );
							break;
					}
					?>
				</div>

				<?php if ( 'advanced' !== $active_tab ) : ?>
					<div class="rasascp-submit-row">
						<?php submit_button( __( 'Save Settings', 'rasa-social-contact-panel' ), 'primary', 'submit', false ); ?>
					</div>
				<?php endif; ?>
			</form>
		</div>
		<?php
	}

	private function redirect_after_save( $tab ) {
		wp_safe_redirect(
			add_query_arg(
				[
					'page'      => 'rasa-social-contact-panel',
					'rasascp_saved' => '1',
					'tab'       => $tab,
				],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	private function sanitize_platform_value( $platform_id, $value ) {
		$type = self::$platform_meta[ $platform_id ]['type'] ?? 'text';

		switch ( $type ) {
			case 'url':
				$sanitized = RASASCP_Helpers::sanitize_url( $value );
				return '' !== $sanitized ? $sanitized : RASASCP_Helpers::sanitize_text( $value );

			case 'email':
				return RASASCP_Helpers::sanitize_email_field( $value );

			case 'phone':
				return RASASCP_Helpers::sanitize_phone( $value );

			default:
				return RASASCP_Helpers::sanitize_text( $value );
		}
	}

	private function merge_tab_settings( array $saved, array $posted, $current_tab ) {
		$saved = $this->sanitize_settings( $saved );

		switch ( $current_tab ) {
			case 'general':
				foreach ( $this->get_general_setting_keys() as $key ) {
					$saved[ $key ] = $posted[ $key ];
				}
				break;

			case 'platforms':
				$saved['platforms'] = $posted['platforms'];
				break;

			case 'design':
				foreach ( $this->get_design_setting_keys() as $key ) {
					$saved[ $key ] = $posted[ $key ];
				}
				break;
		}

		return $saved;
	}

	private function get_general_setting_keys() {
		return [
			'enabled',
			'button_position',
			'button_text',
			'button_side_text',
			'button_color',
			'button_text_color',
			'popup_title',
			'popup_description',
			'show_branding',
		];
	}

	private function get_design_setting_keys() {
		return [
			'popup_width',
			'popup_border_radius',
			'popup_bg_color',
			'popup_text_color',
			'popup_box_shadow',
			'popup_columns',
			'card_bg_color',
			'card_hover_color',
			'card_icon_color',
			'card_border_radius',
			'animation_style',
			'mobile_columns',
			'mobile_full_width',
		];
	}

	private function get_tab_labels() {
		return [
			'general'   => __( 'General', 'rasa-social-contact-panel' ),
			'platforms' => __( 'Platforms', 'rasa-social-contact-panel' ),
			'design'    => __( 'Design', 'rasa-social-contact-panel' ),
			'advanced'  => __( 'Advanced', 'rasa-social-contact-panel' ),
		];
	}

	private function render_tab_general( array $options ) {
		?>
		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Plugin Status', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><?php esc_html_e( 'Enable Plugin', 'rasa-social-contact-panel' ); ?></th>
					<td>
						<label class="rasascp-toggle">
							<input type="checkbox" name="rasascp[enabled]" value="1" <?php checked( ! empty( $options['enabled'] ) ); ?>>
							<span class="rasascp-toggle__slider"></span>
						</label>
						<p class="description"><?php esc_html_e( 'When disabled, the floating button and popup will not appear on the frontend and no assets will be loaded.', 'rasa-social-contact-panel' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Floating Button', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><?php esc_html_e( 'Position', 'rasa-social-contact-panel' ); ?></th>
					<td>
						<label>
							<input type="radio" name="rasascp[button_position]" value="bottom-left" <?php checked( $options['button_position'], 'bottom-left' ); ?>>
							<?php esc_html_e( 'Bottom Left', 'rasa-social-contact-panel' ); ?>
						</label>
						&nbsp;&nbsp;
						<label>
							<input type="radio" name="rasascp[button_position]" value="bottom-right" <?php checked( $options['button_position'], 'bottom-right' ); ?>>
							<?php esc_html_e( 'Bottom Right', 'rasa-social-contact-panel' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-button-text"><?php esc_html_e( 'Button Text', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="text" id="rasascp-button-text" name="rasascp[button_text]" value="<?php echo esc_attr( $options['button_text'] ); ?>" class="regular-text" maxlength="60">
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-button-side-text"><?php esc_html_e( 'Side Text', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="text" id="rasascp-button-side-text" name="rasascp[button_side_text]" value="<?php echo esc_attr( $options['button_side_text'] ); ?>" class="regular-text" maxlength="80" placeholder="<?php esc_attr_e( 'Need help?', 'rasa-social-contact-panel' ); ?>">
						<p class="description"><?php esc_html_e( 'Optional text shown beside the floating button. Leave blank to hide.', 'rasa-social-contact-panel' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-button-color"><?php esc_html_e( 'Button Background Color', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="rasascp-button-color" name="rasascp[button_color]" value="<?php echo esc_attr( $options['button_color'] ); ?>">
						<span class="rasascp-color-value"><?php echo esc_html( $options['button_color'] ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-button-text-color"><?php esc_html_e( 'Button Text Color', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="rasascp-button-text-color" name="rasascp[button_text_color]" value="<?php echo esc_attr( $options['button_text_color'] ); ?>">
						<span class="rasascp-color-value"><?php echo esc_html( $options['button_text_color'] ); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Popup Content', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><label for="rasascp-popup-title"><?php esc_html_e( 'Popup Title', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="text" id="rasascp-popup-title" name="rasascp[popup_title]" value="<?php echo esc_attr( $options['popup_title'] ); ?>" class="regular-text" maxlength="100">
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-popup-description"><?php esc_html_e( 'Popup Description', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<?php
						wp_editor(
							$options['popup_description'],
							'rasascp-popup-description',
							[
								'textarea_name' => 'rasascp[popup_description]',
								'textarea_rows' => 5,
								'media_buttons' => false,
								'teeny'         => true,
							]
						);
						?>
						<p class="description"><?php esc_html_e( 'Optional rich text shown below the popup title. Leave blank to hide.', 'rasa-social-contact-panel' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Branding', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><?php esc_html_e( 'Show Branding', 'rasa-social-contact-panel' ); ?></th>
					<td>
						<label class="rasascp-toggle">
							<input type="checkbox" name="rasascp[show_branding]" value="1" <?php checked( ! empty( $options['show_branding'] ) ); ?>>
							<span class="rasascp-toggle__slider"></span>
						</label>
						<p class="description"><?php esc_html_e( 'Show "Powered by RASA - Social Contact Panel" at the bottom of the popup.', 'rasa-social-contact-panel' ); ?></p>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	private function render_tab_platforms( array $options ) {
		$platforms = $options['platforms'];

		uasort(
			$platforms,
			static function ( $first, $second ) {
				return $first['sort_order'] <=> $second['sort_order'];
			}
		);
		?>
		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Contact Platforms', 'rasa-social-contact-panel' ); ?></h2>
			<p class="description" style="margin-bottom:16px"><?php esc_html_e( 'Enable and configure each platform. Only enabled platforms appear in the popup.', 'rasa-social-contact-panel' ); ?></p>

			<div class="rasascp-platforms-list" id="rasascp-platforms-list">
				<?php foreach ( $platforms as $id => $platform ) : ?>
					<div class="rasascp-platform-row" data-id="<?php echo esc_attr( $id ); ?>">
						<div class="rasascp-platform-row__header" role="button" tabindex="0" aria-expanded="false">
							<span class="rasascp-platform-row__toggle-enabled">
								<label class="rasascp-toggle rasascp-toggle--sm" title="<?php esc_attr_e( 'Enable/Disable', 'rasa-social-contact-panel' ); ?>">
									<input type="checkbox" name="rasascp[platforms][<?php echo esc_attr( $id ); ?>][enabled]" value="1" <?php checked( ! empty( $platform['enabled'] ) ); ?> onclick="event.stopPropagation()">
									<span class="rasascp-toggle__slider"></span>
								</label>
							</span>
							<span class="rasascp-platform-row__name"><?php echo esc_html( $platform['label'] ); ?></span>
							<span class="rasascp-platform-row__value-preview"><?php echo esc_html( $platform['value'] ? $platform['value'] : __( 'Not configured yet', 'rasa-social-contact-panel' ) ); ?></span>
							<span class="rasascp-platform-row__sort">
								<button type="button" class="rasascp-sort-btn rasascp-sort-btn--up" title="<?php esc_attr_e( 'Move up', 'rasa-social-contact-panel' ); ?>" onclick="event.stopPropagation()" aria-label="<?php esc_attr_e( 'Move up', 'rasa-social-contact-panel' ); ?>">&#9650;</button>
								<button type="button" class="rasascp-sort-btn rasascp-sort-btn--down" title="<?php esc_attr_e( 'Move down', 'rasa-social-contact-panel' ); ?>" onclick="event.stopPropagation()" aria-label="<?php esc_attr_e( 'Move down', 'rasa-social-contact-panel' ); ?>">&#9660;</button>
							</span>
							<span class="rasascp-platform-row__chevron">&#9660;</span>
						</div>

						<div class="rasascp-platform-row__body" hidden>
							<table class="rasascp-table rasascp-table--inner">
								<tr>
									<th><label><?php esc_html_e( 'Display Name', 'rasa-social-contact-panel' ); ?></label></th>
									<td>
										<input type="text" name="rasascp[platforms][<?php echo esc_attr( $id ); ?>][label]" value="<?php echo esc_attr( $platform['label'] ); ?>" class="regular-text" maxlength="50">
									</td>
								</tr>
								<tr>
									<th><label><?php echo esc_html( $this->get_value_field_label( $id ) ); ?></label></th>
									<td>
										<input type="text" name="rasascp[platforms][<?php echo esc_attr( $id ); ?>][value]" value="<?php echo esc_attr( $platform['value'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr( RASASCP_Helpers::get_value_placeholder( $id ) ); ?>">
									</td>
								</tr>
								<?php if ( 'whatsapp' === $id ) : ?>
									<tr>
										<th><label><?php esc_html_e( 'Prefilled Message', 'rasa-social-contact-panel' ); ?></label></th>
										<td>
											<textarea name="rasascp[platforms][<?php echo esc_attr( $id ); ?>][message]" class="large-text" rows="3" placeholder="<?php esc_attr_e( 'Hello, I would like to know more.', 'rasa-social-contact-panel' ); ?>"><?php echo esc_textarea( $platform['message'] ?? '' ); ?></textarea>
											<p class="description"><?php esc_html_e( 'Optional message that appears in WhatsApp before the visitor sends it.', 'rasa-social-contact-panel' ); ?></p>
										</td>
									</tr>
								<?php endif; ?>
								<tr>
									<th><label><?php esc_html_e( 'Description', 'rasa-social-contact-panel' ); ?></label></th>
									<td>
										<input type="text" name="rasascp[platforms][<?php echo esc_attr( $id ); ?>][description]" value="<?php echo esc_attr( $platform['description'] ); ?>" class="regular-text" maxlength="80" placeholder="<?php esc_attr_e( 'Short subtitle shown under the platform name (optional)', 'rasa-social-contact-panel' ); ?>">
									</td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Sort Order', 'rasa-social-contact-panel' ); ?></th>
									<td>
										<input type="number" name="rasascp[platforms][<?php echo esc_attr( $id ); ?>][sort_order]" value="<?php echo esc_attr( $platform['sort_order'] ); ?>" min="1" max="99" step="1" class="small-text">
									</td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Open in New Tab', 'rasa-social-contact-panel' ); ?></th>
									<td>
										<label class="rasascp-toggle rasascp-toggle--sm">
											<input type="checkbox" name="rasascp[platforms][<?php echo esc_attr( $id ); ?>][new_tab]" value="1" <?php checked( ! empty( $platform['new_tab'] ) ); ?>>
											<span class="rasascp-toggle__slider"></span>
										</label>
									</td>
								</tr>
							</table>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	private function get_value_field_label( $platform_id ) {
		$labels = [
			'whatsapp'  => __( 'Phone Number', 'rasa-social-contact-panel' ),
			'telegram'  => __( 'Username or Phone', 'rasa-social-contact-panel' ),
			'messenger' => __( 'Username / Page', 'rasa-social-contact-panel' ),
			'instagram' => __( 'Username', 'rasa-social-contact-panel' ),
			'facebook'  => __( 'Page URL or Username', 'rasa-social-contact-panel' ),
			'twitter'   => __( 'Username', 'rasa-social-contact-panel' ),
			'linkedin'  => __( 'Profile URL or Username', 'rasa-social-contact-panel' ),
			'tiktok'    => __( 'Username', 'rasa-social-contact-panel' ),
			'youtube'   => __( 'Channel URL or Handle', 'rasa-social-contact-panel' ),
			'snapchat'  => __( 'Username', 'rasa-social-contact-panel' ),
			'pinterest' => __( 'Username', 'rasa-social-contact-panel' ),
			'discord'   => __( 'Invite Code or Link', 'rasa-social-contact-panel' ),
			'skype'     => __( 'Skype ID', 'rasa-social-contact-panel' ),
			'viber'     => __( 'Phone Number', 'rasa-social-contact-panel' ),
			'line'      => __( 'Username', 'rasa-social-contact-panel' ),
			'wechat'    => __( 'Page URL', 'rasa-social-contact-panel' ),
			'email'     => __( 'Email Address', 'rasa-social-contact-panel' ),
			'phone'     => __( 'Phone Number', 'rasa-social-contact-panel' ),
			'sms'       => __( 'Phone Number', 'rasa-social-contact-panel' ),
			'website'   => __( 'Website URL', 'rasa-social-contact-panel' ),
		];

		return $labels[ $platform_id ] ?? __( 'Value', 'rasa-social-contact-panel' );
	}

	private function render_tab_design( array $options ) {
		$animations = [
			'none'     => __( 'None', 'rasa-social-contact-panel' ),
			'fade'     => __( 'Fade', 'rasa-social-contact-panel' ),
			'slide-up' => __( 'Slide Up', 'rasa-social-contact-panel' ),
			'zoom'     => __( 'Zoom', 'rasa-social-contact-panel' ),
		];
		?>
		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Popup Dimensions', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><label for="rasascp-popup-width"><?php esc_html_e( 'Popup Width (px)', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="number" id="rasascp-popup-width" name="rasascp[popup_width]" value="<?php echo esc_attr( $options['popup_width'] ); ?>" min="200" max="800" step="10" class="small-text">
						<p class="description"><?php esc_html_e( '200 - 800 px', 'rasa-social-contact-panel' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-popup-border-radius"><?php esc_html_e( 'Border Radius (px)', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="number" id="rasascp-popup-border-radius" name="rasascp[popup_border_radius]" value="<?php echo esc_attr( $options['popup_border_radius'] ); ?>" min="0" max="50" step="1" class="small-text">
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Box Shadow', 'rasa-social-contact-panel' ); ?></th>
					<td>
						<label class="rasascp-toggle">
							<input type="checkbox" name="rasascp[popup_box_shadow]" value="1" <?php checked( ! empty( $options['popup_box_shadow'] ) ); ?>>
							<span class="rasascp-toggle__slider"></span>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Popup Colors', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><label for="rasascp-popup-bg-color"><?php esc_html_e( 'Background Color', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="rasascp-popup-bg-color" name="rasascp[popup_bg_color]" value="<?php echo esc_attr( $options['popup_bg_color'] ); ?>">
						<span class="rasascp-color-value"><?php echo esc_html( $options['popup_bg_color'] ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-popup-text-color"><?php esc_html_e( 'Text Color', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="rasascp-popup-text-color" name="rasascp[popup_text_color]" value="<?php echo esc_attr( $options['popup_text_color'] ); ?>">
						<span class="rasascp-color-value"><?php echo esc_html( $options['popup_text_color'] ); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Layout', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><?php esc_html_e( 'Desktop Columns', 'rasa-social-contact-panel' ); ?></th>
					<td>
						<?php foreach ( [ 1, 2, 3 ] as $columns ) : ?>
							<label>
								<input type="radio" name="rasascp[popup_columns]" value="<?php echo esc_attr( $columns ); ?>" <?php checked( (int) $options['popup_columns'], $columns ); ?>>
								<?php echo esc_html( $columns . ' ' . _n( 'Column', 'Columns', $columns, 'rasa-social-contact-panel' ) ); ?>
							</label>
							&nbsp;&nbsp;
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Mobile Columns', 'rasa-social-contact-panel' ); ?></th>
					<td>
						<?php foreach ( [ 1, 2 ] as $columns ) : ?>
							<label>
								<input type="radio" name="rasascp[mobile_columns]" value="<?php echo esc_attr( $columns ); ?>" <?php checked( (int) $options['mobile_columns'], $columns ); ?>>
								<?php echo esc_html( $columns . ' ' . _n( 'Column', 'Columns', $columns, 'rasa-social-contact-panel' ) ); ?>
							</label>
							&nbsp;&nbsp;
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Full-Width on Mobile', 'rasa-social-contact-panel' ); ?></th>
					<td>
						<label class="rasascp-toggle">
							<input type="checkbox" name="rasascp[mobile_full_width]" value="1" <?php checked( ! empty( $options['mobile_full_width'] ) ); ?>>
							<span class="rasascp-toggle__slider"></span>
						</label>
						<p class="description"><?php esc_html_e( 'Makes the popup fill the screen width on small screens.', 'rasa-social-contact-panel' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Card Styling', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><label for="rasascp-card-bg-color"><?php esc_html_e( 'Card Background', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="rasascp-card-bg-color" name="rasascp[card_bg_color]" value="<?php echo esc_attr( $options['card_bg_color'] ); ?>">
						<span class="rasascp-color-value"><?php echo esc_html( $options['card_bg_color'] ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-card-hover-color"><?php esc_html_e( 'Card Hover Color', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="rasascp-card-hover-color" name="rasascp[card_hover_color]" value="<?php echo esc_attr( $options['card_hover_color'] ); ?>">
						<span class="rasascp-color-value"><?php echo esc_html( $options['card_hover_color'] ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-card-icon-color"><?php esc_html_e( 'Icon Color Override', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="rasascp-card-icon-color" name="rasascp[card_icon_color]" value="<?php echo esc_attr( $options['card_icon_color'] ); ?>">
						<span class="rasascp-color-value"><?php echo esc_html( $options['card_icon_color'] ? $options['card_icon_color'] : __( 'Brand colors', 'rasa-social-contact-panel' ) ); ?></span>
						<br>
						<label for="rasascp-use-brand-colors">
							<input type="checkbox" id="rasascp-use-brand-colors" <?php checked( empty( $options['card_icon_color'] ) ); ?>>
							<?php esc_html_e( 'Use platform brand colors', 'rasa-social-contact-panel' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Uncheck to use a uniform icon color for all platforms.', 'rasa-social-contact-panel' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="rasascp-card-border-radius"><?php esc_html_e( 'Card Border Radius (px)', 'rasa-social-contact-panel' ); ?></label></th>
					<td>
						<input type="number" id="rasascp-card-border-radius" name="rasascp[card_border_radius]" value="<?php echo esc_attr( $options['card_border_radius'] ); ?>" min="0" max="50" step="1" class="small-text">
					</td>
				</tr>
			</table>
		</div>

		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Animation', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><?php esc_html_e( 'Open Animation', 'rasa-social-contact-panel' ); ?></th>
					<td>
						<?php foreach ( $animations as $value => $label ) : ?>
							<label>
								<input type="radio" name="rasascp[animation_style]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $options['animation_style'], $value ); ?>>
								<?php echo esc_html( $label ); ?>
							</label>
							&nbsp;&nbsp;
						<?php endforeach; ?>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	private function render_tab_advanced( array $options ) {
		?>
		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Export / Import Settings', 'rasa-social-contact-panel' ); ?></h2>
			<div class="rasascp-advanced-actions">
				<div class="rasascp-advanced-card">
					<h3><?php esc_html_e( 'Export Settings', 'rasa-social-contact-panel' ); ?></h3>
					<p><?php esc_html_e( 'Download all plugin settings as a JSON file for backup or migration.', 'rasa-social-contact-panel' ); ?></p>
					<button type="button" class="button button-secondary" id="rasascp-export-btn">
						<?php esc_html_e( 'Export Settings JSON', 'rasa-social-contact-panel' ); ?>
					</button>
					<textarea id="rasascp-export-data" class="rasascp-hidden" readonly aria-label="<?php esc_attr_e( 'Settings JSON', 'rasa-social-contact-panel' ); ?>"><?php echo esc_textarea( wp_json_encode( $options, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) ); ?></textarea>
				</div>

				<div class="rasascp-advanced-card">
					<h3><?php esc_html_e( 'Import Settings', 'rasa-social-contact-panel' ); ?></h3>
					<p><?php esc_html_e( 'Import settings from a previously exported JSON file.', 'rasa-social-contact-panel' ); ?></p>
					<label class="button button-secondary" for="rasascp-import-file" style="cursor:pointer">
						<?php esc_html_e( 'Choose JSON File', 'rasa-social-contact-panel' ); ?>
					</label>
					<input type="file" id="rasascp-import-file" accept=".json,application/json" class="rasascp-hidden" aria-label="<?php esc_attr_e( 'Import JSON file', 'rasa-social-contact-panel' ); ?>">
					<input type="hidden" name="" id="rasascp-import-data">
					<p id="rasascp-import-filename" class="description"></p>
					<p id="rasascp-import-message" class="description" aria-live="polite"></p>
				</div>
			</div>
		</div>

		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Reset Settings', 'rasa-social-contact-panel' ); ?></h2>
			<p><?php esc_html_e( 'Reset all settings back to their default values. This cannot be undone.', 'rasa-social-contact-panel' ); ?></p>
			<button type="button" class="button button-link-delete" id="rasascp-reset-btn">
				<?php esc_html_e( 'Reset to Defaults', 'rasa-social-contact-panel' ); ?>
			</button>
		</div>

		<div class="rasascp-section">
			<h2 class="rasascp-section-title"><?php esc_html_e( 'Plugin Info', 'rasa-social-contact-panel' ); ?></h2>
			<table class="rasascp-table">
				<tr>
					<th><?php esc_html_e( 'Version', 'rasa-social-contact-panel' ); ?></th>
					<td><?php echo esc_html( $this->version ); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Option Key', 'rasa-social-contact-panel' ); ?></th>
					<td><?php echo esc_html( $this->option_key ); ?></td>
				</tr>
			</table>
		</div>

		<div class="rasascp-submit-row">
			<?php submit_button( __( 'Save Imported Settings', 'rasa-social-contact-panel' ), 'primary', 'submit', false ); ?>
		</div>
		<?php
	}
}
