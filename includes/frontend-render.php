<?php
/**
 * SSCP_Frontend — enqueues frontend assets and renders the floating button + popup.
 *
 * All HTML is generated server-side and fully escaped. JavaScript only adds/removes
 * CSS classes — it never writes user-controlled data into the DOM via innerHTML.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class SSCP_Frontend {

	/** @var array Plugin options (merged with defaults) */
	private $options;

	/** Brand hex colors used when admin has not set a custom icon color. */
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
		add_action( 'wp_footer',          [ $this, 'render_panel' ], 100 );
	}

	// -------------------------------------------------------------------------
	// Asset enqueue — only when plugin is enabled
	// -------------------------------------------------------------------------

	public function enqueue_assets() {
		if ( empty( $this->options['enabled'] ) ) return;

		wp_enqueue_style(
			'sscp-frontend',
			SSCP_URL . 'assets/css/frontend.css',
			[],
			SSCP_VERSION
		);

		wp_enqueue_script(
			'sscp-frontend',
			SSCP_URL . 'assets/js/frontend.js',
			[],
			SSCP_VERSION,
			true  // load in footer
		);
	}

	// -------------------------------------------------------------------------
	// Main output
	// -------------------------------------------------------------------------

	public function render_panel() {
		if ( empty( $this->options['enabled'] ) ) return;

		$platforms = $this->get_enabled_platforms();

		// Always render the button even when no platforms are enabled,
		// but skip the panel body if there's nothing to show.
		$o = $this->options;

		// Inline CSS variable overrides
		$anim_class   = 'sscp-panel--anim-' . sanitize_html_class( $o['animation_style'] );
		$shadow_class = ! empty( $o['popup_box_shadow'] ) ? 'sscp-panel--shadow' : '';

		echo "\n<!-- Smart Social Contact Panel -->\n";

		// Inline style overrides (CSS custom properties)
		$this->render_inline_styles( $o );

		// Floating trigger button
		echo wp_kses( $this->render_trigger( $o ), $this->get_allowed_trigger_html() );

		// Backdrop overlay
		echo '<div id="sscp-overlay" class="sscp-overlay" aria-hidden="true"></div>' . "\n";

		// Popup panel
		$mobile_fw = ! empty( $o['mobile_full_width'] ) ? '1' : '0';

		echo '<div id="sscp-panel" class="sscp-panel ' . esc_attr( $anim_class . ' ' . $shadow_class ) . '" '
			. 'role="dialog" aria-modal="true" aria-hidden="true" '
			. 'aria-labelledby="sscp-panel-title" '
			. 'data-mobile-fullwidth="' . esc_attr( $mobile_fw ) . '">' . "\n";

		echo wp_kses( $this->render_panel_header( $o ), $this->get_allowed_panel_header_html() );

		if ( ! empty( $platforms ) ) {
			echo wp_kses( $this->render_grid( $platforms, $o ), $this->get_allowed_grid_html() );
		} else {
			echo '<p class="sscp-panel__empty">' . esc_html__( 'No contact methods have been configured yet.', 'smart-social-contact-panel' ) . '</p>' . "\n";
		}

		if ( ! empty( $o['show_branding'] ) ) {
			echo '<p class="sscp-panel__branding">'
				. esc_html__( 'Powered by Smart Social Contact Panel', 'smart-social-contact-panel' )
				. '</p>' . "\n";
		}

		echo '</div><!-- /#sscp-panel -->' . "\n";
	}

	// -------------------------------------------------------------------------
	// Inline CSS variables
	// -------------------------------------------------------------------------

	private function render_inline_styles( $o ) {
		$icon_color = ! empty( $o['card_icon_color'] ) ? esc_attr( $o['card_icon_color'] ) : 'currentColor';

		printf(
			'<style id="sscp-vars">
:root{
--sscp-btn-bg:%s;
--sscp-btn-color:%s;
--sscp-popup-bg:%s;
--sscp-popup-color:%s;
--sscp-popup-width:%dpx;
--sscp-popup-radius:%dpx;
--sscp-card-bg:%s;
--sscp-card-hover:%s;
--sscp-card-icon:%s;
--sscp-card-radius:%dpx;
--sscp-mobile-cols:%d;
}
</style>' . "\n",
			esc_attr( $o['button_color'] ),
			esc_attr( $o['button_text_color'] ),
			esc_attr( $o['popup_bg_color'] ),
			esc_attr( $o['popup_text_color'] ),
			(int) $o['popup_width'],
			(int) $o['popup_border_radius'],
			esc_attr( $o['card_bg_color'] ),
			esc_attr( $o['card_hover_color'] ),
			esc_attr( $icon_color ),
			(int) $o['card_border_radius'],
			(int) $o['mobile_columns']
		);
	}

	private function get_allowed_svg_html() {
		return [
			'svg'  => [
				'xmlns'       => true,
				'width'       => true,
				'height'      => true,
				'viewbox'     => true,
				'viewBox'     => true,
				'fill'        => true,
				'stroke'      => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
				'aria-hidden' => true,
				'focusable'   => true,
				'role'        => true,
				'class'       => true,
			],
			'path' => [
				'd'           => true,
				'fill'        => true,
				'stroke'      => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			],
			'circle' => [
				'cx'          => true,
				'cy'          => true,
				'r'           => true,
				'fill'        => true,
				'stroke'      => true,
				'stroke-width' => true,
			],
			'line' => [
				'x1'          => true,
				'y1'          => true,
				'x2'          => true,
				'y2'          => true,
				'stroke'      => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
			],
			'polyline' => [
				'points'      => true,
				'fill'        => true,
				'stroke'      => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			],
			'rect' => [
				'x'           => true,
				'y'           => true,
				'width'       => true,
				'height'      => true,
				'rx'          => true,
				'fill'        => true,
				'stroke'      => true,
				'stroke-width' => true,
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

	// -------------------------------------------------------------------------
	// Floating trigger button
	// -------------------------------------------------------------------------

	private function render_trigger( $o ) {
		$position_class = 'bottom-right' === $o['button_position']
			? 'sscp-trigger--bottom-right'
			: 'sscp-trigger--bottom-left';

		$svg = self::get_svg( 'chat' );

		$side_text = '';
		if ( ! empty( $o['button_side_text'] ) ) {
			$side_text = '<span class="sscp-trigger-wrap__side-text">' . esc_html( $o['button_side_text'] ) . '</span>';
		}

		return sprintf(
			'<div class="sscp-trigger-wrap %s">'
				. '%s'
				. '<button id="sscp-trigger" class="sscp-trigger %s" '
				. 'aria-label="%s" aria-expanded="false" aria-controls="sscp-panel">'
				. '<span class="sscp-trigger__icon" aria-hidden="true">%s</span>'
				. '<span class="sscp-trigger__text">%s</span>'
				. '</button>'
				. '</div>' . "\n",
			esc_attr( str_replace( 'sscp-trigger--', 'sscp-trigger-wrap--', $position_class ) ),
			$side_text,
			esc_attr( $position_class ),
			esc_attr( $o['button_text'] ?: __( 'Contact Us', 'smart-social-contact-panel' ) ),
			$svg, // hardcoded SVG, safe
			esc_html( $o['button_text'] )
		);
	}

	// -------------------------------------------------------------------------
	// Popup header
	// -------------------------------------------------------------------------

	private function render_panel_header( $o ) {
		$close_svg = self::get_svg( 'close' );
		$out  = '<div class="sscp-panel__header">' . "\n";
		$out .= '<h2 id="sscp-panel-title" class="sscp-panel__title">' . esc_html( $o['popup_title'] ) . '</h2>' . "\n";
		$out .= '<button id="sscp-close" class="sscp-panel__close" aria-label="' . esc_attr__( 'Close contact panel', 'smart-social-contact-panel' ) . '">'
			. $close_svg
			. '</button>' . "\n";
		$out .= '</div>' . "\n";

		if ( ! empty( $o['popup_description'] ) ) {
			$out .= '<div class="sscp-panel__description">' . wp_kses_post( wpautop( $o['popup_description'] ) ) . '</div>' . "\n";
		}

		return $out;
	}

	// -------------------------------------------------------------------------
	// Platform grid
	// -------------------------------------------------------------------------

	private function render_grid( $platforms, $o ) {
		$cols       = (int) $o['popup_columns'];
		$grid_class = 'sscp-grid sscp-grid--col-' . $cols;

		$out = '<div class="' . esc_attr( $grid_class ) . '">' . "\n";
		foreach ( $platforms as $id => $p ) {
			$out .= $this->render_card( $id, $p );
		}
		$out .= '</div>' . "\n";
		return $out;
	}

	// -------------------------------------------------------------------------
	// Single platform card
	// -------------------------------------------------------------------------

	private function render_card( $id, $p ) {
		$url        = SSCP_Helpers::build_platform_url( $id, $p['value'], $p );
		$target     = ! empty( $p['new_tab'] ) ? '_blank' : '_self';
		$rel        = ! empty( $p['new_tab'] ) ? ' rel="noopener noreferrer"' : '';
		$brand_color = self::$brand_colors[ $id ] ?? '#555';
		$svg        = self::get_svg( $id );

		$icon_style = '';
		if ( empty( $this->options['card_icon_color'] ) ) {
			// Use brand color as CSS variable so the stylesheet can apply it
			$icon_style = ' style="--sscp-card-icon:' . esc_attr( $brand_color ) . '"';
		}

		$out = sprintf(
			'<a href="%s" class="sscp-card" target="%s"%s aria-label="%s"%s>',
			esc_url( $url ),
			esc_attr( $target ),
			$rel,
			esc_attr( $p['label'] ),
			$icon_style
		);

		$out .= '<span class="sscp-card__icon" aria-hidden="true">' . $svg . '</span>';
		$out .= '<span class="sscp-card__content">';
		$out .= '<span class="sscp-card__label">' . esc_html( $p['label'] ) . '</span>';
		if ( ! empty( $p['description'] ) ) {
			$out .= '<span class="sscp-card__description">' . esc_html( $p['description'] ) . '</span>';
		}
		$out .= '</span>';
		$out .= '</a>' . "\n";

		return $out;
	}

	// -------------------------------------------------------------------------
	// Enabled + sorted platforms
	// -------------------------------------------------------------------------

	private function get_enabled_platforms() {
		$platforms = $this->options['platforms'] ?? [];
		$enabled   = array_filter( $platforms, function( $p ) {
			return ! empty( $p['enabled'] ) && ! empty( $p['value'] );
		} );

		uasort( $enabled, function( $a, $b ) {
			return ( (int) $a['sort_order'] ) <=> ( (int) $b['sort_order'] );
		} );

		return $enabled;
	}

	// -------------------------------------------------------------------------
	// SVG icon map — all hardcoded, no external requests
	// -------------------------------------------------------------------------

	public static function get_svg( $id ) {
		static $map = null;
		if ( null === $map ) {
			$map = self::build_svg_map();
		}
		return $map[ $id ] ?? $map['website'];
	}

	private static function build_svg_map() {
		return [

			// UI icons
			'close' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
			'chat'  => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',

			// WhatsApp
			'whatsapp' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>',

			// Telegram
			'telegram' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',

			// Messenger
			'messenger' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.614 4.469 8.654V24l4.088-2.242c1.092.3 2.246.464 3.443.464 6.627 0 12-4.975 12-11.111C24 4.975 18.627 0 12 0zm1.191 14.963-3.055-3.26-5.963 3.26L10.732 8.6l3.131 3.26L19.752 8.6l-6.561 6.363z"/></svg>',

			// Instagram
			'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',

			// Facebook
			'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',

			// X / Twitter
			'twitter' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622 5.91-5.622Zm-1.161 17.52h1.833L7.084 4.126H5.117Z"/></svg>',

			// LinkedIn
			'linkedin' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',

			// TikTok
			'tiktok' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',

			// YouTube
			'youtube' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',

			// Snapchat
			'snapchat' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M12.065.003C9.063-.035 6.228 1.44 4.76 3.896c-.605 1.034-.783 2.203-.76 3.392.034 1.607.033 3.213.014 4.818-.004.27-.077.543-.19.789-.246.53-.726.87-1.218 1.147-.352.198-.726.36-1.076.564-.167.097-.334.213-.434.38-.128.212-.109.47.034.674.198.283.533.413.842.518.296.1.608.171.886.305.534.254.69.68.644 1.23-.011.133-.026.268-.049.4-.064.372.17.6.524.545.45-.07.895-.213 1.34-.26.393-.042.785-.031 1.16.086.586.183 1.07.57 1.532.976.672.588 1.287 1.236 2.053 1.677.762.44 1.645.62 2.509.607.864.013 1.747-.167 2.509-.607.766-.441 1.381-1.089 2.053-1.677.462-.406.946-.793 1.532-.976.375-.117.767-.128 1.16-.086.445.047.89.19 1.34.26.354.055.588-.173.524-.545a4.624 4.624 0 0 1-.05-.4c-.045-.55.11-.976.645-1.23.278-.134.59-.205.886-.305.309-.105.644-.235.842-.518.143-.204.162-.462.034-.674-.1-.167-.267-.283-.434-.38-.35-.204-.724-.366-1.076-.564-.492-.277-.972-.617-1.218-1.147-.113-.246-.186-.519-.19-.789-.019-1.605-.02-3.21.014-4.818.023-1.189-.155-2.358-.76-3.392C17.772 1.44 14.937-.035 11.935.003h.13z"/></svg>',

			// Pinterest
			'pinterest' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>',

			// Discord
			'discord' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418Z"/></svg>',

			// Skype
			'skype' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M12.069 18.874c-4.023 0-5.82-1.979-5.82-3.464 0-.765.561-1.296 1.333-1.296 1.723 0 1.273 2.477 4.487 2.477 1.641 0 2.55-.895 2.55-1.811 0-.551-.269-1.16-1.354-1.421l-3.576-.895c-2.88-.72-3.403-2.286-3.403-3.751 0-3.047 2.861-4.191 5.549-4.191 2.471 0 5.393 1.373 5.393 3.199 0 .784-.688 1.24-1.453 1.24-1.469 0-1.198-2.037-4.164-2.037-1.469 0-2.292.664-2.292 1.617s1.153 1.258 2.157 1.504l2.637.637c2.9.7 3.605 2.346 3.605 3.965 0 2.71-2.098 4.227-5.649 4.227m11.084-4.882c.209 1.045.314 2.11.314 3.165 0 2.645-.857 4.906-2.574 6.728-1.7 1.858-3.905 2.115-6.585 2.115-1.043 0-2.062-.127-3.075-.368A6.76 6.76 0 0 1 7.48 24C5.44 24 3.566 23.292 2.12 21.851.677 20.408 0 18.545 0 16.514a9.61 9.61 0 0 1 .878-4.052 11.994 11.994 0 0 1-.406-3.091C.472 6.886 1.316 4.646 3.034 2.825 4.735.955 6.938.5 9.628.5c1.052 0 2.078.142 3.085.422A6.742 6.742 0 0 1 16.52 0c2.043 0 3.918.708 5.36 2.15 1.44 1.443 2.12 3.306 2.12 5.337a9.732 9.732 0 0 1-.847 3.505z"/></svg>',

			// Viber
			'viber' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M11.4 0C5.93 0 1.5 4.177 1.5 9.322c0 2.988 1.504 5.758 4.06 7.537v3.403l2.893-1.818c.943.257 1.938.396 2.947.396 5.47 0 9.9-4.177 9.9-9.322C21.3 4.177 16.87 0 11.4 0zM8.168 12.46l-.654-.693c-.855-.906-1.289-1.86-1.289-2.832 0-1.793 1.404-3.248 3.131-3.248.81 0 1.59.322 2.17.91.58.587.9 1.372.9 2.197 0 .153-.012.305-.038.452l-1.029-.122a1.84 1.84 0 0 0 .025-.33c0-.555-.21-1.075-.59-1.46a1.963 1.963 0 0 0-1.438-.607c-1.115 0-2.023.936-2.023 2.208 0 .657.295 1.331.9 2.003l.693.733-1.758 1.789zm5.83 2.58l-1.45-1.537-.005-.005c-.367.14-.746.213-1.143.213-.376 0-.744-.067-1.098-.197l-.99 1.009-.048-.048C7.95 13.9 7.128 12.428 7.128 10.935c0-.938.352-1.79.994-2.44.64-.65 1.484-1.008 2.378-1.008s1.738.358 2.378 1.009c.643.65.994 1.502.994 2.44 0 1.494-.822 2.966-2.134 4.075l.26.03zm.695-3.3a1.876 1.876 0 0 0 .025-.33c0-.555-.21-1.075-.59-1.46a1.963 1.963 0 0 0-1.438-.607l-.044.001.994 1.053c.142.383.213.783.213 1.19 0 .153-.012.305-.038.452l.878.701z"/></svg>',

			// Line
			'line' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.105.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.281.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.070 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>',

			// WeChat
			'wechat' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="var(--sscp-card-icon)" aria-hidden="true"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 0 1 .213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.295.295a.328.328 0 0 0 .166-.054l1.9-1.106a.6.6 0 0 1 .34-.105c.068 0 .135.014.2.037.654.204 1.348.327 2.07.341l-.03-.344C7.574 16.32 7.5 15.96 7.5 15.585c0-3.673 3.46-6.648 7.724-6.648.12 0 .238.003.357.008C14.98 5.73 12.25 2.188 8.691 2.188zM5.97 6.24c.493 0 .895.398.895.89a.895.895 0 0 1-1.788 0 .894.894 0 0 1 .893-.89zm5.344 0c.49 0 .895.398.895.89a.895.895 0 0 1-1.788 0 .893.893 0 0 1 .893-.89zM24 15.585c0-3.326-3.12-6.024-6.973-6.024-3.851 0-6.974 2.698-6.974 6.024S13.175 21.61 17.027 21.61c.735 0 1.44-.104 2.11-.29a.468.468 0 0 1 .258.02l1.49.867a.255.255 0 0 0 .135.045.22.22 0 0 0 .223-.224c0-.055-.02-.11-.037-.165l-.302-1.152a.478.478 0 0 1 .172-.527C22.897 19.52 24 17.643 24 15.585zm-9.09-1.066a.7.7 0 0 1 0-1.4.7.7 0 0 1 0 1.4zm4.22 0a.7.7 0 0 1 0-1.4.7.7 0 0 1 0 1.4z"/></svg>',

			// Email
			'email' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="var(--sscp-card-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>',

			// Phone
			'phone' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="var(--sscp-card-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.68 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.59 1.2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6.13 6.13l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',

			// SMS
			'sms' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="var(--sscp-card-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="9" x2="15" y1="10" y2="10"/><line x1="12" x2="12" y1="7" y2="13"/></svg>',

			// Website / Globe
			'website' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="var(--sscp-card-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
		];
	}
}
