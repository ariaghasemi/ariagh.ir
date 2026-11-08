<?php
/**
 * Registers and enqueues the plugin's front-end and editor assets.
 *
 * The widgets declare these handles through get_style_depends() and
 * get_script_depends(), so nothing loads on pages that do not use them.
 *
 * @package MainframeHero
 */

namespace MFH;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {

	/**
	 * Front-end script handle.
	 *
	 * @var string
	 */
	const HANDLE_JS = 'mainframe-hero-frontend';

	/**
	 * Front-end style handle.
	 *
	 * @var string
	 */
	const HANDLE_CSS = 'mainframe-hero-frontend';

	/**
	 * Frame-sampling library handle.
	 *
	 * @var string
	 */
	const HANDLE_LAB = 'mfh-video-lab';

	/**
	 * Editor script and style handle.
	 *
	 * @var string
	 */
	const HANDLE_EDITOR = 'mfh-editor';

	/**
	 * Wire the hooks.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register' ), 1 );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'register' ), 1 );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'register' ), 1 );

		// The editor needs two extra files: the frame-sampling library and the
		// script that drives auto-configuration. Both load only there.
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor' ), 20 );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_editor' ), 20 );
	}

	/**
	 * Register the handles. Registration is cheap; enqueueing is up to Elementor.
	 *
	 * @return void
	 */
	public function register() {
		wp_register_style(
			self::HANDLE_CSS,
			mfh_versioned_url( 'css/mainframe-hero.css' ),
			array(),
			MFH_VERSION
		);

		wp_register_script(
			self::HANDLE_JS,
			mfh_versioned_url( 'js/mainframe-hero.js' ),
			array(),
			MFH_VERSION,
			true
		);

		wp_register_script(
			self::HANDLE_LAB,
			mfh_versioned_url( 'js/mfh-video-lab.js' ),
			array(),
			MFH_VERSION,
			true
		);

		wp_localize_script(
			self::HANDLE_JS,
			'MFH',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'i18n'     => array(
					'copied'      => __( 'رایانامه کپی شد', 'mainframe-hero' ),
					'copyFailed'  => __( 'کپی انجام نشد؛ رایانامه را دستی بردارید', 'mainframe-hero' ),
					'menuOpen'    => __( 'باز کردن فهرست', 'mainframe-hero' ),
					'menuClose'   => __( 'بستن فهرست', 'mainframe-hero' ),
				),
				'reduced'  => $this->prefers_reduced_motion(),
				'defaults' => self::shared_defaults(),
			)
		);
	}

	/**
	 * Load the editor-only assets.
	 *
	 * @return void
	 */
	public function enqueue_editor() {
		wp_register_style(
			self::HANDLE_EDITOR,
			mfh_versioned_url( 'css/mfh-editor.css' ),
			array(),
			MFH_VERSION
		);

		wp_register_script(
			self::HANDLE_EDITOR,
			mfh_versioned_url( 'js/mfh-editor.js' ),
			array(),
			MFH_VERSION,
			true
		);

		wp_localize_script(
			self::HANDLE_EDITOR,
			'MFH_EDITOR',
			array(
				'i18n' => array(
					'analysing' => __( 'در حال تحلیلِ ویدیو…', 'mainframe-hero' ),
					'done'      => __( 'تنظیماتِ خودکار اعمال شد.', 'mainframe-hero' ),
					'failed'    => __( 'ویدیو را نتوانستم بخوانم. نشانی را بررسی کنید.', 'mainframe-hero' ),
					'duration'  => __( 'مدت‌زمان', 'mainframe-hero' ),
					'seconds'   => __( 'ثانیه', 'mainframe-hero' ),
					'frame'     => __( 'فریمِ آغاز', 'mainframe-hero' ),
					'sens'      => __( 'حساسیت', 'mainframe-hero' ),
					'noPixels'  => __( 'فریم‌ها قابلِ خواندن نبود (نبودِ هدرِ CORS)؛ تنها مدت‌زمان به کار رفت.', 'mainframe-hero' ),
				),
			)
		);

		wp_enqueue_style( self::HANDLE_EDITOR );
		wp_enqueue_script( self::HANDLE_LAB );
		wp_enqueue_script( self::HANDLE_EDITOR );
	}

	/**
	 * The documented defaults, shared with the panel and the handbook.
	 *
	 * Keeping one source of truth means the number a user reads in the docs is
	 * the number the widget actually uses.
	 *
	 * @return array
	 */
	public static function shared_defaults() {
		return array(
			// How many seconds of footage one full-width pointer sweep covers.
			'comfortSeconds'   => 9.6,
			'sensitivity'      => 0.8,
			// Mean luminance (0-255) a frame must reach to count as "has content".
			'luminanceFloor'   => 18,
			// How many frames to sample when hunting for the first live frame.
			'sampleCount'      => 24,
			'pointerSmoothing' => 0.06,
			'characterDrift'   => 26,
			'typeSpeed'        => 38,
			'typeDelay'        => 600,
			'blurPx'           => 4,
		);
	}

	/**
	 * Mirror the user's OS-level motion preference into JS.
	 *
	 * The CSS media query already covers our own animations; this flag lets the
	 * scrub engine disable itself before it ever touches a video element.
	 *
	 * @return bool
	 */
	private function prefers_reduced_motion() {
		// A visitor's preference is only knowable in the browser, so the real
		// decision lives in JS. This is a conservative default for the payload.
		return false;
	}
}
