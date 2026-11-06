<?php
/**
 * Plugin Name:       مین‌فریم — هیروی تعاملی برای المنتور
 * Description:       پنج ویجت اختصاصی المنتور (ویدیوی حرکت با ماوس، تایپ‌نویسر، برچسب تار، دکمه‌های قرصی، نوار بالا) به‌همراه قالب آمادهٔ صفحهٔ هیرو — کاملاً فارسی، قابل ویرایش و بدون کد اضافه.
 * Version:           1.1.0
 * Author:            ariagh
 * Text Domain:       mainframe-elementor
 * Requires at least: 6.0
 * Requires PHP:      7.4
 *
 * معماری کلی:
 *  - فایل راه‌انداز: بارگذاری کلاس‌ها و قلاب‌های فعال‌سازی
 *  - Plugin:        منوی مدیریت، تنظیمات، دستهٔ ویجت‌ها، ثبت دارایی‌ها
 *  - Template:      ورود قالب JSON به «قالب‌های المنتور» + ساخت صفحه
 *  - Help:          صفحهٔ راهنمای جامع (شروع، معماری، مکانیزم فیلم، ویجت‌ها، عیب‌یابی)
 *  - Fonts:         بارگذاری فونت‌های Helvetica Now از تنظیمات
 *  - widgets/*:     پنج ویجت المنتور — هرکدام با کنترل‌های فارسی و راهنمای تفصیلی
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MF_VERSION', '1.1.0' );
define( 'MF_FILE', __FILE__ );
define( 'MF_DIR', plugin_dir_path( __FILE__ ) );
define( 'MF_URL', plugin_dir_url( __FILE__ ) );

require_once MF_DIR . 'includes/class-mainframe-plugin.php';
require_once MF_DIR . 'includes/class-mainframe-template.php';
require_once MF_DIR . 'includes/class-mainframe-help.php';
require_once MF_DIR . 'includes/class-mainframe-fonts.php';

/**
 * ثبت ویجت‌ها و دستهٔ «مین‌فریم» در زودترین زمان ممکن.
 *
 * نکتهٔ حیاتی: ترتیب بارگذاری افزونه‌ها ثابت نیست؛ اگر المنتور زودتر بالا بیاید،
 * قلاب‌هایی که داخل plugins_loaded ثبت کنیم دیر می‌شوند و افزونهٔ قبلی هرگز
 * ویجت‌ها را به المنتور نمی‌دهد → ویرایش قالب با «خطای بحرانی» می‌شکند.
 * پس این قلاب‌ها را همین حالا — پیش از plugins_loaded — می‌گذاریم.
 */
add_action( 'elementor/widgets/register', array( 'Mainframe_Plugin', 'action_register_widgets' ) );
add_action( 'elementor/widgets/widgets_registered', array( 'Mainframe_Plugin', 'action_register_widgets' ) );
add_action( 'elementor/elements/categories_registered', array( 'Mainframe_Plugin', 'action_register_category' ) );
// تعمیر خودکار دادهٔ خراب `_elementor_page_settings` نسخه‌های قبلی — زودترین زمان ممکن
add_action( 'elementor/loaded', array( 'Mainframe_Template', 'maybe_repair' ) );

/**
 * راه‌اندازی هسته — همیشه (بدون المنتور هم باید تنظیمات و راهنما در دسترس باشند).
 */
function mf_bootstrap() {
	Mainframe_Plugin::instance()->init();
}
add_action( 'plugins_loaded', 'mf_bootstrap' );

/**
 * پس از فعال‌سازی: پرچم ورود قالب را می‌گذاریم؛ اجرای واقعی وقتی است
 * که المنتور در دسترس باشد (در همین درخواست یا درخواست‌های بعدی).
 */
function mf_on_activate() {
	update_option( 'mf_template_pending', 1 );
}
register_activation_hook( __FILE__, 'mf_on_activate' );

/**
 * اگر المنتور زودتر فعال بوده باشد، `elementor/loaded` همین حالا اتفاق افتاده؛
 * پس بررسی در admin_init هم لازم است تا فعال‌سازی جا نماند.
 */
function mf_maybe_import_on_admin() {
	if ( ! get_option( 'mf_template_pending' ) ) {
		return;
	}
	if ( class_exists( '\Elementor\Plugin' ) ) {
		Mainframe_Template::maybe_import();
	}
}
add_action( 'admin_init', 'mf_maybe_import_on_admin' );
