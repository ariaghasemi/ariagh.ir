<?php
/**
 * قالب آمادهٔ هیرو: ورود JSON به «قالب‌های المنتور» (elementor_library)
 * و ساخت صفحهٔ مستقل با همان ساختار.
 *
 * نکتهٔ مهم: دادهٔ `_elementor_data` باید اسلش‌خورده ذخیره شود
 * چون توابع متا در وردپرس داده را unslash می‌کنند.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Mainframe_Template {

	const TEMPLATE_TITLE = 'هیرو مین‌فریم (قالب آماده)';
	const PAGE_TITLE     = 'هیرو مین‌فریم';

	/**
	 * خواندن فایل JSON قالب.
	 * @return array|null
	 */
	public static function load_json() {
		$path = MF_DIR . 'templates/mainframe-hero.json';
		if ( ! file_exists( $path ) ) {
			return null;
		}
		$raw  = file_get_contents( $path );
		$data = json_decode( $raw, true );
		if ( ! is_array( $data ) || empty( $data['content'] ) ) {
			return null;
		}
		return $data;
	}

	/**
	 * پیدا کردن قالب/صفحهٔ موجود با عنوان.
	 */
	private static function find_post( $title, $type ) {
		$posts = get_posts(
			array(
				'post_type'   => $type,
				'title'       => $title,
				'post_status' => array( 'publish', 'draft', 'private' ),
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);
		return $posts ? (int) $posts[0] : 0;
	}

	/**
	 * اعمال دادهٔ Elementor روی یک پست.
	 */
	private static function apply_elementor_data( $post_id, $json ) {
		$content      = $json['content'];
		$page_settings = isset( $json['page_settings'] ) ? $json['page_settings'] : array();

		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $content ) ) );
		// نکته: المنتور این متا را به‌صورت «آرایه» می‌خواند (وردپرس خودش serialize می‌کند)؛
		// ذخیره به‌صورت رشتهٔ JSON باعث TypeError در sanitize_settings می‌شد. (v1.0.2)
		update_post_meta( $post_id, '_elementor_page_settings', $page_settings );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
		}
		// پاک‌سازی کش CSS اختصاصی پست تا استایل‌ها بازسازی شوند
		delete_post_meta( $post_id, '_elementor_css' );
	}

	/**
	 * ساخت یا به‌روزرسانی قالب در elementor_library.
	 * @return int شناسهٔ پست (0 در صورت شکست)
	 */
	public static function import_template( $force = false ) {
		$json = self::load_json();
		if ( null === $json ) {
			return 0;
		}

		$existing = self::find_post( self::TEMPLATE_TITLE, 'elementor_library' );

		if ( $existing && ! $force ) {
			return $existing;
		}

		if ( $existing ) {
			self::apply_elementor_data( $existing, $json );
			return $existing;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'    => self::TEMPLATE_TITLE,
				'post_type'     => 'elementor_library',
				'post_status'   => 'publish',
				'post_content'  => '',
				'post_excerpt'  => 'قالب کامل هیروی مین‌فریم — ساخته‌شده با کانتینرهای استاندارد المنتور و ویجت‌های اختصاصی مین‌فریم. بدون هیچ کد سفارشی در المان‌ها.',
				'comment_status' => 'closed',
			)
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return 0;
		}

		self::apply_elementor_data( $post_id, $json );

		// نوع قالب برای نمایش در فهرست قالب‌های المنتور
		update_post_meta( $post_id, '_elementor_template_type', 'page' );

		return $post_id;
	}

	/**
	 * ساخت صفحهٔ وردپرس با همان دادهٔ المنتور.
	 * @return int شناسهٔ صفحه
	 */
	public static function import_page( $force = false ) {
		$json = self::load_json();
		if ( null === $json ) {
			return 0;
		}

		$existing = self::find_post( self::PAGE_TITLE, 'page' );

		if ( $existing && ! $force ) {
			return $existing;
		}

		if ( $existing ) {
			self::apply_elementor_data( $existing, $json );
			return $existing;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'   => self::PAGE_TITLE,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '',
			)
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return 0;
		}

		self::apply_elementor_data( $post_id, $json );

		return $post_id;
	}

	/**
	 * ورود خودکار: یک بار پس از فعال‌سازی (پرچم mf_template_pending).
	 * ایمن در برابر اجرای چندباره.
	 */
	public static function maybe_import() {
		if ( ! get_option( 'mf_template_pending' ) ) {
			return;
		}
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		self::import_template();
		delete_option( 'mf_template_pending' );
	}

	/* ─── دکمه‌های مدیریتی ─── */

	private static function verify_nonce( $action ) {
		$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		return (bool) wp_verify_nonce( $nonce, $action );
	}

	private static function redirect_with( $msg ) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'page' => 'mainframe',
					'tab'  => 'template',
					'mf'   => $msg,
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	public static function handle_create_template() {
		if ( ! current_user_can( 'manage_options' ) || ! self::verify_nonce( 'mf_create_template' ) ) {
			wp_die( 'دسترسی غیرمجاز' );
		}
		$id = self::import_template();
		self::redirect_with( $id ? 'template_ok' : 'template_fail' );
	}

	public static function handle_force_rebuild() {
		if ( ! current_user_can( 'manage_options' ) || ! self::verify_nonce( 'mf_force_rebuild' ) ) {
			wp_die( 'دسترسی غیرمجاز' );
		}
		// قالب و صفحه هر دو با آخرین نسخهٔ JSON بازنویسی می‌شوند
		self::import_template( true );
		self::import_page( true );
		self::redirect_with( 'rebuild_ok' );
	}

	public static function handle_create_page() {
		if ( ! current_user_can( 'manage_options' ) || ! self::verify_nonce( 'mf_create_page' ) ) {
			wp_die( 'دسترسی غیرمجاز' );
		}
		$id = self::import_page();
		self::redirect_with( $id ? 'page_ok' : 'page_fail' );
	}

	/**
	 * تعمیر دادهٔ خراب نسخه‌های قبلی (1.0.0 و 1.0.1):
	 * آنجا `_elementor_page_settings` به‌صورت رشتهٔ JSON ذخیره شده بود
	 * و المنتور هنگام باز کردن سند با TypeError «sanitize_settings must be
	 * of the type array» می‌مرد. این متد رشته را به آرایه تبدیل می‌کند.
	 * فقط یک بار در هر نسخه اجرا می‌شود.
	 */
	public static function maybe_repair() {
		if ( get_option( 'mf_meta_repaired' ) === MF_VERSION ) {
			return;
		}
		self::repair();
		update_option( 'mf_meta_repaired', MF_VERSION );
	}

	public static function repair() {
		$targets = array(
			array( self::TEMPLATE_TITLE, 'elementor_library' ),
			array( self::PAGE_TITLE, 'page' ),
		);

		foreach ( $targets as $target ) {
			$post_id = self::find_post( $target[0], $target[1] );
			if ( ! $post_id ) {
				continue;
			}
			$value = get_post_meta( $post_id, '_elementor_page_settings', true );
			if ( is_string( $value ) && '' !== $value ) {
				$decoded = json_decode( $value, true );
				if ( is_array( $decoded ) ) {
					update_post_meta( $post_id, '_elementor_page_settings', $decoded );
				} else {
					delete_post_meta( $post_id, '_elementor_page_settings' );
				}
			}
		}
	}

	/**
	 * وضعیت فعلی برای نمایش در صفحهٔ راهنما.
	 */
	public static function status() {
		return array(
			'template_id' => self::find_post( self::TEMPLATE_TITLE, 'elementor_library' ),
			'page_id'     => self::find_post( self::PAGE_TITLE, 'page' ),
			'json_ok'     => null !== self::load_json(),
		);
	}
}
