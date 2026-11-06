<?php
/**
 * هستهٔ افزونه: منوی مدیریت، تنظیمات، دستهٔ ویجت‌ها و ثبت دارایی‌ها.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * تبدیل مقدار کنترل ابعاد المنتور به رشتهٔ CSS.
 * ورودی: ['unit'=>'px','top'=>'16','right'=>'20','bottom'=>'16','left'=>'20']
 * خروجی: "16px 20px 16px 20px"
 */
function mf_dim( $v ) {
	if ( ! is_array( $v ) ) {
		return '';
	}
	$unit = isset( $v['unit'] ) ? $v['unit'] : 'px';
	$top  = isset( $v['top'] ) && '' !== $v['top'] ? $v['top'] : '0';
	$right = isset( $v['right'] ) && '' !== $v['right'] ? $v['right'] : '0';
	$bottom = isset( $v['bottom'] ) && '' !== $v['bottom'] ? $v['bottom'] : '0';
	$left = isset( $v['left'] ) && '' !== $v['left'] ? $v['left'] : '0';
	return $top . $unit . ' ' . $right . $unit . ' ' . $bottom . $unit . ' ' . $left . $unit;
}

/**
 * عدد امن با پیش‌فرض.
 */
function mf_num( $v, $default = 0 ) {
	if ( null === $v || '' === $v || false === $v ) {
		return $default;
	}
	return is_numeric( $v ) ? $v + 0 : $default;
}

/**
 * ویژگی‌های target/rel برای لینک‌های کنترل URL (باز شدن در پنجرهٔ جدید / nofollow).
 * خروجی: رشتهٔ آمادهٔ درج در تگ <a> (با فاصلهٔ ابتدا) یا رشتهٔ خالی.
 */
function mf_link_target( $link ) {
	$link = is_array( $link ) ? $link : array();
	$rel  = '';
	$attr = '';
	if ( ! empty( $link['is_external'] ) ) {
		$attr = ' target="_blank"';
		$rel  = 'noopener';
	}
	if ( ! empty( $link['nofollow'] ) ) {
		$rel = trim( $rel . ' nofollow' );
	}
	if ( $rel ) {
		$attr .= ' rel="' . esc_attr( $rel ) . '"';
	}
	return $attr;
}

class Mainframe_Plugin {

	/** @var Mainframe_Plugin */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** نام گزینه‌های تنظیمات (برای فرم و خواندن مشترک) */
	const OPT = 'mf_settings';

	/**
	 * پیش‌فرض‌های تنظیمات — دقیقاً همان مقادیر دموی Mainframe.
	 */
	public static function defaults() {
		return array(
			'fonts_enabled'  => '1',
			'heading_url'    => 'https://db.onlinewebfonts.com/c/5ac3fe7c6abd2f62067f266d89671492?family=HelveticaNowDisplay-Medium',
			'heading_family' => "'HelveticaNowDisplay-Medium', 'Helvetica Neue', Arial, sans-serif",
			'body_url'       => 'https://db.onlinewebfonts.com/c/1aa3377e489837a26d019bba501e779d?family=HelveticaNowDisplayW01-Rg',
			'body_family'    => "'HelveticaNowDisplayW01-Rg', 'Helvetica Neue', Arial, sans-serif",
			'default_email'  => 'hello@mainframe.co',
		);
	}

	public static function get_settings() {
		$saved = get_option( self::OPT, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		return wp_parse_args( $saved, self::defaults() );
	}

	public function init() {
		// دارایی‌های مشترک ویجت‌ها (فقط ثبت؛ زمان بارگذاری را المنتور تصمیم می‌گیرد)
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 5 );

		// فونت‌ها و متغیرهای --mf-heading / --mf-body
		Mainframe_Fonts::init();

		// دستهٔ اختصاصی ویجت‌ها و ثبت خود ویجت‌ها:
		// این دو از فایل اصلی (پیش از plugins_loaded) ثبت می‌شوند تا با تغییر ترتیب
		// بارگذاری افزونه‌ها از دست نروند — action_register_widgets / action_register_category

		// تنظیمات
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// تعمیر دادهٔ قدیمی در هر درخواست مدیریت (پس از آپلود افزونهٔ جدید،
		// قبل از باز شدن ویرایشگر اجرا می‌شود؛ فقط یک بار در هر نسخه)
		add_action( 'admin_init', array( 'Mainframe_Template', 'maybe_repair' ) );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

		// اعلان نبود المنتور
		add_action( 'admin_notices', array( $this, 'elementor_missing_notice' ) );

		// ورود قالب به محیط المنتور
		add_action( 'elementor/loaded', array( 'Mainframe_Template', 'maybe_import' ) );

		// فرم‌های دکمه‌دار صفحهٔ راهنما
		add_action( 'admin_post_mf_create_template', array( 'Mainframe_Template', 'handle_create_template' ) );
		add_action( 'admin_post_mf_force_rebuild', array( 'Mainframe_Template', 'handle_force_rebuild' ) );
		add_action( 'admin_post_mf_create_page', array( 'Mainframe_Template', 'handle_create_page' ) );

		// راهنما
		Mainframe_Help::instance()->init();
	}

