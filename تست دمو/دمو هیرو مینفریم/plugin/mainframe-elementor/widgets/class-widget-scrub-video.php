<?php
/**
 * ویجت: ویدیوی حرکت با ماوس (mouse-scrub)
 *
 * رفتار: ویدیو با حرکت افقی ماوس جلو/عقب می‌رود.
 * قانون: offset = (delta ÷ عرض پنجره) × حساسیت × duration
 * صف seek: فقط یک seek همزمان؛ با onSeeked در صورت جا افتادن هدف، seek بعدی.
 *
 * حالت‌های قرارگیری (position_mode):
 *  - fill  = پرکردن کانتینر (بنر هیرو — پیش‌فرض)؛ ریشهٔ ویجت absolute می‌شود
 *  - box   = جعبه‌ای در جریان محتوا با ارتفاع دلخواه
 *  - fixed = تمام‌صفحه ثابت (کل پنجره را می‌پوشاند)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget_Mainframe_Scrub_Video extends \Elementor\Widget_Base {

	public function get_name() {
		return 'mainframe-scrub-video';
	}

	public function get_title() {
		return 'ویدیوی حرکت با ماوس';
	}

	public function get_icon() {
		return 'eicon-video-played';
	}

	public function get_categories() {
		return array( 'mainframe' );
	}

	public function get_keywords() {
		return array( 'ویدیو', 'ماوس', 'اسکرول', 'کاراکتر', 'mousemove', 'scrub', 'بنر', 'هیرو' );
	}

	public function get_script_depends() {
		return array( 'mainframe-frontend' );
	}

	public function get_style_depends() {
		return array( 'mainframe-frontend' );
	}

	/**
	 * حالت قرارگیری — با پشتیبانی از دادهٔ ذخیره‌شدهٔ نسخه‌های قبلی (fixed_mode).
	 */
	private function mf_position_mode() {
		// دادهٔ خام ذخیره‌شده (بدون اعمال پیش‌فرض‌ها) — برای تشخیص نسخهٔ قدیمی.
		$raw = array();
		if ( method_exists( $this, 'get_data' ) ) {
			$raw = $this->get_data( 'settings' );
		}
		if ( is_array( $raw ) && array_key_exists( 'fixed_mode', $raw ) && ! array_key_exists( 'position_mode', $raw ) ) {
			return 'yes' === $raw['fixed_mode'] ? 'fixed' : 'box';
		}
		$settings = $this->get_settings();
		$mode     = isset( $settings['position_mode'] ) && $settings['position_mode'] ? $settings['position_mode'] : 'fill';
		return in_array( $mode, array( 'fill', 'box', 'fixed' ), true ) ? $mode : 'fill';
	}

	/**
	 * کلاس اضافی روی ریشهٔ ویجت — در حالت fill باعث می‌شود ریشه کل کانتینر را بگیرد.
	 * (پیش از چاپ تگ ریشه اجرا می‌شود؛ هر بار رندر.)
	 */
	protected function get_html_wrapper_class() {
		$class = parent::get_html_wrapper_class();
		if ( 'fill' === $this->mf_position_mode() ) {
			$class .= ' mf-scrub-host';
		}
		return $class;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_source',
			array(
				'label' => 'منبع ویدیو',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'video_media',
			array(
				'label'       => 'ویدیو (آپلود از رسانه‌ها)',
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'description' => 'یک فایل ویدیویی (پیشنهاد: MP4 با کدک H.264) را انتخاب یا آپلود کنید. اگر این گزینه پر باشد، از «لینک ویدیو» استفاده نمی‌شود. پس از آپلود، افزونه خودکار همه چیز (بافر، صف seek، قاب‌بندی) را مثل پیش‌فرض دمو آماده می‌کند.',
			)
		);

		$this->add_control(
			'video_url',
			array(
				'label'       => 'لینک ویدیو',
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array(
					'url'          => 'https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260826_041744_63efcd78-bf7d-4039-99e2-2461e8a61903.mp4',
					'is_external'  => true,
					'nofollow'     => true,
				),
				'description' => 'برای ویدیویی که روی سرور دیگری میزبانی می‌شود. پیش‌فرض، ویدیوی ابری دموی Mainframe است. حتماً با https:// شروع شود.',
				'condition'   => array( 'video_media' => '' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_behavior',
			array(
				'label' => 'رفتار حرکت و قرارگیری',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'position_mode',
			array(
				'label'       => 'حالت قرارگیری',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'fill',
				'options'     => array(
					'fill'  => 'پرکردن کانتینر — بنر هیرو (پیش‌فرض)',
					'box'   => 'جعبه‌ای — در جریان محتوا با ارتفاع دلخواه',
					'fixed' => 'تمام‌صفحه ثابت — پوشش کل پنجره',
				),
				'description' => 'fill: ویدیو کل کانتینر پدر را مثل بنر اسلایدر پر می‌کند و ارتفاع از کانتینر می‌آید (تنظیمات کانتینر ← چیدمان ← حداقل ارتفاع). fixed: مثل نسخه‌های قبل، روی کل صفحه ثابت. box: ویدیو عضوی از جریان محتواست.',
			)
		);

		$this->add_control(
			'sensitivity',
			array(
				'label'       => 'حساسیت',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0.8,
				'min'         => 0.1,
				'max'         => 3,
				'step'        => 0.1,
				'description' => 'ضریب جابه‌جایی زمان. فرمول: offset = (اختلاف ماوس ÷ عرض پنجره) × حساسیت × مدت ویدیو. مقدار 0.8 یعنی یک کشیدن کامل ماوس از چپ تا راست صفحه، 80٪ کل ویدیو را جابه‌جا می‌کند (همان مقدار دمو). برای ویدیوهای بلند 0.4 تا 0.6 روان‌تر است.',
			)
		);

		$this->add_control(
			'object_position',
			array(
				'label'       => 'موقعیت قاب (object-position)',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '70% center',
				'description' => 'کدام بخش ویدیو داخل قاب دیده شود. پیش‌فرض 70% center همان ترکیب دمو است: تمرکز روی 70٪ افق تصویر. نمونه‌ها: center، left top، 30% 50%.',
			)
		);

		$this->add_control(
			'object_fit',
			array(
				'label'       => 'نحوهٔ پر کردن قاب (object-fit)',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'cover',
				'options'     => array(
					'cover'       => 'cover — پر کردن بدون حاشیه (دمو)',
					'contain'     => 'contain — نمایش کامل با حاشیه',
					'fill'        => 'fill — کشیدگی تا اندازهٔ قاب',
					'scale-down'  => 'scale-down — کوچک‌تر از هر دو',
				),
				'description' => 'ویدیو چگونه داخل قاب جا شود. تغییر آنی است و بدون رندر مجدد اعمال می‌شود.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-scrub video' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'       => 'پوشش رنگی روی ویدیو',
				'type'        => \Elementor\Controls_Manager::COLOR,
				'default'     => '#000000',
				'description' => 'رنگ لایهٔ نیمه‌شفاف روی ویدیو برای خوانایی بهتر متن. به‌تنهایی اثری ندارد؛ شفافیت آن را با گزینهٔ کنترل کنید.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-scrub-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_opacity',
			array(
				'label'       => 'شفافیت پوشش (درصد)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 100,
				'step'        => 1,
				'description' => '0 = بدون پوشش (دمو). 30 تا 50 برای خوانایی متن روی ویدیوی شلوغ مناسب است. تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-scrub-overlay' => 'opacity: calc({{VALUE}} / 100);',
				),
			)
		);

		$this->add_control(
			'touch_scrub',
			array(
				'label'       => 'اسکرول لمسی (موبایل)',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label_on'    => 'روشن',
				'label_off'   => 'خاموش',
				'default'     => 'yes',
				'description' => 'روشن: کشیدن افقی انگشت روی ویدیو، آن را جلو/عقب می‌برد. کشیدن عمودی همچنان اسکرول صفحه را انجام می‌دهد (قفل محور هوشمند — بدون اختلال در اسکرول).',
			)
		);

		$this->add_control(
			'reverse',
			array(
				'label'       => 'جهت معکوس',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label_on'    => 'روشن',
				'label_off'   => 'خاموش',
				'default'     => '',
				'description' => 'روشن: حرکت ماوس به راست، ویدیو را به عقب می‌برد. برای ویدیوهایی که جهت طبیعی‌شان برعکس است.',
			)
		);

		$this->add_control(
			'hide_mobile',
			array(
				'label'       => 'پنهان در موبایل',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label_on'    => 'روشن',
				'label_off'   => 'خاموش',
				'default'     => '',
				'description' => 'روشن: در صفحه‌های کمتر از ۷۶۸ پیکسل ویدیو نمایش داده نمی‌شود (صرفه‌جویی در توجه و بارگذاری). برای این حالت به کانتینر پدر پس‌زمینه بدهید تا خالی دیده نشود.',
			)
		);

		$this->add_control(
			'height',
			array(
				'label'       => 'ارتفاع (حالت جعبه‌ای)',
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'default'     => array( 'size' => 420, 'unit' => 'px' ),
				'size_units'  => array( 'px', 'vh' ),
				'range'       => array(
					'px' => array( 'min' => 120, 'max' => 1200 ),
					'vh' => array( 'min' => 10, 'max' => 100 ),
				),
				'description' => 'فقط در حالت «جعبه‌ای» اعمال می‌شود. در حالت پرکردن، ارتفاع از کانتینر پدر می‌آید.',
				'condition'   => array( 'position_mode' => 'box' ),
			)
		);

		$this->add_control(
			'z_index',
			array(
				'label'       => 'لایه (z-index)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => -10,
				'max'         => 99,
				'description' => '0 یعنی پشت محتوا (همان دمو). در حالت پرکردن، محتوای روی ویدیو باید z-index بزرگ‌تر داشته باشد (در قالب آماده محتوا 1 و نوار بالا 10 است).',
				'condition'   => array( 'position_mode!' => 'box' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_playback',
			array(
				'label' => 'پخش و بارگذاری',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'       => 'پخش خودکار',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => '',
				'description' => 'در دمو خاموش است تا کنترل کامل با حرکت ماوس باشد. روشن کردن فقط در حالت بی‌صدا توسط مرورگر پذیرفته می‌شود.',
			)
		);

		$this->add_control(
			'muted',
			array(
				'label'       => 'بی‌صدا',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => 'بدون این گزینه مرورگرها پخش خودکار را رد می‌کنند. پیش‌فرض روشن (دمو).',
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'       => 'حلقه (تکرار)',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => '',
				'description' => 'در پایان ویدیو از اول شروع شود. دمو خاموش است؛ برای پس‌زمینه‌های دائمی روشن کنید.',
			)
		);

		$this->add_control(
			'start_offset',
			array(
				'label'       => 'شروع از (ثانیه)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 3600,
				'step'        => 0.5,
				'description' => 'ویدیو به‌جای ثانیهٔ صفر، از این لحظه شروع شود (مثلاً 5 تا از صحنهٔ وسط). 0 یعنی ابتدای ویدیو.',
			)
		);

		$this->add_control(
			'preload',
			array(
				'label'       => 'پیش‌بارگذاری',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'auto',
				'options'     => array(
					'auto'     => 'auto — کامل (مثل دمو؛ آماده برای اولین حرکت ماوس)',
					'metadata' => 'metadata — سبک‌تر، فقط اطلاعات',
					'none'     => 'none — بدون بارگذاری تا پخش',
				),
				'description' => 'auto باعث می‌شود duration ویدیو زودتر آماده شود و scrub از همان اول روان پاسخ دهد.',
			)
		);

		$this->add_control(
			'poster',
			array(
				'label'       => 'کاور (پوستر)',
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'media_types' => array( 'image' ),
				'description' => 'تصویری که تا لود شدن ویدیو دیده می‌شود. برای اینترنت‌های کند توصیه می‌شود.',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$video_url = '';
		if ( isset( $settings['video_media']['url'] ) && $settings['video_media']['url'] ) {
			$video_url = $settings['video_media']['url'];
		} elseif ( isset( $settings['video_url']['url'] ) && $settings['video_url']['url'] ) {
			$video_url = $settings['video_url']['url'];
		}

		if ( ! $video_url ) {
			return;
		}

		$mode = $this->mf_position_mode();
		$z    = intval( mf_num( isset( $settings['z_index'] ) ? $settings['z_index'] : null, 0 ) );

		$wrap_class = array( 'mf-scrub' );
		$wrap_style = '';

		if ( 'fixed' === $mode ) {
			$wrap_class[] = 'mf-scrub--fixed';
			$wrap_style   = 'position:fixed;inset:0;z-index:' . $z . ';';
		} elseif ( 'box' === $mode ) {
			$wrap_class[] = 'mf-scrub--box';
			$wrap_style   = 'position:relative;width:100%;height:' . esc_attr( mf_dim_unit( $settings['height'] ) ) . ';overflow:hidden;z-index:' . $z . ';';
		} else {
			// fill — ریشه با کلاس mf-scrub-host کل کانتینر را می‌گیرد؛ این لایه داخل آن را پر می‌کند.
			$wrap_class[] = 'mf-scrub--fill';
			$wrap_style   = 'position:absolute;inset:0;z-index:' . $z . ';overflow:hidden;pointer-events:none;';
		}

		if ( 'yes' === ( isset( $settings['hide_mobile'] ) ? $settings['hide_mobile'] : '' ) ) {
			$wrap_class[] = 'mf-scrub--hide-mobile';
		}

		$attrs = array(
			'class'          => $wrap_class,
			'data-sensitivity' => str_replace( ',', '.', (string) mf_num( $settings['sensitivity'], 0.8 ) ),
			'data-reverse'   => 'yes' === ( isset( $settings['reverse'] ) ? $settings['reverse'] : '' ) ? '1' : '0',
			'data-touch'     => 'yes' === ( isset( $settings['touch_scrub'] ) ? $settings['touch_scrub'] : 'yes' ) ? '1' : '0',
			'data-start'     => (string) mf_num( isset( $settings['start_offset'] ) ? $settings['start_offset'] : null, 0 ),
			'style'          => $wrap_style,
		);

		$this->add_render_attribute( 'wrap', $attrs );

		$video_attr = array(
			'src'     => $video_url,
			'preload' => $settings['preload'] ? $settings['preload'] : 'auto',
		);
		if ( 'yes' === $settings['muted'] ) {
			$video_attr['muted'] = '';
		}
		if ( 'yes' === $settings['autoplay'] ) {
			$video_attr['autoplay'] = '';
		}
		if ( 'yes' === $settings['loop'] ) {
			$video_attr['loop'] = '';
		}
		if ( isset( $settings['poster']['url'] ) && $settings['poster']['url'] ) {
			$video_attr['poster'] = $settings['poster']['url'];
		}
		$video_attr['playsinline'] = '';
		$this->add_render_attribute( 'video', $video_attr );

		// object-fit از کنترل selector (CSS زنده) می‌آید — فقط بقیهٔ استایل‌ها inline.
		$video_style = 'width:100%;height:100%;object-position:'
			. esc_attr( $settings['object_position'] ? $settings['object_position'] : '70% center' ) . ';display:block;';

		echo '<div ' . $this->get_render_attribute_string( 'wrap' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<video ' . $this->get_render_attribute_string( 'video' ) . ' style="' . esc_attr( $video_style ) . '"></video>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="mf-scrub-overlay" aria-hidden="true"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>';
	}
}

/**
 * تبدیل خروجی اسلایدر به رشتهٔ واحددار (مثلاً 420px).
 */
function mf_dim_unit( $v ) {
	if ( ! is_array( $v ) || empty( $v['size'] ) ) {
		return '420px';
	}
	$unit = isset( $v['unit'] ) && $v['unit'] ? $v['unit'] : 'px';
	return $v['size'] . $unit;
}
