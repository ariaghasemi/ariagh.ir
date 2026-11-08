<?php
/**
 * Plugin bootstrap. Owns the hook table and wires every module together.
 *
 * @package MainframeHero
 */

namespace MFH;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Bootstrap {

	/**
	 * Singleton instance.
	 *
	 * @var Bootstrap|null
	 */
	private static $instance = null;

	/**
	 * Modules that were successfully started.
	 *
	 * @var array
	 */
	private $modules = array();

	/**
	 * Get — or create — the singleton.
	 *
	 * @return Bootstrap
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hook everything up. Deliberately thin: no work happens here.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 5 );
		add_action( 'plugins_loaded', array( $this, 'start' ), 20 );
		add_filter( 'plugin_action_links_' . MFH_BASENAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'row_meta' ), 10, 2 );
	}

	/**
	 * Load the translation catalogue.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'mainframe-hero', false, dirname( MFH_BASENAME ) . '/languages' );
	}

	/**
	 * Start the modules that can run in the current environment.
	 *
	 * @return void
	 */
	public function start() {
		if ( ! $this->requirements_met() ) {
			add_action( 'admin_notices', array( $this, 'notice_requirements' ) );
			return;
		}

		require_once MFH_PATH . 'includes/class-assets.php';
		require_once MFH_PATH . 'includes/class-widgets.php';
		require_once MFH_PATH . 'includes/class-templates.php';
		require_once MFH_PATH . 'includes/class-library-sync.php';
		require_once MFH_PATH . 'includes/class-admin.php';

		$this->modules['assets']   = new Assets();
		$this->modules['widgets']  = new Widgets();
		$this->modules['templates'] = new Library_Sync();

		if ( is_admin() ) {
			$this->modules['admin'] = new Admin();
		}

		/**
		 * Fired once every module is up.
		 *
		 * @since 1.0.0
		 */
		do_action( 'mfh/loaded', $this );
	}

	/**
	 * Is the environment able to run the plugin?
	 *
	 * @return bool
	 */
	public function requirements_met() {
		return mfh_elementor_active() && mfh_elementor_meets_minimum();
	}

	/**
	 * Admin notice shown when Elementor is missing or too old.
	 *
	 * @return void
	 */
	public function notice_requirements() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$installed = mfh_elementor_version();
		$screen    = get_current_screen();

		// Do not nag on the plugin install screen — the user is already there.
		if ( $screen && 'plugin-install' === $screen->id ) {
			return;
		}

		echo '<div class="notice notice-warning mfh-notice"><p>';

		if ( ! $installed ) {
			echo esc_html__( 'افزونهٔ Mainframe Hero برای کار به المنتور نیاز دارد. لطفاً المنتور را نصب و فعال کنید.', 'mainframe-hero' );
		} else {
			printf(
				/* translators: 1: installed Elementor version, 2: required version */
				esc_html__( 'افزونهٔ Mainframe Hero به المنتور نسخهٔ %2$s یا بالاتر نیاز دارد. نسخهٔ فعلی شما %1$s است؛ لطفاً المنتور را به‌روزرسانی کنید.', 'mainframe-hero' ),
				esc_html( $installed ),
				esc_html( MFH_MIN_ELEMENTOR )
			);
		}

		echo '</p></div>';
	}

	/**
	 * Quick links on the plugins screen.
	 *
	 * @param array $links Existing links.
	 * @return array
	 */
	public function action_links( $links ) {
		$extra = array(
			sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=mainframe-hero' ) ),
				esc_html__( 'راهنما و تنظیمات', 'mainframe-hero' )
			),
		);

		return array_merge( $extra, (array) $links );
	}

	/**
	 * Extra rows under the plugin description.
	 *
	 * @param array  $meta  Existing rows.
	 * @param string $file  Plugin file being rendered.
	 * @return array
	 */
	public function row_meta( $meta, $file ) {
		if ( MFH_BASENAME !== $file ) {
			return $meta;
		}

		$meta[] = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( 'https://github.com/ariaghasemi/ariagh.ir' ),
			esc_html__( 'مخزن پروژه', 'mainframe-hero' )
		);

		return $meta;
	}

	/**
	 * Get a started module by name.
	 *
	 * @param string $name Module key.
	 * @return object|null
	 */
	public function module( $name ) {
		return isset( $this->modules[ $name ] ) ? $this->modules[ $name ] : null;
	}

	/**
	 * Activation: seed the Elementor template so it is waiting in the library.
	 *
	 * @return void
	 */
	public static function on_activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		require_once MFH_PATH . 'includes/helpers.php';
		require_once MFH_PATH . 'includes/class-templates.php';

		Templates::seed();
		set_transient( 'mfh_activated', '1', 60 );
	}

	/**
	 * Deactivation. Nothing is deleted — the user's templates are their own.
	 *
	 * @return void
	 */
	public static function on_deactivate() {
		delete_transient( 'mfh_activated' );
	}
}
