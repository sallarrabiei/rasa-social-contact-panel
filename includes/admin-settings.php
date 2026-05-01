<?php
/**
 * SSCP_Settings — admin settings page with 4 tabs.
 *
 * Tab 1 General  : enable/disable, floating button, popup content, branding
 * Tab 2 Platforms: 20 collapsible platform rows
 * Tab 3 Design   : popup dimensions/colors, card styling, animation, mobile
 * Tab 4 Advanced : reset, export JSON, import JSON
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class SSCP_Settings {

	/** @var string */
	private $option_key;

	/** @var string */
	private $version;

	/** Valid tab slugs */
	private static $tabs = [ 'general', 'platforms', 'design', 'advanced' ];

	/** Platforms metadata (label + value-field type hint) */
	private static $platform_meta = [
		'whatsapp'  => [ 'label' => 'WhatsApp',    'type' => 'phone'    ],
		'telegram'  => [ 'label' => 'Telegram',    'type' => 'text'     ],
		'messenger' => [ 'label' => 'Messenger',   'type' => 'text'     ],
		'instagram' => [ 'label' => 'Instagram',   'type' => 'text'     ],
		'facebook'  => [ 'label' => 'Facebook',    'type' => 'url'      ],
		'twitter'   => [ 'label' => 'X / Twitter', 'type' => 'text'     ],
		'linkedin'  => [ 'label' => 'LinkedIn',    'type' => 'url'      ],
		'tiktok'    => [ 'label' => 'TikTok',      'type' => 'text'     ],
		'youtube'   => [ 'label' => 'YouTube',     'type' => 'url'      ],
		'snapchat'  => [ 'label' => 'Snapchat',    'type' => 'text'     ],
		'pinterest' => [ 'label' => 'Pinterest',   'type' => 'text'     ],
		'discord'   => [ 'label' => 'Discord',     'type' => 'text'     ],
		'skype'     => [ 'label' => 'Skype',       'type' => 'text'     ],
		'viber'     => [ 'label' => 'Viber',       'type' => 'phone'    ],
		'line'      => [ 'label' => 'Line',        'type' => 'text'     ],
		'wechat'    => [ 'label' => 'WeChat',      'type' => 'url'      ],
		'email'     => [ 'label' => 'Email',       'type' => 'email'    ],
		'phone'     => [ 'label' => 'Phone Call',  'type' => 'phone'    ],
		'sms'       => [ 'label' => 'SMS',         'type' => 'phone'    ],
		'website'   => [ 'label' => 'Website',     'type' => 'url'      ],
	];

	public function __construct( $option_key, $version ) {
		$this->option_key = $option_key;
		$this->version    = $version;
	}

	public function register_hooks() {
		add_action( 'admin_menu',             [ $this, 'add_settings_page' ] );
		add_action( 'admin_post_sscp_save',   [ $this, 'handle_save' ] );
		add_action( 'admin_enqueue_scripts',  [ $this, 'enqueue_admin_assets' ] );
	}

	// -------------------------------------------------------------------------
	// Default options schema
	// -------------------------------------------------------------------------

	public static function get_default_options() {
		return [
			// Global
			'enabled'             => false,
			'button_position'     => 'bottom-right',
			'button_text'         => 'Contact Us',
			'button_side_text'    => '',
			'button_color'        => '#25d366',
			'button_text_color'   => '#ffffff',

			// Popup
			'popup_title'         => 'Contact Us',
			'popup_description'   => '',
			'popup_width'         => 420,
			'popup_border_radius' => 12,
			'popup_bg_color'      => '#ffffff',
			'popup_text_color'    => '#333333',
			'popup_box_shadow'    => true,
			'popup_columns'       => 2,

			// Card styling
			'card_bg_color'       => '#f8f9fa',
			'card_hover_color'    => '#e9ecef',
			'card_icon_color'     => '',
			'card_border_radius'  => 8,

			// Animation
			'animation_style'     => 'slide-up',

			// Mobile
			'mobile_columns'      => 1,
			'mobile_full_width'   => true,

			// Branding
			'show_branding'       => false,

			// Platforms
			'platforms' => [
				'whatsapp'  => [ 'enabled' => false, 'label' => 'WhatsApp',    'value' => '', 'message' => '', 'description' => '', 'sort_order' => 1,  'new_tab' => true  ],
				'telegram'  => [ 'enabled' => false, 'label' => 'Telegram',    'value' => '', 'description' => '', 'sort_order' => 2,  'new_tab' => true  ],
				'messenger' => [ 'enabled' => false, 'label' => 'Messenger',   'value' => '', 'description' => '', 'sort_order' => 3,  'new_tab' => true  ],
				'instagram' => [ 'enabled' => false, 'label' => 'Instagram',   'value' => '', 'description' => '', 'sort_order' => 4,  'new_tab' => true  ],
				'facebook'  => [ 'enabled' => false, 'label' => 'Facebook',    'value' => '', 'description' => '', 'sort_order' => 5,  'new_tab' => true  ],
				'twitter'   => [ 'enabled' => false, 'label' => 'X / Twitter', 'value' => '', 'description' => '', 'sort_order' => 6,  'new_tab' => true  ],
				'linkedin'  => [ 'enabled' => false, 'label' => 'LinkedIn',    'value' => '', 'description' => '', 'sort_order' => 7,  'new_tab' => true  ],
				'tiktok'    => [ 'enabled' => false, 'label' => 'TikTok',      'value' => '', 'description' => '', 'sort_order' => 8,  'new_tab' => true  ],
				'youtube'   => [ 'enabled' => false, 'label' => 'YouTube',     'value' => '', 'description' => '', 'sort_order' => 9,  'new_tab' => true  ],
				'snapchat'  => [ 'enabled' => false, 'label' => 'Snapchat',    'value' => '', 'description' => '', 'sort_order' => 10, 'new_tab' => true  ],
				'pinterest' => [ 'enabled' => false, 'label' => 'Pinterest',   'value' => '', 'description' => '', 'sort_order' => 11, 'new_tab' => true  ],
				'discord'   => [ 'enabled' => false, 'label' => 'Discord',     'value' => '', 'description' => '', 'sort_order' => 12, 'new_tab' => true  ],
				'skype'     => [ 'enabled' => false, 'label' => 'Skype',       'value' => '', 'description' => '', 'sort_order' => 13, 'new_tab' => true  ],
				'viber'     => [ 'enabled' => false, 'label' => 'Viber',       'value' => '', 'description' => '', 'sort_order' => 14, 'new_tab' => true  ],
				'line'      => [ 'enabled' => false, 'label' => 'Line',        'value' => '', 'description' => '', 'sort_order' => 15, 'new_tab' => true  ],
				'wechat'    => [ 'enabled' => false, 'label' => 'WeChat',      'value' => '', 'description' => '', 'sort_order' => 16, 'new_tab' => false ],
				'email'     => [ 'enabled' => false, 'label' => 'Email',       'value' => '', 'description' => '', 'sort_order' => 17, 'new_tab' => false ],
				'phone'     => [ 'enabled' => false, 'label' => 'Phone Call',  'value' => '', 'description' => '', 'sort_order' => 18, 'new_tab' => false ],
				'sms'       => [ 'enabled' => false, 'label' => 'SMS',         'value' => '', 'description' => '', 'sort_order' => 19, 'new_tab' => false ],
				'website'   => [ 'enabled' => false, 'label' => 'Website',     'value' => '', 'description' => '', 'sort_order' => 20, 'new_tab' => true  ],
			],
		];
	}

	// -------------------------------------------------------------------------
	// Admin menu
	// -------------------------------------------------------------------------

	public function add_settings_page() {
		add_menu_page(
			esc_html__( 'Smart Social Contact', 'smart-social-contact-panel' ),
			esc_html__( 'Social Contact', 'smart-social-contact-panel' ),
			'manage_options',
			'smart-social-contact',
			[ $this, 'render_page' ],
			'dashicons-share',
			80
		);
	}

	public function enqueue_admin_assets( $hook ) {
		if ( 'toplevel_page_smart-social-contact' !== $hook ) return;

		wp_enqueue_style(
			'sscp-admin',
			SSCP_URL . 'assets/css/admin.css',
			[],
			SSCP_VERSION
		);
		wp_enqueue_script(
			'sscp-admin',
			SSCP_URL . 'assets/js/admin.js',
			[],
			SSCP_VERSION,
			true
		);
		wp_localize_script( 'sscp-admin', 'sscpAdmin', [
			'confirmReset'  => __( 'Reset all settings to defaults? This cannot be undone.', 'smart-social-contact-panel' ),
			'importInvalid' => __( 'Invalid settings file. Please export from this plugin.', 'smart-social-contact-panel' ),
		] );
	}

	// -------------------------------------------------------------------------
	// Save handler
	// -------------------------------------------------------------------------

	public function handle_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'smart-social-contact-panel' ) );
		}

		check_admin_referer( 'sscp_save_settings', 'sscp_nonce' );

		// Reset to defaults
		if ( ! empty( $_POST['sscp_reset'] ) ) {
			update_option( $this->option_key, self::get_default_options(), false );
			wp_safe_redirect(
				add_query_arg(
					[ 'page' => 'smart-social-contact', 'sscp_saved' => '1', 'tab' => 'general' ],
					admin_url( 'admin.php' )
				)
			);
			exit;
		}

		// JSON import
		if ( ! empty( $_POST['sscp_import_json'] ) ) {
			$json   = wp_unslash( $_POST['sscp_import_json'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$parsed = json_decode( $json, true );
			if ( is_array( $parsed ) ) {
				$clean = $this->sanitize_settings( $parsed );
				update_option( $this->option_key, $clean, false );
			}
			wp_safe_redirect(
				add_query_arg(
					[ 'page' => 'smart-social-contact', 'sscp_saved' => '1', 'tab' => 'advanced' ],
					admin_url( 'admin.php' )
				)
			);
			exit;
		}

		$current_tab = sanitize_key( wp_unslash( $_POST['sscp_current_tab'] ?? 'general' ) );
		$input       = isset( $_POST['sscp'] ) && is_array( $_POST['sscp'] ) ? wp_unslash( $_POST['sscp'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$clean       = $this->sanitize_settings( $input );
		$clean       = $this->merge_tab_settings( SSCP_Plugin::get_options(), $clean, $current_tab );

		update_option( $this->option_key, $clean, false );

		wp_safe_redirect(
			add_query_arg(
				[ 'page' => 'smart-social-contact', 'sscp_saved' => '1', 'tab' => $current_tab ],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	// -------------------------------------------------------------------------
	// Sanitize settings
	// -------------------------------------------------------------------------

	public function sanitize_settings( array $input ) {
		$defaults = self::get_default_options();
		$clean    = [];

		// Global
		$clean['enabled']           = ! empty( $input['enabled'] );
		$clean['button_position']   = SSCP_Helpers::sanitize_select( $input['button_position'] ?? '', [ 'bottom-left', 'bottom-right' ], $defaults['button_position'] );
		$clean['button_text']       = SSCP_Helpers::sanitize_text( $input['button_text'] ?? '' );
		$clean['button_side_text']  = SSCP_Helpers::sanitize_text( $input['button_side_text'] ?? '' );
		$clean['button_color']      = SSCP_Helpers::sanitize_hex_color( $input['button_color'] ?? '' ) ?: $defaults['button_color'];
		$clean['button_text_color'] = SSCP_Helpers::sanitize_hex_color( $input['button_text_color'] ?? '' ) ?: $defaults['button_text_color'];

		// Popup
		$clean['popup_title']         = SSCP_Helpers::sanitize_text( $input['popup_title'] ?? '' );
		$clean['popup_description']   = wp_kses_post( $input['popup_description'] ?? '' );
		$clean['popup_width']         = SSCP_Helpers::sanitize_integer( $input['popup_width'] ?? 420, 200, 800, $defaults['popup_width'] );
		$clean['popup_border_radius'] = SSCP_Helpers::sanitize_integer( $input['popup_border_radius'] ?? 12, 0, 50, $defaults['popup_border_radius'] );
		$clean['popup_bg_color']      = SSCP_Helpers::sanitize_hex_color( $input['popup_bg_color'] ?? '' ) ?: $defaults['popup_bg_color'];
		$clean['popup_text_color']    = SSCP_Helpers::sanitize_hex_color( $input['popup_text_color'] ?? '' ) ?: $defaults['popup_text_color'];
		$clean['popup_box_shadow']    = ! empty( $input['popup_box_shadow'] );
		$clean['popup_columns']       = SSCP_Helpers::sanitize_integer( $input['popup_columns'] ?? 2, 1, 3, $defaults['popup_columns'] );

		// Card
		$clean['card_bg_color']      = SSCP_Helpers::sanitize_hex_color( $input['card_bg_color'] ?? '' ) ?: $defaults['card_bg_color'];
		$clean['card_hover_color']   = SSCP_Helpers::sanitize_hex_color( $input['card_hover_color'] ?? '' ) ?: $defaults['card_hover_color'];
		$clean['card_icon_color']    = SSCP_Helpers::sanitize_hex_color( $input['card_icon_color'] ?? '' );
		$clean['card_border_radius'] = SSCP_Helpers::sanitize_integer( $input['card_border_radius'] ?? 8, 0, 50, $defaults['card_border_radius'] );

		// Animation
		$clean['animation_style'] = SSCP_Helpers::sanitize_select( $input['animation_style'] ?? '', [ 'none', 'fade', 'slide-up', 'zoom' ], $defaults['animation_style'] );

		// Mobile
		$clean['mobile_columns']    = SSCP_Helpers::sanitize_integer( $input['mobile_columns'] ?? 1, 1, 2, $defaults['mobile_columns'] );
		$clean['mobile_full_width'] = ! empty( $input['mobile_full_width'] );

		// Branding
		$clean['show_branding'] = ! empty( $input['show_branding'] );

		// Platforms
		$clean['platforms'] = [];
		$raw_platforms      = isset( $input['platforms'] ) && is_array( $input['platforms'] ) ? $input['platforms'] : [];

		foreach ( $defaults['platforms'] as $id => $default_platform ) {
			$raw = isset( $raw_platforms[ $id ] ) && is_array( $raw_platforms[ $id ] ) ? $raw_platforms[ $id ] : [];

			$clean['platforms'][ $id ] = [
				'enabled'     => ! empty( $raw['enabled'] ),
				'label'       => SSCP_Helpers::sanitize_text( $raw['label'] ?? $default_platform['label'] ) ?: $default_platform['label'],
				'value'       => SSCP_Helpers::sanitize_text( $raw['value'] ?? '' ),
				'message'     => SSCP_Helpers::sanitize_textarea( $raw['message'] ?? '' ),
				'description' => SSCP_Helpers::sanitize_text( $raw['description'] ?? '' ),
				'sort_order'  => SSCP_Helpers::sanitize_sort_order( $raw['sort_order'] ?? $default_platform['sort_order'] ),
				'new_tab'     => ! empty( $raw['new_tab'] ),
			];
		}

		return $clean;
	}

	/**
	 * The settings page renders one tab at a time, so a normal save only posts
	 * the fields from the active tab. Preserve the saved values from every
	 * other tab instead of treating missing checkboxes/fields as disabled.
	 */
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

			default:
				return $saved;
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

	// -------------------------------------------------------------------------
	// Page render
	// -------------------------------------------------------------------------

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) return;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only page state.
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
		if ( ! in_array( $active_tab, self::$tabs, true ) ) {
			$active_tab = 'general';
		}

		$options = SSCP_Plugin::get_options();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only success notice flag after redirect.
		$saved   = isset( $_GET['sscp_saved'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['sscp_saved'] ) );
		?>
		<div class="wrap sscp-wrap">
			<h1 class="sscp-page-title">
				<span class="sscp-logo">&#9993;</span>
				<?php esc_html_e( 'Smart Social Contact Panel', 'smart-social-contact-panel' ); ?>
				<span class="sscp-version">v<?php echo esc_html( $this->version ); ?></span>
			</h1>

			<?php if ( $saved ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Settings saved successfully.', 'smart-social-contact-panel' ); ?></p>
				</div>
			<?php endif; ?>

			<nav class="sscp-tabs" aria-label="<?php esc_attr_e( 'Settings tabs', 'smart-social-contact-panel' ); ?>">
				<?php
				$tab_labels = [
					'general'   => __( 'General', 'smart-social-contact-panel' ),
					'platforms' => __( 'Platforms', 'smart-social-contact-panel' ),
					'design'    => __( 'Design', 'smart-social-contact-panel' ),
					'advanced'  => __( 'Advanced', 'smart-social-contact-panel' ),
				];
				foreach ( $tab_labels as $slug => $label ) :
					$url = add_query_arg( [ 'page' => 'smart-social-contact', 'tab' => $slug ], admin_url( 'admin.php' ) );
					?>
					<a href="<?php echo esc_url( $url ); ?>"
					   class="sscp-tab<?php echo $active_tab === $slug ? ' sscp-tab--active' : ''; ?>"
					   aria-current="<?php echo $active_tab === $slug ? 'page' : 'false'; ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="sscp-form" id="sscp-settings-form">
				<?php wp_nonce_field( 'sscp_save_settings', 'sscp_nonce' ); ?>
				<input type="hidden" name="action" value="sscp_save">
				<input type="hidden" name="sscp_current_tab" value="<?php echo esc_attr( $active_tab ); ?>" id="sscp-current-tab">

				<div class="sscp-tab-content">
					<?php
					switch ( $active_tab ) {
						case 'general':   $this->render_tab_general( $options );   break;
						case 'platforms': $this->render_tab_platforms( $options ); break;
						case 'design':    $this->render_tab_design( $options );    break;
						case 'advanced':  $this->render_tab_advanced( $options );  break;
					}
					?>
				</div>

				<?php if ( 'advanced' !== $active_tab ) : ?>
					<div class="sscp-submit-row">
						<?php submit_button( __( 'Save Settings', 'smart-social-contact-panel' ), 'primary', 'submit', false ); ?>
					</div>
				<?php endif; ?>
			</form>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Tab: General
	// -------------------------------------------------------------------------

	private function render_tab_general( $o ) {
		?>
		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Plugin Status', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><?php esc_html_e( 'Enable Plugin', 'smart-social-contact-panel' ); ?></th>
					<td>
						<label class="sscp-toggle">
							<input type="checkbox" name="sscp[enabled]" value="1" <?php checked( ! empty( $o['enabled'] ) ); ?>>
							<span class="sscp-toggle__slider"></span>
						</label>
						<p class="description"><?php esc_html_e( 'When disabled, the floating button and popup will not appear on the frontend and no assets will be loaded.', 'smart-social-contact-panel' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Floating Button', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><?php esc_html_e( 'Position', 'smart-social-contact-panel' ); ?></th>
					<td>
						<label>
							<input type="radio" name="sscp[button_position]" value="bottom-left" <?php checked( $o['button_position'], 'bottom-left' ); ?>>
							<?php esc_html_e( 'Bottom Left', 'smart-social-contact-panel' ); ?>
						</label>
						&nbsp;&nbsp;
						<label>
							<input type="radio" name="sscp[button_position]" value="bottom-right" <?php checked( $o['button_position'], 'bottom-right' ); ?>>
							<?php esc_html_e( 'Bottom Right', 'smart-social-contact-panel' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th><label for="sscp-button-text"><?php esc_html_e( 'Button Text', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="text" id="sscp-button-text" name="sscp[button_text]"
							   value="<?php echo esc_attr( $o['button_text'] ); ?>"
							   class="regular-text" maxlength="60">
					</td>
				</tr>
				<tr>
					<th><label for="sscp-button-side-text"><?php esc_html_e( 'Side Text', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="text" id="sscp-button-side-text" name="sscp[button_side_text]"
							   value="<?php echo esc_attr( $o['button_side_text'] ); ?>"
							   class="regular-text" maxlength="80"
							   placeholder="<?php esc_attr_e( 'Need help?', 'smart-social-contact-panel' ); ?>">
						<p class="description"><?php esc_html_e( 'Optional text shown beside the floating button. Leave blank to hide.', 'smart-social-contact-panel' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="sscp-button-color"><?php esc_html_e( 'Button Background Color', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="sscp-button-color" name="sscp[button_color]"
							   value="<?php echo esc_attr( $o['button_color'] ); ?>">
						<span class="sscp-color-value"><?php echo esc_html( $o['button_color'] ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="sscp-button-text-color"><?php esc_html_e( 'Button Text Color', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="sscp-button-text-color" name="sscp[button_text_color]"
							   value="<?php echo esc_attr( $o['button_text_color'] ); ?>">
						<span class="sscp-color-value"><?php echo esc_html( $o['button_text_color'] ); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Popup Content', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><label for="sscp-popup-title"><?php esc_html_e( 'Popup Title', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="text" id="sscp-popup-title" name="sscp[popup_title]"
							   value="<?php echo esc_attr( $o['popup_title'] ); ?>"
							   class="regular-text" maxlength="100">
					</td>
				</tr>
				<tr>
					<th><label for="sscp-popup-description"><?php esc_html_e( 'Popup Description', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<?php
						wp_editor(
							$o['popup_description'],
							'sscp-popup-description',
							[
								'textarea_name' => 'sscp[popup_description]',
								'textarea_rows' => 5,
								'media_buttons' => false,
								'teeny'         => true,
							]
						);
						?>
						<p class="description"><?php esc_html_e( 'Optional rich text shown below the popup title. Leave blank to hide.', 'smart-social-contact-panel' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Branding', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><?php esc_html_e( 'Show Branding', 'smart-social-contact-panel' ); ?></th>
					<td>
						<label class="sscp-toggle">
							<input type="checkbox" name="sscp[show_branding]" value="1" <?php checked( ! empty( $o['show_branding'] ) ); ?>>
							<span class="sscp-toggle__slider"></span>
						</label>
						<p class="description"><?php esc_html_e( 'Show "Powered by Smart Social Contact Panel" at the bottom of the popup.', 'smart-social-contact-panel' ); ?></p>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Tab: Platforms
	// -------------------------------------------------------------------------

	private function render_tab_platforms( $o ) {
		$platforms = $o['platforms'];

		// Sort by sort_order for display
		uasort( $platforms, function( $a, $b ) {
			return $a['sort_order'] <=> $b['sort_order'];
		} );
		?>
		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Contact Platforms', 'smart-social-contact-panel' ); ?></h2>
			<p class="description" style="margin-bottom:16px"><?php esc_html_e( 'Enable and configure each platform. Only enabled platforms appear in the popup.', 'smart-social-contact-panel' ); ?></p>

			<div class="sscp-platforms-list" id="sscp-platforms-list">
				<?php foreach ( $platforms as $id => $p ) : ?>
				<div class="sscp-platform-row" data-id="<?php echo esc_attr( $id ); ?>">
					<div class="sscp-platform-row__header" role="button" tabindex="0" aria-expanded="false">
						<span class="sscp-platform-row__toggle-enabled">
							<label class="sscp-toggle sscp-toggle--sm" title="<?php esc_attr_e( 'Enable/Disable', 'smart-social-contact-panel' ); ?>">
								<input type="checkbox" name="sscp[platforms][<?php echo esc_attr( $id ); ?>][enabled]" value="1"
									   <?php checked( ! empty( $p['enabled'] ) ); ?>
									   onclick="event.stopPropagation()">
								<span class="sscp-toggle__slider"></span>
							</label>
						</span>
						<span class="sscp-platform-row__name"><?php echo esc_html( $p['label'] ); ?></span>
						<span class="sscp-platform-row__value-preview"><?php echo esc_html( $p['value'] ? $p['value'] : __( '— not configured —', 'smart-social-contact-panel' ) ); ?></span>
						<span class="sscp-platform-row__sort">
							<button type="button" class="sscp-sort-btn sscp-sort-btn--up" title="<?php esc_attr_e( 'Move up', 'smart-social-contact-panel' ); ?>" onclick="event.stopPropagation()" aria-label="<?php esc_attr_e( 'Move up', 'smart-social-contact-panel' ); ?>">&#9650;</button>
							<button type="button" class="sscp-sort-btn sscp-sort-btn--down" title="<?php esc_attr_e( 'Move down', 'smart-social-contact-panel' ); ?>" onclick="event.stopPropagation()" aria-label="<?php esc_attr_e( 'Move down', 'smart-social-contact-panel' ); ?>">&#9660;</button>
						</span>
						<span class="sscp-platform-row__chevron">&#9660;</span>
					</div>

					<div class="sscp-platform-row__body" hidden>
						<table class="sscp-table sscp-table--inner">
							<tr>
								<th><label><?php esc_html_e( 'Display Name', 'smart-social-contact-panel' ); ?></label></th>
								<td>
									<input type="text"
										   name="sscp[platforms][<?php echo esc_attr( $id ); ?>][label]"
										   value="<?php echo esc_attr( $p['label'] ); ?>"
										   class="regular-text" maxlength="50">
								</td>
							</tr>
							<tr>
								<th><label><?php echo esc_html( $this->get_value_field_label( $id ) ); ?></label></th>
								<td>
									<input type="text"
										   name="sscp[platforms][<?php echo esc_attr( $id ); ?>][value]"
										   value="<?php echo esc_attr( $p['value'] ); ?>"
										   class="regular-text"
										   placeholder="<?php echo esc_attr( SSCP_Helpers::get_value_placeholder( $id ) ); ?>">
								</td>
							</tr>
							<?php if ( 'whatsapp' === $id ) : ?>
							<tr>
								<th><label><?php esc_html_e( 'Prefilled Message', 'smart-social-contact-panel' ); ?></label></th>
								<td>
									<textarea name="sscp[platforms][<?php echo esc_attr( $id ); ?>][message]"
											  class="large-text" rows="3"
											  placeholder="<?php esc_attr_e( 'Hello, I would like to know more.', 'smart-social-contact-panel' ); ?>"><?php echo esc_textarea( $p['message'] ?? '' ); ?></textarea>
									<p class="description"><?php esc_html_e( 'Optional message that appears in WhatsApp before the visitor sends it.', 'smart-social-contact-panel' ); ?></p>
								</td>
							</tr>
							<?php endif; ?>
							<tr>
								<th><label><?php esc_html_e( 'Description', 'smart-social-contact-panel' ); ?></label></th>
								<td>
									<input type="text"
										   name="sscp[platforms][<?php echo esc_attr( $id ); ?>][description]"
										   value="<?php echo esc_attr( $p['description'] ); ?>"
										   class="regular-text" maxlength="80"
										   placeholder="<?php esc_attr_e( 'Short subtitle shown under the platform name (optional)', 'smart-social-contact-panel' ); ?>">
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Sort Order', 'smart-social-contact-panel' ); ?></th>
								<td>
									<input type="number"
										   name="sscp[platforms][<?php echo esc_attr( $id ); ?>][sort_order]"
										   value="<?php echo esc_attr( $p['sort_order'] ); ?>"
										   min="1" max="99" step="1" class="small-text">
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Open in New Tab', 'smart-social-contact-panel' ); ?></th>
								<td>
									<label class="sscp-toggle sscp-toggle--sm">
										<input type="checkbox" name="sscp[platforms][<?php echo esc_attr( $id ); ?>][new_tab]" value="1" <?php checked( ! empty( $p['new_tab'] ) ); ?>>
										<span class="sscp-toggle__slider"></span>
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
			'whatsapp'  => __( 'Phone Number', 'smart-social-contact-panel' ),
			'telegram'  => __( 'Username or Phone', 'smart-social-contact-panel' ),
			'messenger' => __( 'Username / Page', 'smart-social-contact-panel' ),
			'instagram' => __( 'Username', 'smart-social-contact-panel' ),
			'facebook'  => __( 'Page URL or Username', 'smart-social-contact-panel' ),
			'twitter'   => __( 'Username', 'smart-social-contact-panel' ),
			'linkedin'  => __( 'Profile URL or Username', 'smart-social-contact-panel' ),
			'tiktok'    => __( 'Username', 'smart-social-contact-panel' ),
			'youtube'   => __( 'Channel URL or Handle', 'smart-social-contact-panel' ),
			'snapchat'  => __( 'Username', 'smart-social-contact-panel' ),
			'pinterest' => __( 'Username', 'smart-social-contact-panel' ),
			'discord'   => __( 'Invite Code or Link', 'smart-social-contact-panel' ),
			'skype'     => __( 'Skype ID', 'smart-social-contact-panel' ),
			'viber'     => __( 'Phone Number', 'smart-social-contact-panel' ),
			'line'      => __( 'Username', 'smart-social-contact-panel' ),
			'wechat'    => __( 'Page URL', 'smart-social-contact-panel' ),
			'email'     => __( 'Email Address', 'smart-social-contact-panel' ),
			'phone'     => __( 'Phone Number', 'smart-social-contact-panel' ),
			'sms'       => __( 'Phone Number', 'smart-social-contact-panel' ),
			'website'   => __( 'Website URL', 'smart-social-contact-panel' ),
		];
		return $labels[ $platform_id ] ?? __( 'Value', 'smart-social-contact-panel' );
	}

	// -------------------------------------------------------------------------
	// Tab: Design
	// -------------------------------------------------------------------------

	private function render_tab_design( $o ) {
		?>
		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Popup Dimensions', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><label for="sscp-popup-width"><?php esc_html_e( 'Popup Width (px)', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="number" id="sscp-popup-width" name="sscp[popup_width]"
							   value="<?php echo esc_attr( $o['popup_width'] ); ?>"
							   min="200" max="800" step="10" class="small-text">
						<p class="description"><?php esc_html_e( '200 – 800 px', 'smart-social-contact-panel' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="sscp-popup-border-radius"><?php esc_html_e( 'Border Radius (px)', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="number" id="sscp-popup-border-radius" name="sscp[popup_border_radius]"
							   value="<?php echo esc_attr( $o['popup_border_radius'] ); ?>"
							   min="0" max="50" step="1" class="small-text">
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Box Shadow', 'smart-social-contact-panel' ); ?></th>
					<td>
						<label class="sscp-toggle">
							<input type="checkbox" name="sscp[popup_box_shadow]" value="1" <?php checked( ! empty( $o['popup_box_shadow'] ) ); ?>>
							<span class="sscp-toggle__slider"></span>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Popup Colors', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><label for="sscp-popup-bg-color"><?php esc_html_e( 'Background Color', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="sscp-popup-bg-color" name="sscp[popup_bg_color]"
							   value="<?php echo esc_attr( $o['popup_bg_color'] ); ?>">
						<span class="sscp-color-value"><?php echo esc_html( $o['popup_bg_color'] ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="sscp-popup-text-color"><?php esc_html_e( 'Text Color', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="sscp-popup-text-color" name="sscp[popup_text_color]"
							   value="<?php echo esc_attr( $o['popup_text_color'] ); ?>">
						<span class="sscp-color-value"><?php echo esc_html( $o['popup_text_color'] ); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Layout', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><?php esc_html_e( 'Desktop Columns', 'smart-social-contact-panel' ); ?></th>
					<td>
						<?php foreach ( [ 1, 2, 3 ] as $col ) : ?>
							<label style="margin-right:16px">
								<input type="radio" name="sscp[popup_columns]" value="<?php echo esc_attr( $col ); ?>"
									   <?php checked( (int) $o['popup_columns'], $col ); ?>>
								<?php echo esc_html( $col ); ?> <?php echo esc_html( _n( 'Column', 'Columns', $col, 'smart-social-contact-panel' ) ); ?>
							</label>
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Mobile Columns', 'smart-social-contact-panel' ); ?></th>
					<td>
						<?php foreach ( [ 1, 2 ] as $col ) : ?>
							<label style="margin-right:16px">
								<input type="radio" name="sscp[mobile_columns]" value="<?php echo esc_attr( $col ); ?>"
									   <?php checked( (int) $o['mobile_columns'], $col ); ?>>
								<?php echo esc_html( $col ); ?> <?php echo esc_html( _n( 'Column', 'Columns', $col, 'smart-social-contact-panel' ) ); ?>
							</label>
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Full-Width on Mobile', 'smart-social-contact-panel' ); ?></th>
					<td>
						<label class="sscp-toggle">
							<input type="checkbox" name="sscp[mobile_full_width]" value="1" <?php checked( ! empty( $o['mobile_full_width'] ) ); ?>>
							<span class="sscp-toggle__slider"></span>
						</label>
						<p class="description"><?php esc_html_e( 'Makes the popup fill the screen width on small screens.', 'smart-social-contact-panel' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Card Styling', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><label for="sscp-card-bg-color"><?php esc_html_e( 'Card Background', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="sscp-card-bg-color" name="sscp[card_bg_color]"
							   value="<?php echo esc_attr( $o['card_bg_color'] ); ?>">
						<span class="sscp-color-value"><?php echo esc_html( $o['card_bg_color'] ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="sscp-card-hover-color"><?php esc_html_e( 'Card Hover Color', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="sscp-card-hover-color" name="sscp[card_hover_color]"
							   value="<?php echo esc_attr( $o['card_hover_color'] ); ?>">
						<span class="sscp-color-value"><?php echo esc_html( $o['card_hover_color'] ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="sscp-card-icon-color"><?php esc_html_e( 'Icon Color Override', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="color" id="sscp-card-icon-color" name="sscp[card_icon_color]"
							   value="<?php echo esc_attr( $o['card_icon_color'] ?: '#555555' ); ?>">
						<span class="sscp-color-value"><?php echo esc_html( $o['card_icon_color'] ?: 'Brand colors' ); ?></span>
						<label style="margin-left:8px">
							<input type="checkbox" id="sscp-use-brand-colors" <?php checked( empty( $o['card_icon_color'] ) ); ?>>
							<?php esc_html_e( 'Use platform brand colors', 'smart-social-contact-panel' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Uncheck to use a uniform icon color for all platforms.', 'smart-social-contact-panel' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="sscp-card-border-radius"><?php esc_html_e( 'Card Border Radius (px)', 'smart-social-contact-panel' ); ?></label></th>
					<td>
						<input type="number" id="sscp-card-border-radius" name="sscp[card_border_radius]"
							   value="<?php echo esc_attr( $o['card_border_radius'] ); ?>"
							   min="0" max="50" step="1" class="small-text">
					</td>
				</tr>
			</table>
		</div>

		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Animation', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><?php esc_html_e( 'Open Animation', 'smart-social-contact-panel' ); ?></th>
					<td>
						<?php
						$animations = [
							'none'     => __( 'None', 'smart-social-contact-panel' ),
							'fade'     => __( 'Fade', 'smart-social-contact-panel' ),
							'slide-up' => __( 'Slide Up', 'smart-social-contact-panel' ),
							'zoom'     => __( 'Zoom', 'smart-social-contact-panel' ),
						];
						foreach ( $animations as $val => $label ) :
						?>
							<label style="margin-right:16px">
								<input type="radio" name="sscp[animation_style]" value="<?php echo esc_attr( $val ); ?>"
									   <?php checked( $o['animation_style'], $val ); ?>>
								<?php echo esc_html( $label ); ?>
							</label>
						<?php endforeach; ?>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Tab: Advanced
	// -------------------------------------------------------------------------

	private function render_tab_advanced( $o ) {
		?>
		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Export / Import Settings', 'smart-social-contact-panel' ); ?></h2>
			<div class="sscp-advanced-actions">
				<div class="sscp-advanced-card">
					<h3><?php esc_html_e( 'Export Settings', 'smart-social-contact-panel' ); ?></h3>
					<p><?php esc_html_e( 'Download all plugin settings as a JSON file for backup or migration.', 'smart-social-contact-panel' ); ?></p>
					<button type="button" class="button button-secondary" id="sscp-export-btn">
						<?php esc_html_e( 'Export Settings JSON', 'smart-social-contact-panel' ); ?>
					</button>
					<textarea id="sscp-export-data" class="sscp-hidden" readonly
							  aria-label="<?php esc_attr_e( 'Settings JSON', 'smart-social-contact-panel' ); ?>"><?php echo esc_textarea( wp_json_encode( $o, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) ); ?></textarea>
				</div>

				<div class="sscp-advanced-card">
					<h3><?php esc_html_e( 'Import Settings', 'smart-social-contact-panel' ); ?></h3>
					<p><?php esc_html_e( 'Import settings from a previously exported JSON file.', 'smart-social-contact-panel' ); ?></p>
					<label class="button button-secondary" for="sscp-import-file" style="cursor:pointer">
						<?php esc_html_e( 'Choose JSON File', 'smart-social-contact-panel' ); ?>
					</label>
					<input type="file" id="sscp-import-file" accept=".json,application/json" class="sscp-hidden"
						   aria-label="<?php esc_attr_e( 'Import JSON file', 'smart-social-contact-panel' ); ?>">
					<input type="hidden" name="sscp_import_data" id="sscp-import-data">
					<p id="sscp-import-filename" class="description"></p>
					<p id="sscp-import-message" class="description" aria-live="polite"></p>
				</div>
			</div>
		</div>

		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Reset Settings', 'smart-social-contact-panel' ); ?></h2>
			<p><?php esc_html_e( 'Reset all settings back to their default values. This cannot be undone.', 'smart-social-contact-panel' ); ?></p>
			<button type="button" class="button button-link-delete" id="sscp-reset-btn">
				<?php esc_html_e( 'Reset to Defaults', 'smart-social-contact-panel' ); ?>
			</button>
		</div>

		<div class="sscp-section">
			<h2 class="sscp-section-title"><?php esc_html_e( 'Plugin Info', 'smart-social-contact-panel' ); ?></h2>
			<table class="sscp-table">
				<tr>
					<th><?php esc_html_e( 'Version', 'smart-social-contact-panel' ); ?></th>
					<td><?php echo esc_html( $this->version ); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Option Key', 'smart-social-contact-panel' ); ?></th>
					<td><code><?php echo esc_html( $this->option_key ); ?></code></td>
				</tr>
			</table>
		</div>

		<!-- Import/export uses JS — include the Save button here too for import -->
		<div class="sscp-submit-row">
			<?php submit_button( __( 'Save Imported Settings', 'smart-social-contact-panel' ), 'primary', 'submit', false ); ?>
		</div>
		<?php
	}
}
