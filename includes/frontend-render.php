<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WLTSSCP_Frontend {

	private $options;

	private static $brand_colors = [
		'whatsapp'  => '#25d366',
		'telegram'  => '#229ed9',
		'messenger' => '#0084ff',
		'instagram' => '#e1306c',
		'facebook'  => '#1877f2',
		'twitter'   => '#000000',
		'linkedin'  => '#0a66c2',
		'tiktok'    => '#010101',
		'youtube'   => '#ff0000',
		'snapchat'  => '#fffc00',
		'pinterest' => '#e60023',
		'discord'   => '#5865f2',
		'skype'     => '#00aff0',
		'viber'     => '#7360f2',
		'line'      => '#06c755',
		'wechat'    => '#07c160',
		'email'     => '#6c757d',
		'phone'     => '#28a745',
		'sms'       => '#007bff',
		'website'   => '#6c757d',
	];

	public function __construct( array $options ) {
		$this->options = $options;
	}

	public function register_hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'wp_footer', [ $this, 'render_panel' ], 100 );
	}

	public function enqueue_assets() {
		if ( empty( $this->options['enabled'] ) ) {
			return;
		}

		wp_enqueue_style(
			'wltsscp-frontend',
			WLTSSCP_URL . 'assets/css/frontend.css',
			[],
			WLTSSCP_VERSION
		);

		wp_add_inline_style( 'wltsscp-frontend', $this->get_inline_styles() );

		wp_enqueue_script(
			'wltsscp-frontend',
			WLTSSCP_URL . 'assets/js/frontend.js',
			[],
			WLTSSCP_VERSION,
			true
		);
	}

	public function render_panel() {
		if ( empty( $this->options['enabled'] ) ) {
			return;
		}

		$options      = $this->options;
		$platforms    = $this->get_enabled_platforms();
		$anim_class   = 'wltsscp-panel--anim-' . sanitize_html_class( $options['animation_style'] );
		$shadow_class = ! empty( $options['popup_box_shadow'] ) ? 'wltsscp-panel--shadow' : '';
		$mobile_fw    = ! empty( $options['mobile_full_width'] ) ? '1' : '0';

		echo wp_kses( $this->render_trigger( $options ), $this->get_allowed_trigger_html() );
		echo '<div id="wltsscp-overlay" class="wltsscp-overlay" aria-hidden="true"></div>' . "\n";
		echo '<div id="wltsscp-panel" class="wltsscp-panel ' . esc_attr( trim( $anim_class . ' ' . $shadow_class ) ) . '" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="wltsscp-panel-title" data-mobile-fullwidth="' . esc_attr( $mobile_fw ) . '">' . "\n";
		echo wp_kses( $this->render_panel_header( $options ), $this->get_allowed_panel_header_html() );

		if ( ! empty( $platforms ) ) {
			echo wp_kses( $this->render_grid( $platforms, $options ), $this->get_allowed_grid_html() );
		} else {
			echo '<p class="wltsscp-panel__empty">' . esc_html__( 'No contact methods have been configured yet.', 'smart-social-contact-panel' ) . '</p>' . "\n";
		}

		if ( ! empty( $options['show_branding'] ) ) {
			echo '<p class="wltsscp-panel__branding">' . esc_html__( 'Powered by Smart Social Contact Panel', 'smart-social-contact-panel' ) . '</p>' . "\n";
		}

		echo '</div>' . "\n";
	}

	private function get_inline_styles() {
		$options    = $this->options;
		$icon_color = ! empty( $options['card_icon_color'] ) ? sanitize_hex_color( $options['card_icon_color'] ) : 'currentColor';

		return sprintf(
			':root{--wltsscp-btn-bg:%1$s;--wltsscp-btn-color:%2$s;--wltsscp-popup-bg:%3$s;--wltsscp-popup-color:%4$s;--wltsscp-popup-width:%5$dpx;--wltsscp-popup-radius:%6$dpx;--wltsscp-card-bg:%7$s;--wltsscp-card-hover:%8$s;--wltsscp-card-icon:%9$s;--wltsscp-card-radius:%10$dpx;--wltsscp-mobile-cols:%11$d;}',
			sanitize_hex_color( $options['button_color'] ),
			sanitize_hex_color( $options['button_text_color'] ),
			sanitize_hex_color( $options['popup_bg_color'] ),
			sanitize_hex_color( $options['popup_text_color'] ),
			(int) $options['popup_width'],
			(int) $options['popup_border_radius'],
			sanitize_hex_color( $options['card_bg_color'] ),
			sanitize_hex_color( $options['card_hover_color'] ),
			$icon_color,
			(int) $options['card_border_radius'],
			(int) $options['mobile_columns']
		);
	}

	private function get_allowed_svg_html() {
		return [
			'svg'      => [
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewBox'         => true,
				'fill'            => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'aria-hidden'     => true,
				'focusable'       => true,
				'role'            => true,
				'class'           => true,
			],
			'path'     => [
				'd'                => true,
				'fill'             => true,
				'stroke'           => true,
				'stroke-width'     => true,
				'stroke-linecap'   => true,
				'stroke-linejoin'  => true,
			],
			'circle'   => [
				'cx'               => true,
				'cy'               => true,
				'r'                => true,
				'fill'             => true,
				'stroke'           => true,
				'stroke-width'     => true,
			],
			'line'     => [
				'x1'               => true,
				'y1'               => true,
				'x2'               => true,
				'y2'               => true,
				'stroke'           => true,
				'stroke-width'     => true,
				'stroke-linecap'   => true,
			],
			'polyline' => [
				'points'           => true,
				'fill'             => true,
				'stroke'           => true,
				'stroke-width'     => true,
				'stroke-linecap'   => true,
				'stroke-linejoin'  => true,
			],
			'rect'     => [
				'x'                => true,
				'y'                => true,
				'width'            => true,
				'height'           => true,
				'rx'               => true,
				'fill'             => true,
				'stroke'           => true,
				'stroke-width'     => true,
			],
		];
	}

	private function get_allowed_trigger_html() {
		return array_merge(
			$this->get_allowed_svg_html(),
			[
				'div'    => [ 'class' => true ],
				'button' => [
					'id'            => true,
					'class'         => true,
					'aria-label'    => true,
					'aria-expanded' => true,
					'aria-controls' => true,
				],
				'span'   => [
					'class'       => true,
					'aria-hidden' => true,
				],
			]
		);
	}

	private function get_allowed_panel_header_html() {
		return array_merge(
			$this->get_allowed_svg_html(),
			[
				'div'    => [ 'class' => true ],
				'h2'     => [
					'id'    => true,
					'class' => true,
				],
				'button' => [
					'id'         => true,
					'class'      => true,
					'aria-label' => true,
				],
				'p'      => [ 'class' => true ],
			]
		);
	}

	private function get_allowed_grid_html() {
		return array_merge(
			$this->get_allowed_svg_html(),
			[
				'div'  => [ 'class' => true ],
				'a'    => [
					'href'       => true,
					'class'      => true,
					'target'     => true,
					'rel'        => true,
					'aria-label' => true,
					'style'      => true,
				],
				'span' => [
					'class'       => true,
					'aria-hidden' => true,
				],
			]
		);
	}

	private function render_trigger( $options ) {
		$position_class = 'bottom-right' === $options['button_position']
			? 'wltsscp-trigger--bottom-right'
			: 'wltsscp-trigger--bottom-left';

		$side_text = '';
		if ( ! empty( $options['button_side_text'] ) ) {
			$side_text = '<span class="wltsscp-trigger-wrap__side-text">' . esc_html( $options['button_side_text'] ) . '</span>';
		}

		return sprintf(
			'<div class="wltsscp-trigger-wrap %1$s">%2$s<button id="wltsscp-trigger" class="wltsscp-trigger %3$s" aria-label="%4$s" aria-expanded="false" aria-controls="wltsscp-panel"><span class="wltsscp-trigger__icon" aria-hidden="true">%5$s</span><span class="wltsscp-trigger__text">%6$s</span></button></div>' . "\n",
			esc_attr( str_replace( 'wltsscp-trigger--', 'wltsscp-trigger-wrap--', $position_class ) ),
			$side_text,
			esc_attr( $position_class ),
			esc_attr( $options['button_text'] ?: __( 'Contact Us', 'smart-social-contact-panel' ) ),
			self::get_svg( 'chat' ),
			esc_html( $options['button_text'] )
		);
	}

	private function render_panel_header( $options ) {
		$output  = '<div class="wltsscp-panel__header">' . "\n";
		$output .= '<h2 id="wltsscp-panel-title" class="wltsscp-panel__title">' . esc_html( $options['popup_title'] ) . '</h2>' . "\n";
		$output .= '<button id="wltsscp-close" class="wltsscp-panel__close" aria-label="' . esc_attr__( 'Close contact panel', 'smart-social-contact-panel' ) . '">' . self::get_svg( 'close' ) . '</button>' . "\n";
		$output .= '</div>' . "\n";

		if ( ! empty( $options['popup_description'] ) ) {
			$output .= '<div class="wltsscp-panel__description">' . wp_kses_post( wpautop( $options['popup_description'] ) ) . '</div>' . "\n";
		}

		return $output;
	}

	private function render_grid( array $platforms, array $options ) {
		$grid_class = 'wltsscp-grid wltsscp-grid--col-' . (int) $options['popup_columns'];
		$output     = '<div class="' . esc_attr( $grid_class ) . '">' . "\n";

		foreach ( $platforms as $id => $platform ) {
			$output .= $this->render_card( $id, $platform );
		}

		$output .= '</div>' . "\n";

		return $output;
	}

	private function render_card( $id, array $platform ) {
		$url         = WLTSSCP_Helpers::build_platform_url( $id, $platform['value'], $platform );
		$target      = ! empty( $platform['new_tab'] ) ? '_blank' : '_self';
		$rel         = ! empty( $platform['new_tab'] ) ? ' rel="noopener noreferrer"' : '';
		$brand_color = self::$brand_colors[ $id ] ?? '#555555';
		$icon_style  = '';

		if ( empty( $this->options['card_icon_color'] ) ) {
			$icon_style = ' style="--wltsscp-card-icon:' . esc_attr( $brand_color ) . '"';
		}

		$output  = sprintf(
			'<a href="%1$s" class="wltsscp-card" target="%2$s"%3$s aria-label="%4$s"%5$s>',
			esc_url( $url ),
			esc_attr( $target ),
			$rel,
			esc_attr( $platform['label'] ),
			$icon_style
		);
		$output .= '<span class="wltsscp-card__icon" aria-hidden="true">' . self::get_svg( $id ) . '</span>';
		$output .= '<span class="wltsscp-card__content">';
		$output .= '<span class="wltsscp-card__label">' . esc_html( $platform['label'] ) . '</span>';

		if ( ! empty( $platform['description'] ) ) {
			$output .= '<span class="wltsscp-card__description">' . esc_html( $platform['description'] ) . '</span>';
		}

		$output .= '</span>';
		$output .= '</a>' . "\n";

		return $output;
	}

	private function get_enabled_platforms() {
		$platforms = $this->options['platforms'] ?? [];
		$enabled   = array_filter(
			$platforms,
			function ( $platform ) {
				return ! empty( $platform['enabled'] ) && ! empty( $platform['value'] );
			}
		);

		uasort(
			$enabled,
			function ( $a, $b ) {
				return ( (int) $a['sort_order'] ) <=> ( (int) $b['sort_order'] );
			}
		);

		return $enabled;
	}

	public static function get_svg( $id ) {
		$chat = '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
		$close = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
		$globe = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="var(--wltsscp-card-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>';
		$phone = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="var(--wltsscp-card-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.68 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.59 1.2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6.13 6.13l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
		$mail = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="var(--wltsscp-card-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
		$sms = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="var(--wltsscp-card-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="9" x2="15" y1="10" y2="10"/><line x1="12" x2="12" y1="7" y2="13"/></svg>';

		$map = [
			'chat'      => $chat,
			'close'     => $close,
			'email'     => $mail,
			'phone'     => $phone,
			'sms'       => $sms,
			'website'   => $globe,
			'facebook'  => $globe,
			'instagram' => $globe,
			'linkedin'  => $globe,
			'twitter'   => $globe,
			'whatsapp'  => $chat,
			'telegram'  => $chat,
			'messenger' => $chat,
			'tiktok'    => $globe,
			'youtube'   => $globe,
			'snapchat'  => $globe,
			'pinterest' => $globe,
			'discord'   => $chat,
			'skype'     => $phone,
			'viber'     => $phone,
			'line'      => $chat,
			'wechat'    => $chat,
		];

		return $map[ $id ] ?? $globe;
	}
}
