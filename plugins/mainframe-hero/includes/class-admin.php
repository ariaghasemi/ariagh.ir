<?php
/**
 * Admin screen: the in-plugin handbook.
 *
 * @package MainframeHero
 */

namespace MFH;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin {

	/**
	 * Menu slug of the handbook page.
	 *
	 * @var string
	 */
	const PAGE = 'mainframe-hero';

	/**
	 * Wire the hooks.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_notices', array( $this, 'notice_activated' ) );
	}

	/**
	 * Register the top-level menu entry.
	 *
	 * @return void
	 */
	public function menu() {
		add_menu_page(
			__( 'Mainframe Hero', 'mainframe-hero' ),
			__( 'Mainframe Hero', 'mainframe-hero' ),
			'edit_posts',
			self::PAGE,
			array( $this, 'render' ),
			'dashicons-format-video',
			58
		);

		add_submenu_page(
			self::PAGE,
			__( 'راهنما', 'mainframe-hero' ),
			__( 'راهنما', 'mainframe-hero' ),
			'edit_posts',
			self::PAGE,
			array( $this, 'render' )
		);

		add_submenu_page(
			self::PAGE,
			__( 'الگوها', 'mainframe-hero' ),
			__( 'الگوها', 'mainframe-hero' ),
			'edit_posts',
			'edit.php?post_type=elementor_library'
		);

		add_submenu_page(
			self::PAGE,
			__( 'تنظیمات', 'mainframe-hero' ),
			__( 'تنظیمات', 'mainframe-hero' ),
			'manage_options',
			'mainframe-hero-settings',
			array( $this, 'render_settings' )
		);
	}

	/**
	 * Load the stylesheet that only the handbook needs.
	 *
	 * @param string $hook Current admin page hook suffix.
	 *
	 * @return void
	 */
	public function enqueue( $hook ) {
		if ( false === strpos( (string) $hook, 'mainframe-hero' ) ) {
			return;
		}

		wp_enqueue_style(
			'mainframe-hero-admin',
			mfh_versioned_url( 'assets/css/admin.css' ),
			array(),
			MFH_VERSION
		);

		wp_enqueue_script( 'jquery' );
	}

	/**
	 * One-time notice pointing to the handbook after activation.
	 *
	 * @return void
	 */
	public function notice_activated() {
		if ( ! get_transient( 'mfh_activated' ) ) {
			return;
		}

		delete_transient( 'mfh_activated' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-success is-dismissible mfh-notice"><p>%s <a href="%s">%s</a></p></div>',
			esc_html__( 'Mainframe Hero فعال شد. یک الگوی آماده در کتابخانهٔ المنتور قرار گرفت.', 'mainframe-hero' ),
			esc_url( admin_url( 'admin.php?page=' . self::PAGE ) ),
			esc_html__( 'راهنما را بخوانید', 'mainframe-hero' )
		);
	}

	/**
	 * Render the handbook.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'اجازهٔ دیدن این صفحه را ندارید.', 'mainframe-hero' ) );
		}

		$view = MFH_PATH . 'views/docs.php';

		if ( ! file_exists( $view ) ) {
			return;
		}

		echo '<div class="wrap mfh-docs" dir="rtl">';
		require_once $view;
		echo '</div>';
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'اجازهٔ دیدن این صفحه را ندارید.', 'mainframe-hero' ) );
		}

		$view = MFH_PATH . 'views/settings.php';

		if ( ! file_exists( $view ) ) {
			return;
		}

		echo '<div class="wrap mfh-docs" dir="rtl">';
		require_once $view;
		echo '</div>';
	}
}
