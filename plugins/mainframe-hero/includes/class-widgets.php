<?php
/**
 * Registers the widget category and every widget the plugin ships.
 *
 * @package MainframeHero
 */

namespace MFH;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widgets {

	/**
	 * Category slug used by every widget in this plugin.
	 *
	 * @var string
	 */
	const CATEGORY = 'mainframe-hero';

	/**
	 * Wire the hooks.
	 */
	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ), 5 );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		// Pre-3.5 Elementor used a different action; keep supporting it.
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets_legacy' ) );
	}

	/**
	 * Add the "Mainframe" category to the widget panel.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 * @return void
	 */
	public function register_category( $elements_manager ) {
		$categories = array();

		if ( method_exists( $elements_manager, 'get_categories' ) ) {
			$categories = (array) $elements_manager->get_categories();
		}

		if ( isset( $categories[ self::CATEGORY ] ) ) {
			return;
		}

		$elements_manager->add_category(
			self::CATEGORY,
			array(
				'title' => __( 'Mainframe', 'mainframe-hero' ),
				'icon'  => 'eicon-header',
			)
		);
	}

	/**
	 * Register every widget (Elementor 3.5+).
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		$this->load_widget_classes();

		foreach ( $this->widget_classes() as $class ) {
			if ( ! class_exists( $class ) ) {
				continue;
			}

			$instance = new $class();

			if ( method_exists( $widgets_manager, 'register' ) ) {
				$widgets_manager->register( $instance );
			} elseif ( method_exists( $widgets_manager, 'register_widget_type' ) ) {
				$widgets_manager->register_widget_type( $instance );
			}
		}
	}

	/**
	 * Legacy registration path for Elementor below 3.5.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 * @return void
	 */
	public function register_widgets_legacy( $widgets_manager ) {
		if ( did_action( 'elementor/widgets/register' ) ) {
			return; // Already registered through the modern action.
		}

		$this->register_widgets( $widgets_manager );
	}

	/**
	 * Widget class names, in panel order.
	 *
	 * @return array
	 */
	private function widget_classes() {
		return array(
			'\MFH\Widgets\Scrub_Video',
			'\MFH\Widgets\Navbar',
			'\MFH\Widgets\Blur_Intro',
			'\MFH\Widgets\Typewriter',
			'\MFH\Widgets\Pills',
			'\MFH\Widgets\Character',
		);
	}

	/**
	 * Include the widget files once.
	 *
	 * @return void
	 */
	private function load_widget_classes() {
		$files = array(
			'class-widget-scrub-video.php',
			'class-widget-navbar.php',
			'class-widget-blur-intro.php',
			'class-widget-typewriter.php',
			'class-widget-pills.php',
			'class-widget-character.php',
		);

		foreach ( $files as $file ) {
			$path = MFH_PATH . 'widgets/' . $file;

			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}
}
