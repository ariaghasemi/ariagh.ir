<?php
/**
 * صفحهٔ راهنمای افزونه — شش بخش:
 * راهنما (شروع، معماری، مکانیزم فیلم، ساختار قالب، آناتومی) | ویجت‌ها |
 * قالب و صفحه | تنظیمات | عیب‌یابی | توسعه‌دهندگان.
 *
 * همهٔ متن‌ها فارسی و بدون ایموجی؛ گرافیک‌ها SVG مینیمال و مدرن.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Mainframe_Help {

	/** @var Mainframe_Help */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		// خودکار: فقط اتصال کلاس (منو در Plugin ثبت شده)
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'guide';
		$tabs = self::tabs();
		if ( ! isset( $tabs[ $tab ] ) ) {
			$tab = 'guide';
		}
		$msg = isset( $_GET['mf'] ) ? sanitize_key( wp_unslash( $_GET['mf'] ) ) : '';

		echo '<div class="mf-wrap" dir="rtl">';
		echo '<header class="mf-head">';
		echo '<div class="mf-head-text"><span class="mf-kicker">افزونهٔ ویجت و قالب المنتور</span><h1>مین‌فریم — راهنمای کامل</h1><p>هرچه لازم دارید همین‌جاست: نصب، معماری، نحوهٔ کار فیلم هیرو، کنترل‌های هر ویجت، تنظیمات و عیب‌یابی.</p></div>';
		echo '<div class="mf-head-meta"><span class="mf-ver">' . esc_html( MF_VERSION ) . '</span><span>سازگار با المنتور 3 و وردپرس 6</span></div>';
		echo '</header>';

		if ( $msg ) {
			echo self::message_html( $msg );
		}

		echo '<nav class="mf-tabs">';
		foreach ( $tabs as $key => $label ) {
			$url = admin_url( 'admin.php?page=mainframe&tab=' . $key );
			echo '<a class="mf-tab' . ( $key === $tab ? ' active' : '' ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
		}
		echo '</nav>';

		echo '<main class="mf-body">';
		$method = 'render_' . $tab;
		if ( method_exists( self::instance(), $method ) ) {
			self::instance()->{$method}();
		}
		echo '</main>';

		echo '<footer class="mf-foot">مین‌فریم ' . esc_html( MF_VERSION ) . ' — ساخته‌شده برای وردپرس، المنتور و تجربه‌ای بی‌نقص.</footer>';
		echo '</div>';
	}

	private static function tabs() {
		return array(
			'guide'       => 'راهنما',
			'widgets'     => 'ویجت‌ها',
			'template'    => 'قالب و صفحه',
			'settings'    => 'تنظیمات',
			'troubleshoot'=> 'عیب‌یابی',
			'dev'         => 'توسعه‌دهندگان',
		);
	}

	private static function message_html( $msg ) {
		$map = array(
			'template_ok'  => array( 'ok', 'قالب «هیرو مین‌فریم» ساخته شد. از بخش «قالب و صفحه» می‌توانید آن را ویرایش یا به صفحه‌ها اضافه کنید.' ),
			'template_fail'=> array( 'err', 'ساخت قالب ناموفق بود. مطمئن شوید المنتور فعال است و پوشهٔ templates در جای خودش هست، سپس دوباره تلاش کنید.' ),
			'rebuild_ok'   => array( 'ok', 'قالب و صفحه با آخرین نسخهٔ JSON بازنویسی شدند. تغییرات قبلی شما در آن‌ها جایگزین شد.' ),
			'page_ok'      => array( 'ok', 'صفحهٔ «هیرو مین‌فریم» ساخته (یا به‌روزرسانی) شد و منتشر است.' ),
			'page_fail'    => array( 'err', 'ساخت صفحه ناموفق بود. دوباره تلاش کنید.' ),
			'settings_ok'  => array( 'ok', 'تنظیمات ذخیره شد.' ),
		);
		if ( ! isset( $map[ $msg ] ) ) {
			return '';
		}
		list( $kind, $text ) = $map[ $msg ];
		return '<div class="mf-msg ' . ( 'ok' === $kind ? 'is-ok' : 'is-err' ) . '">' . esc_html( $text ) . '</div>';
	}

	/* ══════════ بخش راهنما ══════════ */

	private function render_guide() {
		$docs = MF_URL . 'images/docs/';

		echo '<section class="mf-card">';
		echo '<h2>شروع سریع — در پنج گام</h2>';
		echo '<ol class="mf-steps">';
		echo '<li><strong>المنتور را فعال کنید.</strong> این افزونه روی Elementor سوار می‌شود و بدون آن کار نمی‌کند.</li>';
		echo '<li><strong>مین‌فریم را فعال کنید.</strong> بلافاصله قالب آمادهٔ هیرو به «المنتور ← قالب‌ها» اضافه می‌شود و یک صفحهٔ منتشرشده به نام «هیرو مین‌فریم» ساخته می‌شود.</li>';
		echo '<li><strong>صفحه را ببینید.</strong> از پیوند «مشاهدهٔ صفحه» در بخش «قالب و صفحه»، خروجی دقیق دمو را ببینید.</li>';
		echo '<li><strong>ویرایش کنید.</strong> با ویرایشگر المنتور همه چیز قابل تغییر است: ویدیو، متن‌ها، دکمه‌ها، فونت‌ها، رنگ‌ها و چیدمان — بدون نوشتن حتی یک خط کد.</li>';
		echo '<li><strong>در صفحهٔ دیگری استفاده کنید.</strong> در هر برگهٔ المنتور از «المان‌ها ← قالب‌ها ← هیرو مین‌فریم» آن را درج کنید.</li>';
		echo '</ol>';
		echo '</section>';

		echo '<section class="mf-card">';
		echo '<h2>معماری افزونه</h2>';
		echo '<p>افزونه از پنج لایهٔ مستقل تشکیل شده است. هر لایه فقط یک کار می‌کند و از طریق قلاب‌های استاندارد وردپرس و المنتور به بقیه وصل می‌شود؛ به همین دلیل هیچ کدی در المان‌های صفحه قرار نمی‌گیرد و حذف افزونه همه چیز را تمیز برمی‌گرداند.</p>';
		echo '<figure class="mf-figure"><img src="' . esc_url( $docs . 'architecture.svg' ) . '" alt="نمودار معماری افزونه مین‌فریم"><figcaption>لایه‌های افزونه و مسیر داده از فعال‌سازی تا خروجی صفحه</figcaption></figure>';
		echo '<ul class="mf-list">';
		echo '<li><strong>راه‌انداز (mainframe-elementor.php)</strong> — ثبت قلاب‌های فعال‌سازی و بارگذاری کلاس‌ها؛ هیچ منطقی ندارد.</li>';
		echo '<li><strong>هسته (Plugin)</strong> — منوی مدیریت، ذخیرهٔ تنظیمات، دستهٔ «مین‌فریم» در ویرایشگر و ثبت دارایی‌ها.</li>';
	 echo '<li><strong>قالب (Template)</strong> — فایل JSON را به‌صورت یک پست «قالب المنتور» ذخیره می‌کند و می‌تواند صفحهٔ مستقل هم بسازد.</li>';
		echo '<li><strong>ویجت‌ها (widgets)</strong> — پنج ویجت که هرکدام کنترل‌های فارسی، راهنمای تفصیلی و خروجی تمیز HTML دارند.</li>';
		echo '<li><strong>دارایی‌ها (assets)</strong> — یک JS و یک CSS مشترک که فقط وقتی ویجتی در صفحه هست بارگذاری می‌شوند.</li>';
		echo '</ul>';
		echo '</section>';

		echo '<section class="mf-card">';
		echo '<h2>نحوهٔ کار فیلم هیرو (ویدیوی حرکت با ماوس)</h2>';
		echo '<p>قلب دمو یک ویدیوی تمام‌صفحه است که به‌جای پخش خودکار، با حرکت افقی ماوس جلو و عقب می‌رود. این رفتار چیزی نیست جز یک قانون ریاضی ساده که روی «زمان فعلی ویدیو» اعمال می‌شود:</p>';
		echo '<figure class="mf-figure"><img src="' . esc_url( $docs . 'scrub-mechanism.svg' ) . '" alt="نمودار مکانیزم اسکرول ویدیو با ماوس"><figcaption>مسیر کامل یک حرکت ماوس تا نمایش فریم درست</figcaption></figure>';
		echo '<ul class="mf-list">';
		echo '<li><strong>مرحله ۱ — ثبت موقعیت.</strong> با هر رویداد <code>mousemove</code>، موقعیت قبلی X نگه داشته می‌شود و «اختلاف» با موقعیت جدید محاسبه می‌شود.</li>';
		echo '<li><strong>مرحله ۲ — تبدیل به زمان.</strong> <code>offset = (اختلاف ÷ عرض پنجره) × حساسیت × مدت کل ویدیو</code>. یعنی یک بار کشیدن ماوس از چپ تا راست صفحه، دقیقاً «حساسیت» برابر با کل ویدیو را جابه‌جا می‌کند. در دمو حساسیت ۰٫۸ است؛ یعنی ۸۰٪ کل ویدیو برای یک کشیدن کامل.</li>';
		echo '<li><strong>مرحله ۳ — مهار کردن.</strong> مقدار نهایی بین صفر و مدت ویدیو clamp می‌شود تا هرگز از بازهٔ ویدیو بیرون نزند.</li>';
		echo '<li><strong>مرحله ۴ — صف seek.</strong> مرورگر نمی‌تواند ده‌ها seek همزمان را اجرا کند؛ اگر هر حرکت ماوس بلافاصله <code>currentTime</code> را عوض کند، ویدیو می‌لرزد و قفل می‌کند. راه‌حل: فقط یک seek در هر لحظه اجرا می‌شود؛ بقیه در «متغیر هدف» انبار می‌شوند.</li>';
		echo '<li><strong>مرحله ۵ — تکمیل با onSeeked.</strong> به محض پایان هر seek، اگر هدف از آنچه نمایش داده شده جلوتر/عقب‌تر باشد (اختلاف بیش از ۰٫۰۵ ثانیه)، seek بعدی همان لحظه اجرا می‌شود. نتیجه: حرکت روان، بدون از دست رفتن حتی یک حرکت ماوس.</li>';
		echo '</ul>';
		echo '<div class="mf-note"><strong>نکته</strong> — ویدیو بدون autoplay و بدون صدا لود می‌شود (<code>preload=auto</code>) تا اولین حرکت ماوس بلافاصله پاسخ بگیرد. این رفتار در ویجت «ویدیوی حرکت با ماوس» با کنترل «حساسیت» قابل تغییر است.</div>';
		echo '</section>';

		echo '<section class="mf-card">';
		echo '<h2>ساختار قالب آماده — کانتینرها بدون کد اضافه</h2>';
		echo '<p>قالب JSON دقیقاً با همان منطقی ساخته شده که یک طراح در ویرایشگر المنتور می‌سازد: کانتینر ریشه با ارتفاع یک صفحه‌نمایش، سه فرزند (ویدیو، نوار بالا، بلوک محتوا) و یک کانتینر داخلی با عرض حداکثر ۵۷۶ پیکسل. هیچ المان «HTML سفارشی» و هیچ کد JS/CSS درون صفحه وجود ندارد؛ همه رفتارها از دل ویجت‌ها می‌آیند.</p>';
		echo '<figure class="mf-figure"><img src="' . esc_url( $docs . 'template-structure.svg' ) . '" alt="ساختار درختی کانتینرها و ویجت‌های قالب"><figcaption>درخت کانتینرها با تنظیمات کلیدی هر گره</figcaption></figure>';
		echo '</section>';

		echo '<section class="mf-card">';
		echo '<h2>آناتومی هیرو — کدام ویجت، کدام بخش</h2>';
		echo '<p>پنج بخش قابل ویرایش دمو، پنج ویجت اختصاصی دارند. در قالب آماده هرکدام سر جای خودشان چیده شده‌اند؛ در صفحه‌های دیگر می‌توانید هر ویجت را جداگانه هم استفاده کنید.</p>';
		echo '<figure class="mf-figure"><img src="' . esc_url( $docs . 'hero-anatomy.svg' ) . '" alt="نقشهٔ بخش‌های هیرو با شماره و نام ویجت"><figcaption>نقشهٔ هیرو: هر شماره به ویجت معادل خودش اشاره دارد</figcaption></figure>';
		echo '</section>';
	}

	/* ══════════ بخش ویجت‌ها ══════════ */

	private function render_widgets() {
		echo '<section class="mf-card">';
		echo '<h2>ویجت‌های اختصاصی مین‌فریم</h2>';
		echo '<p>همهٔ ویجت‌ها در دستهٔ «مین‌فریم» در ویرایشگر المنتور پیدا می‌شوند. هر کنترل زیر ویرایشگر راهنمای تفصیلی خودش را دارد (در پنل تنظیمات ویجت، زیر هر فیلد). جدول‌ها همان راهنما را یک‌جا جمع کرده‌اند.</p>';
		echo '</section>';

		foreach ( self::widgets_docs() as $w ) {
			echo '<section class="mf-card mf-widget-card">';
			echo '<div class="mf-widget-head"><h2>' . esc_html( $w['title'] ) . '</h2><code class="mf-code">' . esc_html( $w['id'] ) . '</code></div>';
			echo '<p class="mf-lead">' . $w['lead'] . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<div class="mf-grid2">';
			echo '<div><h3>کاربرد</h3><ul class="mf-list"><li>' . implode( '</li><li>', array_map( 'esc_html', $w['uses'] ) ) . '</li></ul></div>';
			echo '<div><h3>نکات مهم</h3><ul class="mf-list"><li>' . implode( '</li><li>', array_map( 'esc_html', $w['tips'] ) ) . '</li></ul></div>';
			echo '</div>';
			echo '<h3>کنترل‌ها</h3>';
			echo '<table class="mf-table"><thead><tr><th style="width:22%">کنترل</th><th style="width:26%">پیش‌فرض (دمو)</th><th>راهنما</th></tr></thead><tbody>';
			foreach ( $w['controls'] as $c ) {
				echo '<tr><td><strong>' . esc_html( $c[0] ) . '</strong></td><td><code>' . esc_html( $c[1] ) . '</code></td><td>' . esc_html( $c[2] ) . '</td></tr>';
			}
			echo '</tbody></table>';
			echo '</section>';
		}
	}

	private static function widgets_docs() {
		return array(
			array(
				'title' => 'ویدیوی حرکت با ماوس',
				'id'    => 'mainframe-scrub-video',
				'lead'  => 'ویدیویی که به‌جای پخش خودکار، با حرکت افقی ماوس جلو و عقب می‌رود. هم پس‌زمینهٔ تمام‌صفحهٔ دمو است، هم می‌تواند «کاراکتر» یا هر ویدیوی دیگری داخل محتوا باشد: کافی است ویدیو را آپلود کنید تا افزونه همه چیز (بافر، صف seek، حساسیت، قاب‌بندی) را خودکار و دقیقاً مثل پیش‌فرض انجام دهد.',
				'uses'  => array(
					'پس‌زمینهٔ تمام‌صفحهٔ هیرو مثل دموی Mainframe',
					'کاراکتر یا محصول ویدیویی که کاربر با ماوس آن را ورق می‌زند',
					'هر ویدیوی کوتاه تعاملی داخل یک کانتینر المنتور',
				),
				'tips'  => array(
					'حالت «تمام‌صفحه ثابت» را روشن بگذارید تا دقیقاً خروجی دمو را داشته باشید؛ برای کاراکتر داخل محتوا آن را خاموش کنید.',
					'حساسیت ۰٫۸ یعنی یک کشیدن کامل ماوس از چپ تا راست، ۸۰٪ ویدیو را جابه‌جا می‌کند. برای ویدیوهای بلند ۰٫۴ تا ۰٫۶ بهتر است.',
					'ویدیو را با فرمت MP4 و H.264 آپلود کنید تا همهٔ مرورگرها پشتیبانی کنند.',
					'اگر ویدیو از سرور خودتان نیست، «لینک ویدیو» را هم می‌توانید مستقیم وارد کنید.',
				),
				'controls' => array(
					array( 'ویدیو (آپلود)', 'خالی', 'فایل ویدیو را از کتابخانهٔ رسانه انتخاب می‌کنید. اولویت با این گزینه است.' ),
					array( 'لینک ویدیو', 'لینک دمو', 'برای ویدیوی میزبانی‌شده در جای دیگر؛ اگر «ویدیو (آپلود)» خالی باشد استفاده می‌شود.' ),
					array( 'حساسیت', '0.8', 'ضریب جابه‌جایی؛ هرچه بزرگ‌تر، سریع‌تر. بازهٔ پیشنهادی ۰٫۲ تا ۲.' ),
					array( 'موقعیت قاب (object-position)', '70% center', 'کدام بخش ویدیو در قاب دیده شود؛ مثل دمو ۷۰٪ از افق.' ),
					array( 'تمام‌صفحه ثابت', 'روشن', 'روشن: fixed و تمام‌صفحه (دمو). خاموش: ویدیو داخل جریان محتوا با ارتفاع دلخواه.' ),
					array( 'پخش خودکار', 'خاموش', 'در دمو خاموش است تا scrub کامل در اختیار کاربر باشد.' ),
					array( 'بی‌صدا', 'روشن', 'اجبار مرورگرها برای پخش بدون صدا.' ),
					array( 'حلقه (تکرار)', 'خاموش', 'تکرار ویدیو در پایان.' ),
					array( 'پیش‌بارگذاری', 'auto', 'auto برای آماده بودن فریم اول مثل دمو؛ metadata سبک‌تر است.' ),
					array( 'کاور (پوستر)', 'خالی', 'تصویر ثابت تا لود شدن ویدیو.' ),
					array( 'لایه (z-index)', '0', 'در حالت ثابت، پشت محتوا؛ عدد بزرگ‌تر جلوتر می‌آید.' ),
					array( 'ارتفاع (حالت جعبه‌ای)', '420px', 'فقط وقتی تمام‌صفحه خاموش باشد.' ),
				),
			),
			array(
				'title' => 'تایپ‌نویسر',
				'id'    => 'mainframe-typewriter',
				'lead'  => 'متنی که پس از یک مکث کوتاه، حرف‌به‌حرف تایپ می‌شود و بصرِ چشمک‌زن تا پایان کنار آن می‌ماند. منطق دقیقاً مثل هوک استفاده‌شده در دمو است: تایمر شروع، سپس فاصلهٔ بین حروف، و پایان تمیز.',
				'uses'  => array(
					'جملهٔ معرفی هیرو مثل دمو',
					'هر شعار یا CTA که می‌خواهید با تایپ وارد شود',
					'جایگزین تیترهای ثابت در بخش‌های مختلف سایت',
				),
				'tips'  => array(
					'سرعت ۳۸ میلی‌ثانیه برای جمله‌های انگلیسی خوانا است؛ برای فارسی ۴۵ تا ۵۵ بهتر است.',
					'«حداقل ارتفاع» را با تعداد خط‌های نهایی تنظیم کنید تا صفحه هنگام تایپ پرش نکند.',
					'اگر متن را تغییر دهید، در ویرایشگر المنتور پیش‌نمایش همان لحظه از اول تایپ می‌شود.',
				),
				'controls' => array(
					array( 'متن', 'جملهٔ دمو', 'متنی که تایپ می‌شود؛ ساده و بدون HTML.' ),
					array( 'سرعت (میلی‌ثانیه)', '38', 'فاصله بین هر حرف.' ),
					array( 'تاخیر شروع (میلی‌ثانیه)', '600', 'مکث قبل از شروع تایپ.' ),
					array( 'اندازهٔ فونت', 'clamp(18px, 4vw, 26px)', 'با clamp روی همهٔ صفحه‌ها خوانا می‌ماند؛ مثل دمو.' ),
					array( 'ارتفاع خط', '1.35', 'همان line-height دمو.' ),
					array( 'حداقل ارتفاع', '54px', 'جلوگیری از پرش چیدمان حین تایپ.' ),
					array( 'رنگ متن', 'سفید', 'رنگ اصلی جمله.' ),
					array( 'ضخامت فونت', '400', 'وزن فونت متن.' ),
					array( 'بصر چشمک‌زن', 'روشن', 'کاراکتر | کنار متن؛ پس از پایان تایپ پنهان می‌شود.' ),
					array( 'رنگ بصر', 'سفید', 'رنگ بصرِ چشمک‌زن.' ),
					array( 'جهت متن', 'پیش‌فرض قالب', 'برای جمله‌های فارسی «راست به چپ» را انتخاب کنید.' ),
				),
			),
			array(
				'title' => 'برچسب مقدمه (تار)',
				'id'    => 'mainframe-intro-label',
				'lead'  => 'آن دو خط اول هیرو که عمداً تار شده‌اند تا چشم اول به متن تایپ‌شده برود. متن، میزان تاری و اندازهٔ فونت همگی در دسترس شماست.',
				'uses'  => array(
					'خط معرفی بالای متن اصلی هیرو',
					'هر متن فرعی که می‌خواهید حضور داشته اما دیده نشود',
				),
				'tips'  => array(
					'تاری ۴ پیکسل همان مقدار دمو است؛ بالای ۶ دیگر متن خوانا نمی‌ماند.',
					'این برچسب غیرفعال انتخاب است (select-none) تا با کلیک اتفاقی انتخاب نشود.',
				),
				'controls' => array(
					array( 'خط اول', 'Hey there, meet A.R.I.A,', 'خط اول برچسب.' ),
					array( 'خط دوم', "Mainframe's Adaptive…", 'خط دوم؛ با شکستن خط از خط اول جدا می‌شود.' ),
					array( 'میزان تاری (پیکسل)', '4', 'شعاع blur؛ ۴ مثل دمو.' ),
					array( 'اندازهٔ فونت', 'clamp(18px, 4vw, 26px)', 'مقیاس‌پذیر مثل دمو.' ),
					array( 'ارتفاع خط', '1.3', 'فاصلهٔ بین دو خط.' ),
					array( 'رنگ متن', 'سفید', 'رنگ برچسب.' ),
					array( 'ضخامت فونت', '400', 'وزن فونت.' ),
					array( 'جهت متن', 'پیش‌فرض قالب', 'برای فارسی «راست به چپ».' ),
				),
			),
			array(
				'title' => 'دکمه‌های قرصی',
				'id'    => 'mainframe-pills',
				'lead'  => 'ردیف دکمه‌های گرد دمو: چهار دکمهٔ سفید و یک دکمهٔ خط‌دار که ایمیل را با یک کلیک کپی می‌کند. دکمه‌ها ۴۰۰ میلی‌ثانیه پس از لود با محو شدن وارد می‌شوند — مستقل از تایپ‌نویسر.',
				'uses'  => array(
					'یک دسته دکمهٔ اصلی هیرو',
					'هر گروه CTA با ظاهر قرصی',
					'دکمهٔ کپی ایمیل یا کد تخفیف',
				),
				'tips'  => array(
					'برای هر قرص می‌توانید نوع «لینک» یا «کپی ایمیل» را جدا انتخاب کنید.',
					'فاصلهٔ عمودی ردیف‌ها و فاصلهٔ کناری دکمه‌ها دقیقاً مثل دمو (۴ و ۰٫۲em) پیاده شده است.',
					'اگر دکمه‌ها ظاهر نشدند، «تاخیر ورود» را چک کنید؛ مقدار ۰ یعنی ورود فوری.',
				),
				'controls' => array(
					array( 'آیتم‌ها (تکرارشونده)', '۵ آیتم دمو', 'برچسب، نوع (لینک/کپی ایمیل)، پیوند یا ایمیل و ظاهر هر قرص.' ),
					array( 'تاخیر ورود (میلی‌ثانیه)', '400', 'زمان ظاهر شدن ردیف پس از لود صفحه.' ),
					array( 'مدت محو (میلی‌ثانیه)', '400', 'مدت انیمیشن ورود (opacity + جابه‌جایی ۸ پیکسل).' ),
					array( 'اندازهٔ فونت', 'خودکار (۱۳ تا ۱۵)', 'خالی = مقیاس دقیق دمو؛ مقدار دلخواه جایگزین می‌کند.' ),
					array( 'رنگ پس‌زمینهٔ سفید', 'سفید', 'پس‌زمینهٔ قرص‌های پر.' ),
					array( 'رنگ متن سفید', 'مشکی', 'متن قرص‌های پر.' ),
					array( 'هاور سفید (پس‌زمینه)', 'مشکی', 'رفتن ماوس روی قرص پر.' ),
					array( 'هاور سفید (متن)', 'سفید', 'متن هاور قرص پر.' ),
					array( 'رنگ حاشیهٔ خط‌دار', 'سفید', 'حاشیهٔ قرص خط‌دار.' ),
					array( 'رنگ متن خط‌دار', 'سفید', 'متن قرص خط‌دار.' ),
					array( 'هاور خط‌دار (پس‌زمینه)', 'سفید', 'پر شدن قرص خط‌دار هنگام هاور.' ),
					array( 'هاور خط‌دار (متن)', 'مشکی', 'متن هاور قرص خط‌دار.' ),
					array( 'آیکون کپی', 'روشن', 'آیکون دو مستطیل ۱۲×۱۲ کنار ایمیل.' ),
				),
			),
			array(
				'title' => 'نوار بالا (ناوبری)',
				'id'    => 'mainframe-navbar',
				'lead'  => 'هدر شناور دمو: لوگو با فونت تیتر، نشانهٔ ✳︎، لینک‌های وسطی جداشده با کاما، دعوت‌نامهٔ زیرخط‌دار و منوی همبرگری موبایل با اورلی تیره و تار. همه در یک ویجت با رفتار باز/بسته شدن آماده.',
				'uses'  => array(
					'هدر ثابت بالای هیرو مثل دمو',
					'نوار بالای هر صفحه با ظاهر مینیمال',
					'منوی موبایل اورلی بدون افزونهٔ منوی جداگانه',
				),
				'tips'  => array(
					'در حالت «ثابت»، ویجت از جریان محتوا خارج می‌شود و روی ویدیو می‌نشیند — دقیقاً مثل دمو.',
					'نقطهٔ شکست ۷۶۸ پیکسل همان breakpoint دمو است؛ زیر آن همبرگر و بالای آن لینک‌ها.',
					'لینک‌ها در ویرایشگر هم قابل افزودن و حذف‌اند؛ جداکننده هم قابل تغییر است.',
				),
				'controls' => array(
					array( 'متن لوگو', 'Mainframe®', 'متن لوگو با فونت تیتر (--mf-heading).' ),
					array( 'پیوند لوگو', '#', 'مقصد کلیک روی لوگو.' ),
					array( 'اندازهٔ لوگو', '26 / موبایل 21', 'واحد پیکسل، با کنترل واکنش‌گرا.' ),
					array( 'نشانه', '✳︎', 'کاراکتر کنار لوگو؛ با «نمایش نشانه» خاموش/روشن می‌شود.' ),
					array( 'اندازهٔ نشانه', '30 / موبایل 25', 'واحد پیکسل.' ),
					array( 'لینک‌ها (تکرارشونده)', 'Labs، Studio، Openings، Shop', 'متن و پیوند هر گزینهٔ منو.' ),
					array( 'جداکنندهٔ لینک‌ها', '، ', 'دقیقاً مثل دمو که لینک‌ها با کاما از هم جدا می‌شوند.' ),
					array( 'دعوت‌نامه (CTA)', 'Get in touch', 'متن زیرخط‌دار سمت چپ (راست در RTL).' ),
					array( 'پیوند CTA', '#', 'مقصد دعوت‌نامه.' ),
					array( 'اندازهٔ لینک‌ها', '23', 'واحد پیکسل برای لینک‌ها و CTA.' ),
					array( 'رنگ متن', 'سفید', 'رنگ همهٔ متن‌های نوار.' ),
					array( 'موقعیت', 'ثابت', 'ثابت = شناور روی ویدیو؛ مطلق یا عادی بسته به چیدمان شما.' ),
					array( 'لایه (z-index)', '10', 'بالای اورلی (۹) و محتوا (۱) مثل دمو.' ),
					array( 'حاشیهٔ داخلی', '16px 20px / دسکتاپ 20px 32px', 'padding نوار؛ با کنترل واکنش‌گرا.' ),
					array( 'شفافیت هاور لینک', '60', 'درصد شفافیت لینک هنگام هاور (دمو: ۶۰٪).' ),
					array( 'نقطهٔ شکست همبرگر', '768px', 'زیر این عرض، منوی همبرگری فعال می‌شود.' ),
					array( 'پس‌زمینهٔ اورلی', 'rgba(0,0,0,0.9)', 'پس‌زمینهٔ منوی موبایل.' ),
					array( 'تاری اورلی (پیکسل)', '12', 'backdrop-blur اورلی موبایل.' ),
					array( 'اندازهٔ لینک‌های اورلی', '32', 'اندازهٔ فونت لینک‌ها در منوی موبایل.' ),
				),
			),
		);
	}

	/* ══════════ بخش قالب و صفحه ══════════ */

	private function render_template() {
		$status = Mainframe_Template::status();

		echo '<section class="mf-card">';
		echo '<h2>وضعیت فعلی</h2>';
		echo '<div class="mf-status-grid">';

		echo '<div class="mf-status"><span class="mf-dot ' . ( $status['json_ok'] ? 'on' : 'off' ) . '"></span><div><strong>فایل قالب (JSON)</strong><span>' . ( $status['json_ok'] ? 'سالم و آمادهٔ ورود' : 'پیدا نشد — پوشهٔ templates را بررسی کنید' ) . '</span></div></div>';

		echo '<div class="mf-status"><span class="mf-dot ' . ( $status['template_id'] ? 'on' : 'off' ) . '"></span><div><strong>قالب در المنتور</strong><span>' .
			( $status['template_id']
				? 'ساخته شد — شناسهٔ پست #' . (int) $status['template_id']
				: 'هنوز ساخته نشده' ) .
			'</span></div></div>';

		echo '<div class="mf-status"><span class="mf-dot ' . ( $status['page_id'] ? 'on' : 'off' ) . '"></span><div><strong>صفحهٔ «هیرو مین‌فریم»</strong><span>' .
			( $status['page_id']
				? 'منتشر است — شناسهٔ پست #' . (int) $status['page_id']
				: 'هنوز ساخته نشده' ) .
			'</span></div></div>';
		echo '</div>';

		echo '</section>';

		echo '<section class="mf-card">';
		echo '<h2>کارها</h2>';
		echo '<div class="mf-actions">';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="mf-inline">';
		echo '<input type="hidden" name="action" value="mf_create_template">';
		wp_nonce_field( 'mf_create_template' );
		echo '<button class="mf-btn" type="submit">' . ( $status['template_id'] ? 'به‌روزرسانی وضعیت قالب' : 'ساخت قالب در المنتور' ) . '</button>';
		echo '</form>';

		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="mf-inline">';
		echo '<input type="hidden" name="action" value="mf_force_rebuild">';
		wp_nonce_field( 'mf_force_rebuild' );
		echo '<button class="mf-btn mf-btn-ghost" type="submit">بازنویسی با آخرین نسخهٔ افزونه</button>';
		echo '</form>';

		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="mf-inline">';
		echo '<input type="hidden" name="action" value="mf_create_page">';
		wp_nonce_field( 'mf_create_page' );
		echo '<button class="mf-btn mf-btn-ghost" type="submit">' . ( $status['page_id'] ? 'به‌روزرسانی صفحه' : 'ساخت صفحهٔ هیرو' ) . '</button>';
		echo '</form>';

		if ( $status['template_id'] ) {
			echo '<a class="mf-btn mf-btn-ghost" href="' . esc_url( admin_url( 'post.php?post=' . $status['template_id'] . '&action=elementor' ) ) . '">ویرایش قالب با المنتور</a>';
		}
		if ( $status['page_id'] ) {
			echo '<a class="mf-btn mf-btn-ghost" href="' . esc_url( get_permalink( $status['page_id'] ) ) . '" target="_blank" rel="noopener">مشاهدهٔ صفحه</a>';
			echo '<a class="mf-btn mf-btn-ghost" href="' . esc_url( admin_url( 'post.php?post=' . $status['page_id'] . '&action=elementor' ) ) . '">ویرایش صفحه با المنتور</a>';
		}
		echo '</div>';
		echo '<div class="mf-note"><strong>بازنویسی</strong> — دکمهٔ «بازنویسی با آخرین نسخهٔ افزونه» تغییرات شما در قالب/صفحه را با ساختار پیش‌فرض افزونه جایگزین می‌کند. فقط وقتی بخواهید به حالت اولیه برگردید از آن استفاده کنید.</div>';
		echo '</section>';

		echo '<section class="mf-card">';
		echo '<h2>درج قالب در یک صفحهٔ دیگر</h2>';
		echo '<ol class="mf-steps">';
		echo '<li>یک برگهٔ جدید بسازید و با المنتور ویرایش کنید.</li>';
		echo '<li>از نوار کناری «المان‌ها» گزینهٔ «قالب‌ها» را باز کنید.</li>';
		echo '<li>«هیرو مین‌فریم (قالب آماده)» را پیدا و درج کنید.</li>';
		echo '<li>برای دیدن دقیق هیرو، در تنظیمات برگه «مخفی کردن عنوان» را روشن و قالب سایت را بدون هدر/فوتر انتخاب کنید (قالب Hello Elementor ایده‌آل است).</li>';
		echo '</ol>';
		echo '</section>';
	}

	/* ══════════ بخش تنظیمات ══════════ */

	private function render_settings() {
		$s = Mainframe_Plugin::get_settings();

		echo '<section class="mf-card">';
		echo '<h2>تنظیمات افزونه</h2>';
		echo '<p>اینجا فونت‌ها و ایمیل پیش‌فرض را کنترل می‌کنید. پیش‌فرض‌ها همان مقادیر دموی Mainframe هستند؛ اگر دست نزنید دقیقاً همان چیزی را می‌بینید که در پیش‌نمایش دیدید.</p>';

		echo '<form method="post" action="options.php" class="mf-form">';
		settings_fields( 'mf_settings_group' );

		echo '<label class="mf-check"><input type="checkbox" name="' . esc_html( Mainframe_Plugin::OPT ) . '[fonts_enabled]" value="1" ' . checked( '1', $s['fonts_enabled'], false ) . '> <span><strong>بارگذاری فونت‌های Helvetica Now</strong> — دو سبک‌برگهٔ تیتر و متن از آدرس‌های زیر در همهٔ صفحه‌ها لود شوند.</span></label>';

		echo '<div class="mf-field"><label>آدرس فونت تیتر (Heading)</label><input type="url" dir="ltr" class="regular-text code" name="' . esc_html( Mainframe_Plugin::OPT ) . '[heading_url]" value="' . esc_attr( $s['heading_url'] ) . '"><p class="description">فونتی که فقط لوگو و تیترها استفاده می‌کنند (<code>--mf-heading</code>).</p></div>';

		echo '<div class="mf-field"><label>نام خانوادهٔ فونت تیتر</label><input type="text" dir="ltr" class="regular-text code" name="' . esc_html( Mainframe_Plugin::OPT ) . '[heading_family]" value="' . esc_attr( $s['heading_family'] ) . '"><p class="description">مقدار CSS برای <code>font-family</code>؛ همراه fallbackها.</p></div>';

		echo '<div class="mf-field"><label>آدرس فونت متن (Body)</label><input type="url" dir="ltr" class="regular-text code" name="' . esc_html( Mainframe_Plugin::OPT ) . '[body_url]" value="' . esc_attr( $s['body_url'] ) . '"><p class="description">فونت پیش‌فرض بدنهٔ صفحه (<code>--mf-body</code>).</p></div>';

		echo '<div class="mf-field"><label>نام خانوادهٔ فونت متن</label><input type="text" dir="ltr" class="regular-text code" name="' . esc_html( Mainframe_Plugin::OPT ) . '[body_family]" value="' . esc_attr( $s['body_family'] ) . '"></div>';

		echo '<div class="mf-field"><label>ایمیل پیش‌فرض دکمهٔ کپی</label><input type="email" dir="ltr" class="regular-text" name="' . esc_html( Mainframe_Plugin::OPT ) . '[default_email]" value="' . esc_attr( $s['default_email'] ) . '"><p class="description">پیش‌فرض ایمیلی که در دکمهٔ «Reach us» قرار می‌گیرد. برای هر ویجت جداگانه هم قابل تغییر است.</p></div>';

		echo '<p class="submit"><button type="submit" class="mf-btn">ذخیرهٔ تنظیمات</button></p>';
		echo '</form>';
		echo '</section>';
	}

	/* ══════════ بخش عیب‌یابی ══════════ */

	private function render_troubleshoot() {
		echo '<section class="mf-card">';
		echo '<h2>عیب‌یابی — رایج‌ترین مشکلات و راه‌حل‌ها</h2>';
		echo '<dl class="mf-faq">';

		$items = array(
			array(
				'ویدیو با ماوس جلو و عقب نمی‌شود.',
				'سه مورد را چک کنید: ۱) ویدیو لود شده باشد (آیکون بارگذاری مرورگر تمام شده باشد) — scrub فقط بعد از آماده شدن duration ممکن است. ۲) در حالت «تمام‌صفحه ثابت» ویدیو پشت المان‌های دیگر نرفته باشد (z-index). ۳) ویدیو واقعاً مدت‌دار باشد؛ فایل خراب یا فقط-تصویری duration ندارد.',
			),
			array(
				'ویدیو اصلاً نمایش داده نمی‌شود.',
				'آدرس ویدیو را در حالت LTR کپی-پیست کنید و مطمئن شوید با https:// شروع می‌شود و مرورگر شما MP4/H.264 را پشتیبانی می‌کند. اگر فایل را آپلود کردید، در پرونده‌های رسانه لینک مستقیم را چک کنید. همچنین ممکن است تم سایت ویدیو را با <code>display:none</code> پنهان کرده باشد — در ویرایشگر المنتور یک بار ذخیره کنید تا CSS بازسازی شود.',
			),
			array(
				'ویجت‌های مین‌فریم در ویرایشگر دیده نمی‌شوند.',
				'المنتور باید فعال باشد. افزونه را یک بار غیرفعال و دوباره فعال کنید؛ اگر باز هم نبود، از «ابزارها ← وضعیت سیستم» مطمئن شوید خطاي PHP وجود ندارد.',
			),
			array(
				'قالب «هیرو مین‌فریم» در قالب‌های المنتور نیست.',
				'به «قالب و صفحه» بروید و «ساخت قالب در المنتور» را بزنید. اگر باز هم نبود، «بازنویسی با آخرین نسخه» را امتحان کنید و در ابزارها ← وضعیت سیستم به دنبال خطای مربوط به elementor_library بگردید.',
			),
			array(
				'بالای هیرو هدر/فوتر تم سایت افتاده است.',
				'این مشکهای به افزونه نیست؛ خروجی دقیق دمو فقط روی برگه‌ای بدون هدر/فوتر به دست می‌آید. راه‌حل: قالب Hello Elementor را فعال کنید، یا در Theme Builder المنتور هدر/فوتر را برای این برگه مخفی کنید، یا در تنظیمات برگه «مخفی کردن عنوان» را روشن بگذارید.',
			),
			array(
				'در موبایل منوی همبرگر باز نمی‌شود.',
				'نقطهٔ شکست همبرگر پیش‌فرض ۷۶۸ پیکسل است؛ زیر آن فقط همبرگر نمایش دارد. اگر تم شما برای موبایل عرض متفاوتی دارد، «نقطهٔ شکست همبرگر» را در تنظیمات ویجت عوض کنید.',
			),
			array(
				'کلیک روی ایمیل چیزی کپی نمی‌کند.',
				'کپی با Clipboard API فقط در https یا localhost کار می‌کند. سایت را با SSL باز کنید؛ در حالت http مرورگرها دسترسی را رد می‌کنند.',
			),
			array(
				'فونت‌ها تغییر نمی‌کنند.',
				'در «تنظیمات» مطمئن شوید «بارگذاری فونت‌ها» روشن است و آدرس سبک‌برگه‌ها <code>https://</code> دارد. پس از تغییر، کش مرورگر و هر افزونهٔ کش را خالی کنید.',
			),
			array(
				'تایپ‌نویسر در ویرایشگر از اول شروع می‌کند.',
				'طبیعی است؛ پیش‌نمایش زنده هر بار ویجت را از نو رندر می‌کند. خروجی نهایی صفحه فقط یک بار تایپ می‌کند.',
			),
			array(
				'پس از حذف افزونه، صفحه خراب می‌شود.',
				'المان‌های ویجت‌های مین‌فریم حذف می‌شوند (کانتینرها می‌مانند). قبل از حذف، صفحه را ذخیره‌نگهداری کنید یا افزونه را نگه دارید؛ قالب‌های المنتور هم تا وقتی افزونه هست سالم‌اند.',
			),
		);

		foreach ( $items as $item ) {
			echo '<dt>' . esc_html( $item[0] ) . '</dt><dd>' . esc_html( $item[1] ) . '</dd>';
		}
		echo '</dl>';
		echo '</section>';
	}

	/* ══════════ بخش توسعه‌دهندگان ══════════ */

	private function render_dev() {
		echo '<section class="mf-card">';
		echo '<h2>نقشهٔ فایل‌ها</h2>';
		echo '<table class="mf-table"><thead><tr><th style="width:34%">فایل</th><th>وظیفه</th></tr></thead><tbody>';
		$rows = array(
			array( 'mainframe-elementor.php', 'سربرگ، ثابت‌ها، راه‌انداز و قلاب فعال‌سازی' ),
			array( 'includes/class-mainframe-plugin.php', 'منو، تنظیمات، دستهٔ ویجت‌ها، ثبت دارایی‌ها' ),
			array( 'includes/class-mainframe-template.php', 'ورود JSON به elementor_library و ساخت صفحه' ),
			array( 'includes/class-mainframe-help.php', 'همین صفحهٔ راهنما' ),
			array( 'includes/class-mainframe-fonts.php', 'بارگذاری فونت‌ها و متغیرهای --mf-heading / --mf-body' ),
			array( 'widgets/class-widget-*.php', 'پنج ویجت؛ هرکدام کنترل‌های فارسی + render تمیز' ),
			array( 'assets/js/mainframe-frontend.js', 'رفتار scrub، تایپ‌نویسر، کپی ایمیل، منو — یکجا و بدون وابستگی' ),
			array( 'assets/css/mainframe-frontend.css', 'استایل ویجت‌ها با همان مقادیر دمو' ),
			array( 'templates/mainframe-hero.json', 'ساختار کانتینرهای قالب (ورودی به المنتور)' ),
			array( 'images/docs/*.svg', 'گرافیک‌های همین راهنما' ),
		);
		foreach ( $rows as $r ) {
			echo '<tr><td><code dir="ltr">' . esc_html( $r[0] ) . '</code></td><td>' . esc_html( $r[1] ) . '</td></tr>';
		}
		echo '</tbody></table>';
		echo '</section>';

		echo '<section class="mf-card">';
		echo '<h2>قلاب‌ها و نقاط توسعه</h2>';
		echo '<table class="mf-table"><thead><tr><th style="width:40%">قلاب</th><th>کاری که می‌کند</th></tr></thead><tbody>';
		$hooks = array(
			array( 'plugins_loaded → mf_bootstrap', 'راه‌انداز هسته در همهٔ حالت‌ها' ),
			array( 'elementor/loaded', 'بارگذاری پنج کلاس ویجت فقط پس از آماده شدن المنتور' ),
			array( 'elementor/elements/categories_registered', 'افزودن دستهٔ «مین‌فریم» به ویرایشگر' ),
			array( 'wp_enqueue_scripts (اولویت 5 و 6)', 'ثبت دارایی‌های ویجت‌ها و بارگذاری فونت‌ها' ),
			array( 'admin_post_mf_create_template / mf_force_rebuild / mf_create_page', 'دکمه‌های بخش «قالب و صفحه» با nonce اختصاصی' ),
			array( 'گزینهٔ mf_settings', 'ذخیرهٔ تنظیمات فونت و ایمیل با sanitize استاندارد' ),
			array( 'گزینهٔ mf_template_pending', 'پرچم یک‌بارمصرف ورود قالب پس از فعال‌سازی' ),
		);
		foreach ( $hooks as $h ) {
			echo '<tr><td><code dir="ltr">' . esc_html( $h[0] ) . '</code></td><td>' . esc_html( $h[1] ) . '</td></tr>';
		}
		echo '</tbody></table>';
		echo '</section>';

		echo '<section class="mf-card">';
		echo '<h2>افزودن ویجت جدید در آینده</h2>';
		echo '<ol class="mf-steps">';
		echo '<li>یک کلاس در پوشهٔ <code>widgets</code> بسازید که از <code>Elementor\Widget_Base</code> ارث ببرد و متد <code>get_name</code> یکتا داشته باشد.</li>';
		echo '<li>نام فایل را به فهرست در <code>Mainframe_Plugin::on_elementor_loaded</code> اضافه کنید.</li>';
		echo '<li>در <code>register_controls</code> برچسب‌ها و راهنماها را فارسی بنویسید؛ در <code>render</code> فقط HTML تمیز با کلاس‌های <code>mf-*</code> خروجی دهید.</li>';
		echo '<li>رفتار را در <code>assets/js/mainframe-frontend.js</code> با الگوی «init-idempotent» (علامت‌گذاری با <code>data-mf-ready</code>) اضافه کنید تا در ویرایشگر چندبار اجرا نشود.</li>';
		echo '<li>اگر ویجت باید در قالب آماده باشد، نام آن را در <code>templates/mainframe-hero.json</code> بگذارید و با دکمهٔ «بازنویسی» در صفحهٔ قالب، JSON را ورود دهید.</li>';
		echo '</ol>';
		echo '</section>';
	}
}