	/** آیا ویجت‌ها در همین درخواست ثبت شده‌اند؟ (جلوگیری از ثبت دوباره) */
	private static $widgets_registered = false;

	/**
	 * قلاب ثبت ویجت‌ها نزد المنتور — از فایل اصلی و پیش از plugins_loaded صدا زده می‌شود
	 * تا با هر ترتیبی از بارگذاری افزونه‌ها، ویجت‌ها حتماً به ویرایشگر برسند.
	 *
	 * @param mixed $widgets_manager شیء مدیر ویجت‌های المنتور.
	 */
	public static function action_register_widgets( $widgets_manager ) {
		if ( self::$widgets_registered ) {
			return;
		}
		if ( ! is_object( $widgets_manager ) || ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}
		self::$widgets_registered = true;

		self::load_widget_classes();

		$classes = array(
			'Widget_Mainframe_Scrub_Video',
			'Widget_Mainframe_Typewriter',
			'Widget_Mainframe_Intro_Label',
			'Widget_Mainframe_Pills',
			'Widget_Mainframe_Navbar',
		);

		foreach ( $classes as $class ) {
			if ( class_exists( $class ) ) {
				$widgets_manager->register( new $class() );
			}
		}
	}

	/**
	 * بارگذاری امن کلاس‌های ویجت (require_once — امن در برابر اجرای چندباره).
	 */
	public static function load_widget_classes() {
		foreach ( array(
			'class-widget-scrub-video.php',
			'class-widget-typewriter.php',
			'class-widget-intro-label.php',
			'class-widget-pills.php',
			'class-widget-navbar.php',
		) as $file ) {
			require_once MF_DIR . 'widgets/' . $file;
		}
	}

	/**
	 * دستهٔ «مین‌فریم» در انتخاب‌گر ویجت‌های المنتور.
	 *
	 * @param mixed $elements_manager شیء مدیر المان‌های المنتور.
	 */
	public static function action_register_category( $elements_manager ) {
		if ( ! is_object( $elements_manager ) || ! method_exists( $elements_manager, 'add_category' ) ) {
			return;
		}
		$elements_manager->add_category(
			'mainframe',
			array(
				'title' => 'مین‌فریم',
				'icon'  => 'eicon-play-o',
			)
		);
	}

	/**
	 * ثبت دارایی‌های جلوی صفحه — زمانی که ویجت‌ها از آن‌ها استفاده کنند بارگذاری می‌شوند.
	 */
	public function register_assets() {
		wp_register_style(
			'mainframe-frontend',
			MF_URL . 'assets/css/mainframe-frontend.css',
			array(),
			MF_VERSION
		);
		wp_register_script(
			'mainframe-frontend',
			MF_URL . 'assets/js/mainframe-frontend.js',
			// بدون این وابستگی، هوک‌های elementorFrontend در ادیتور ممکن است هنوز آماده نباشند.
			array( 'elementor-frontend' ),
			MF_VERSION,
			true
		);
	}

	public function register_settings() {
		register_setting(
			'mf_settings_group',
			self::OPT,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	public function sanitize_settings( $input ) {
		$out    = array();
		$defaults = self::defaults();
		$in = is_array( $input ) ? $input : array();

		$out['fonts_enabled']  = isset( $in['fonts_enabled'] ) ? '1' : '0';
		$out['heading_url']    = esc_url_raw( isset( $in['heading_url'] ) ? $in['heading_url'] : $defaults['heading_url'] );
		$out['body_url']       = esc_url_raw( isset( $in['body_url'] ) ? $in['body_url'] : $defaults['body_url'] );
		$out['heading_family'] = isset( $in['heading_family'] ) ? wp_strip_all_tags( $in['heading_family'] ) : $defaults['heading_family'];
		$out['body_family']    = isset( $in['body_family'] ) ? wp_strip_all_tags( $in['body_family'] ) : $defaults['body_family'];
		$out['default_email']  = isset( $in['default_email'] ) ? sanitize_email( $in['default_email'] ) : $defaults['default_email'];

		return $out;
	}

	public function register_menu() {
		add_menu_page(
			'مین‌فریم',
			'مین‌فریم',
			'manage_options',
			'mainframe',
			array( 'Mainframe_Help', 'render_page' ),
			'dashicons-layout',
			59
		);
	}

	public function admin_assets( $hook ) {
		if ( false === strpos( $hook, 'mainframe' ) ) {
			return;
		}
		wp_enqueue_style(
			'mainframe-admin',
			MF_URL . 'assets/css/mainframe-admin.css',
			array(),
			MF_VERSION
		);
	}

	public function elementor_missing_notice() {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		if ( ! isset( $_GET['page'] ) || 'mainframe' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		echo '<div class="notice notice-error"><p><strong>مین‌فریم:</strong> افزونهٔ المنتور نصب یا فعال نیست. برای استفاده از ویجت‌ها و قالب، ابتدا Elementor را نصب و فعال کنید، سپس این صفحه را تازه‌سازی کنید.</p></div>';
	}
}
