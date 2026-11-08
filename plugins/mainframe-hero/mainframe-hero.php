<?php
/**
 * Plugin Name: Mainframe Hero
 * Plugin URI:  https://github.com/ariaghasemi/ariagh.ir
 * Description: هیروی تمام‌صفحهٔ Mainframe برای وردپرس و المنتور. ویدیوی پس‌زمینه با اسکرابِ ماوس، متنِ تایپ‌شونده، دکمه‌های کپسولی و ناوبار — همراه با شش ویجت اختصاصی المنتور و یک قالب آمادهٔ کانتینری که دقیقاً با المنتور ساخته شده است.
 * Version:     1.0.0
 * Author:      Aria Ghasemi
 * Author URI:  https://ariagh.ir
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mainframe-hero
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 7.4
 *
 * @package MainframeHero
 *
 * NOTE ON LANGUAGE
 * ----------------
 * The source strings of this plugin are written in Persian on purpose: the
 * plugin was built for a Persian-first site and its interface must read as
 * Persian out of the box, with no translation step in between. Every string is
 * still passed through the WordPress i18n functions with the `mainframe-hero`
 * text domain and a POT catalogue ships in /languages, so the plugin can be
 * translated into any other language without touching a single line of code.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'MFH_VERSION', '1.0.0' );
define( 'MFH_FILE', __FILE__ );
define( 'MFH_PATH', plugin_dir_path( __FILE__ ) );
define( 'MFH_URL', plugin_dir_url( __FILE__ ) );
define( 'MFH_BASENAME', plugin_basename( __FILE__ ) );
define( 'MFH_MIN_ELEMENTOR', '3.15.0' );
define( 'MFH_MIN_PHP', '7.4' );

require_once MFH_PATH . 'includes/helpers.php';
require_once MFH_PATH . 'includes/class-bootstrap.php';

MFH\Bootstrap::instance();

register_activation_hook( __FILE__, array( 'MFH\Bootstrap', 'on_activate' ) );
register_deactivation_hook( __FILE__, array( 'MFH\Bootstrap', 'on_deactivate' ) );
