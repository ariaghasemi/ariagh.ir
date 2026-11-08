<?php
/**
 * Shared helpers.
 *
 * @package MainframeHero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mfh_elementor_active' ) ) {
	/**
	 * Is Elementor installed and actually loaded?
	 *
	 * @return bool
	 */
	function mfh_elementor_active() {
		return did_action( 'elementor/loaded' ) || defined( 'ELEMENTOR_VERSION' );
	}
}

if ( ! function_exists( 'mfh_elementor_version' ) ) {
	/**
	 * Installed Elementor version, or an empty string when it is missing.
	 *
	 * @return string
	 */
	function mfh_elementor_version() {
		return defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '';
	}
}

if ( ! function_exists( 'mfh_elementor_meets_minimum' ) ) {
	/**
	 * Does the installed Elementor satisfy the minimum the plugin needs?
	 *
	 * @return bool
	 */
	function mfh_elementor_meets_minimum() {
		$version = mfh_elementor_version();

		if ( ! $version ) {
			return false;
		}

		return version_compare( $version, MFH_MIN_ELEMENTOR, '>=' );
	}
}

if ( ! function_exists( 'mfh_elementor_instance' ) ) {
	/**
	 * The Elementor plugin instance, or null when it is not available yet.
	 *
	 * @return \Elementor\Plugin|null
	 */
	function mfh_elementor_instance() {
		if ( ! mfh_elementor_active() ) {
			return null;
		}

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return null;
		}

		return \Elementor\Plugin::instance();
	}
}

if ( ! function_exists( 'mfh_asset_url' ) ) {
	/**
	 * URL of a file inside the plugin's assets directory.
	 *
	 * @param string $path Path relative to /assets.
	 * @return string
	 */
	function mfh_asset_url( $path ) {
		return MFH_URL . 'assets/' . ltrim( (string) $path, '/' );
	}
}

if ( ! function_exists( 'mfh_asset_path' ) ) {
	/**
	 * Absolute path of a file inside the plugin's assets directory.
	 *
	 * @param string $path Path relative to /assets.
	 * @return string
	 */
	function mfh_asset_path( $path ) {
		return MFH_PATH . 'assets/' . ltrim( (string) $path, '/' );
	}
}

if ( ! function_exists( 'mfh_versioned_url' ) ) {
	/**
	 * Asset URL with a cache-busting version query.
	 *
	 * In development the file mtime is used so edits show up immediately; in
	 * production the plugin version keeps the URL stable and cache friendly.
	 *
	 * @param string $path Path relative to /assets.
	 * @return string
	 */
	function mfh_versioned_url( $path ) {
		$url  = mfh_asset_url( $path );
		$file = mfh_asset_path( $path );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && file_exists( $file ) ) {
			return add_query_arg( 'ver', (string) filemtime( $file ), $url );
		}

		return add_query_arg( 'ver', MFH_VERSION, $url );
	}
}

if ( ! function_exists( 'mfh_template_path' ) ) {
	/**
	 * Absolute path of one of the bundled Elementor templates.
	 *
	 * @param string $file File name inside /templates.
	 * @return string
	 */
	function mfh_template_path( $file ) {
		return MFH_PATH . 'templates/' . ltrim( (string) $file, '/' );
	}
}

if ( ! function_exists( 'mfh_read_json_template' ) ) {
	/**
	 * Read and decode a bundled Elementor template file.
	 *
	 * @param string $file File name inside /templates.
	 * @return array
	 */
	function mfh_read_json_template( $file ) {
		$path = mfh_template_path( $file );

		if ( ! file_exists( $path ) ) {
			return array();
		}

		$raw = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( false === $raw ) {
			return array();
		}

		$data = json_decode( $raw, true );

		return is_array( $data ) ? $data : array();
	}
}

if ( ! function_exists( 'mfh_bool' ) ) {
	/**
	 * Normalise an Elementor switcher value into a strict boolean.
	 *
	 * @param mixed $value Raw setting value.
	 * @return bool
	 */
	function mfh_bool( $value ) {
		return in_array( $value, array( true, 'yes', '1', 1, 'true' ), true );
	}
}

if ( ! function_exists( 'mfh_human_time' ) ) {
	/**
	 * Seconds written the way a person reads them.
	 *
	 * @param float $seconds Seconds.
	 * @return string
	 */
	function mfh_human_time( $seconds ) {
		$seconds = (float) $seconds;

		if ( $seconds < 60 ) {
			return sprintf( '%s ثانیه', number_format_i18n( $seconds, 2 ) );
		}

		$minutes = floor( $seconds / 60 );
		$rest    = $seconds - ( $minutes * 60 );

		return sprintf( '%d دقیقه و %s ثانیه', (int) $minutes, number_format_i18n( $rest, 1 ) );
	}
}

if ( ! function_exists( 'mfh_json_attr' ) ) {
	/**
	 * Encode a value for an HTML attribute safely.
	 *
	 * The flags matter: tags, ampersands and quotes are escaped so the JSON can
	 * never break out of the attribute, while non-ASCII text is left readable.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to encode.
	 *
	 * @return string
	 */
	function mfh_json_attr( $value ) {
		return wp_json_encode(
			$value,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT
		);
	}
}
