<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WLTSSCP_Helpers {

	public static function sanitize_hex_color( $color ) {
		$color = trim( $color );

		if ( preg_match( '/^#[0-9A-Fa-f]{6}$/', $color ) ) {
			return strtolower( $color );
		}

		return '';
	}

	public static function sanitize_url( $url ) {
		$url    = esc_url_raw( trim( $url ) );
		$scheme = wp_parse_url( $url, PHP_URL_SCHEME );

		if ( ! in_array( $scheme, [ 'https', 'http' ], true ) ) {
			return '';
		}

		return $url;
	}

	public static function sanitize_email_field( $email ) {
		return sanitize_email( trim( $email ) );
	}

	public static function sanitize_phone( $phone ) {
		return preg_replace( '/[^+\d\s\-()]/', '', trim( $phone ) );
	}

	public static function sanitize_text( $text ) {
		return sanitize_text_field( $text );
	}

	public static function sanitize_textarea( $text ) {
		return sanitize_textarea_field( $text );
	}

	public static function sanitize_integer( $val, $min, $max, $default ) {
		$val = (int) $val;

		if ( $val < $min || $val > $max ) {
			return $default;
		}

		return $val;
	}

	public static function sanitize_boolean( $val ) {
		return (bool) $val;
	}

	public static function sanitize_select( $val, array $allowed, $default ) {
		return in_array( $val, $allowed, true ) ? $val : $default;
	}

	public static function sanitize_sort_order( $val ) {
		return self::sanitize_integer( $val, 1, 99, 10 );
	}

	public static function build_whatsapp_url( $phone, $message = '' ) {
		$phone = preg_replace( '/[^+\d]/', '', trim( $phone ) );

		if ( empty( $phone ) ) {
			return '';
		}

		$url     = 'https://wa.me/' . ltrim( $phone, '+' );
		$message = trim( $message );

		if ( '' !== $message ) {
			$url = add_query_arg( 'text', $message, $url );
		}

		return $url;
	}

	public static function build_telegram_url( $value ) {
		$value = trim( $value );

		if ( strpos( $value, '+' ) === 0 || ctype_digit( ltrim( $value, '+' ) ) ) {
			$phone = preg_replace( '/[^+\d]/', '', $value );

			return empty( $phone ) ? '' : 'https://t.me/' . $phone;
		}

		$username = preg_replace( '/[^a-zA-Z0-9_]/', '', ltrim( $value, '@' ) );

		return empty( $username ) ? '' : 'https://t.me/' . $username;
	}

	public static function build_messenger_url( $username ) {
		$username = preg_replace( '/[^a-zA-Z0-9._\-]/', '', trim( $username ) );

		return empty( $username ) ? '' : 'https://m.me/' . $username;
	}

	public static function build_instagram_url( $username ) {
		$username = preg_replace( '/[^a-zA-Z0-9._]/', '', ltrim( trim( $username ), '@' ) );

		return empty( $username ) ? '' : 'https://instagram.com/' . $username;
	}

	public static function build_facebook_url( $value ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return self::sanitize_url( $value );
		}

		$page = preg_replace( '/[^a-zA-Z0-9._\-]/', '', trim( $value ) );

		return empty( $page ) ? '' : 'https://facebook.com/' . $page;
	}

	public static function build_twitter_url( $username ) {
		$username = preg_replace( '/[^a-zA-Z0-9_]/', '', ltrim( trim( $username ), '@' ) );

		return empty( $username ) ? '' : 'https://x.com/' . $username;
	}

	public static function build_linkedin_url( $value ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return self::sanitize_url( $value );
		}

		$slug = preg_replace( '/[^a-zA-Z0-9\-_]/', '', trim( $value ) );

		return empty( $slug ) ? '' : 'https://linkedin.com/in/' . $slug;
	}

	public static function build_tiktok_url( $username ) {
		$username = preg_replace( '/[^a-zA-Z0-9._]/', '', ltrim( trim( $username ), '@' ) );

		return empty( $username ) ? '' : 'https://tiktok.com/@' . $username;
	}

	public static function build_youtube_url( $value ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return self::sanitize_url( $value );
		}

		$channel = preg_replace( '/[^a-zA-Z0-9_\-]/', '', trim( $value ) );

		return empty( $channel ) ? '' : 'https://youtube.com/@' . $channel;
	}

	public static function build_snapchat_url( $username ) {
		$username = preg_replace( '/[^a-zA-Z0-9._\-]/', '', trim( $username ) );

		return empty( $username ) ? '' : 'https://snapchat.com/add/' . $username;
	}

	public static function build_pinterest_url( $username ) {
		$username = preg_replace( '/[^a-zA-Z0-9_]/', '', trim( $username ) );

		return empty( $username ) ? '' : 'https://pinterest.com/' . $username;
	}

	public static function build_discord_url( $value ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return self::sanitize_url( $value );
		}

		$code = preg_replace( '/[^a-zA-Z0-9\-]/', '', trim( $value ) );

		return empty( $code ) ? '' : 'https://discord.gg/' . $code;
	}

	public static function build_skype_url( $username ) {
		$username = preg_replace( '/[^a-zA-Z0-9._\-:]/', '', trim( $username ) );

		return empty( $username ) ? '' : 'skype:' . $username . '?chat';
	}

	public static function build_viber_url( $phone ) {
		$phone = preg_replace( '/[^+\d]/', '', trim( $phone ) );

		if ( empty( $phone ) ) {
			return '';
		}

		return 'viber://chat?number=%2B' . ltrim( $phone, '+' );
	}

	public static function build_line_url( $username ) {
		$username = preg_replace( '/[^a-zA-Z0-9._\-]/', '', trim( $username ) );

		return empty( $username ) ? '' : 'https://line.me/ti/p/~' . $username;
	}

	public static function build_wechat_url( $value ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return self::sanitize_url( $value );
		}

		return '';
	}

	public static function build_mailto_url( $email ) {
		$email = sanitize_email( trim( $email ) );

		return empty( $email ) ? '' : 'mailto:' . $email;
	}

	public static function build_tel_url( $phone ) {
		$phone = preg_replace( '/[^+\d]/', '', trim( $phone ) );

		return empty( $phone ) ? '' : 'tel:' . $phone;
	}

	public static function build_sms_url( $phone ) {
		$phone = preg_replace( '/[^+\d]/', '', trim( $phone ) );

		return empty( $phone ) ? '' : 'sms:' . $phone;
	}

	public static function build_generic_url( $url ) {
		return self::sanitize_url( $url );
	}

	public static function build_platform_url( $platform_id, $value, array $platform = [] ) {
		switch ( $platform_id ) {
			case 'whatsapp':
				return self::build_whatsapp_url( $value, $platform['message'] ?? '' );

			case 'telegram':
				return self::build_telegram_url( $value );

			case 'messenger':
				return self::build_messenger_url( $value );

			case 'instagram':
				return self::build_instagram_url( $value );

			case 'facebook':
				return self::build_facebook_url( $value );

			case 'twitter':
				return self::build_twitter_url( $value );

			case 'linkedin':
				return self::build_linkedin_url( $value );

			case 'tiktok':
				return self::build_tiktok_url( $value );

			case 'youtube':
				return self::build_youtube_url( $value );

			case 'snapchat':
				return self::build_snapchat_url( $value );

			case 'pinterest':
				return self::build_pinterest_url( $value );

			case 'discord':
				return self::build_discord_url( $value );

			case 'skype':
				return self::build_skype_url( $value );

			case 'viber':
				return self::build_viber_url( $value );

			case 'line':
				return self::build_line_url( $value );

			case 'wechat':
				return self::build_wechat_url( $value );

			case 'email':
				return self::build_mailto_url( $value );

			case 'phone':
				return self::build_tel_url( $value );

			case 'sms':
				return self::build_sms_url( $value );

			case 'website':
				return self::build_generic_url( $value );

			default:
				return self::build_generic_url( $value );
		}
	}

	public static function get_value_placeholder( $platform_id ) {
		$placeholders = [
			'whatsapp'  => __( 'Phone number with country code, e.g. +1234567890', 'smart-social-contact-panel' ),
			'telegram'  => __( 'Username (e.g. @yourname) or phone number', 'smart-social-contact-panel' ),
			'messenger' => __( 'Facebook username or page name', 'smart-social-contact-panel' ),
			'instagram' => __( 'Instagram username, e.g. @yourname', 'smart-social-contact-panel' ),
			'facebook'  => __( 'Facebook page URL or username', 'smart-social-contact-panel' ),
			'twitter'   => __( 'X/Twitter username, e.g. @yourname', 'smart-social-contact-panel' ),
			'linkedin'  => __( 'LinkedIn profile URL or username', 'smart-social-contact-panel' ),
			'tiktok'    => __( 'TikTok username, e.g. @yourname', 'smart-social-contact-panel' ),
			'youtube'   => __( 'YouTube channel URL or handle', 'smart-social-contact-panel' ),
			'snapchat'  => __( 'Snapchat username', 'smart-social-contact-panel' ),
			'pinterest' => __( 'Pinterest username', 'smart-social-contact-panel' ),
			'discord'   => __( 'Discord invite code or link', 'smart-social-contact-panel' ),
			'skype'     => __( 'Skype username or ID', 'smart-social-contact-panel' ),
			'viber'     => __( 'Phone number with country code, e.g. +1234567890', 'smart-social-contact-panel' ),
			'line'      => __( 'Line username', 'smart-social-contact-panel' ),
			'wechat'    => __( 'WeChat page URL', 'smart-social-contact-panel' ),
			'email'     => __( 'Email address', 'smart-social-contact-panel' ),
			'phone'     => __( 'Phone number with country code, e.g. +1234567890', 'smart-social-contact-panel' ),
			'sms'       => __( 'Phone number with country code, e.g. +1234567890', 'smart-social-contact-panel' ),
			'website'   => __( 'Full URL including https://', 'smart-social-contact-panel' ),
		];

		return $placeholders[ $platform_id ] ?? __( 'Enter value', 'smart-social-contact-panel' );
	}
}
