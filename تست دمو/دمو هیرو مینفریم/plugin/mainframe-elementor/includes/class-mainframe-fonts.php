<?php
/**
 * بارگذاری فونت‌های Helvetica Now از تنظیمات افزونه.
 * آدرس‌ها و نام خانواده پیش‌فرض دقیقاً همان دموی Mainframe است.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Mainframe_Fonts {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ), 6 );
	}

	public static function enqueue() {
		$settings = Mainframe_Plugin::get_settings();

		// متغیرهای CSS — همیشه تعریف می‌شوند تا ویجت‌ها به آن‌ها تکیه کنند
		$vars = ':root{--mf-heading:' . esc_attr( $settings['heading_family'] ) . ';--mf-body:' . esc_attr( $settings['body_family'] ) . ';}';
		wp_register_style( 'mainframe-vars', false, array(), MF_VERSION );
		wp_enqueue_style( 'mainframe-vars' );
		wp_add_inline_style( 'mainframe-vars', $vars );

		if ( '1' !== $settings['fonts_enabled'] ) {
			return;
		}

		if ( $settings['heading_url'] ) {
			wp_enqueue_style( 'mainframe-font-heading', $settings['heading_url'], array(), MF_VERSION );
		}
		if ( $settings['body_url'] ) {
			wp_enqueue_style( 'mainframe-font-body', $settings['body_url'], array(), MF_VERSION );
		}
	}
}
