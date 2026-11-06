<?php
/**
 * ویجت: ویدیوی حرکت با ماوس (mouse-scrub)
 *
 * رفتار: ویدیو با حرکت افقی ماوس جلو/عقب می‌رود.
 * قانون: offset = (delta ÷ عرض پنجره) × حساسیت × duration
 * صف seek: فقط یک seek همزمان؛ با onSeeked در صورت جا افتادن هدف، seek بعدی.
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
		return array( 'ویدیو', 'ماوس', 'اسکرول', 'کاراکتر', 'mousemove', 'scrub' );
	}

	public function get_script_depends() {
		return array( 'mainframe-frontend' );
	}

	public function get_style_depends() {
		return array( 'mainframe-frontend' );
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
					'url' => 'https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260826_041744_63efcd78-bf7d-4039-99e2-2461e8a61903.mp4',
					'is_external' => true,
					'nofollow'    => true,
				),
				'description' => 'برای ویدیویی که روی سرور دیگری میزبانی می‌شود. پیش‌فرض، ویدیوی ابری دموی Mainframe است. حتماً با https:// شروع شود.',
				'condition'   => array( 'video_media' => '' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_behavior',
			array(
				'label' => 'رفتار حرکت',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
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
			'fixed_mode',
			array(
				'label'       => 'تمام‌صفحه ثابت',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label_on'    => 'روشن',
				'label_off'   => 'خاموش',
				'default'     => 'yes',
				'description' => 'روشن: ویدیو position:fixed می‌شود و تمام صفحه را مثل دمو می‌پوشاند (پشت محتوا، لایهٔ 0). خاموش: ویدیو داخل جریان محتوا می‌ماند و به‌عنوان «کاراکتر» یا ویدیوی جعبه‌ای با ارتفاع دلخواه استفاده می‌شود.',
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
				'description' => 'فقط وقتی «تمام‌صفحه ثابت» خاموش باشد اعمال می‌شود؛ در حالت تمام‌صفحه نادیده گرفته می‌شود.',
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
				'description' => 'در حالت تمام‌صفحه، 0 یعنی پشت محتوا (همان دمو). عدد منفی عمیق‌تر، عدد مثبت جلوتر.',
				'condition'   => array( 'fixed_mode' => 'yes' ),
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

		$fixed = 'yes' === $settings['fixed_mode'];

		$wrap_style = $fixed
			? 'position:fixed;inset:0;z-index:' . intval( $settings['z_index'] ) . ';'
			: 'position:relative;width:100%;height:' . esc_attr( mf_dim_unit( $settings['height'] ) ) . ';overflow:hidden;';

		$this->add_render_attribute(
			'wrap',
			array(
				'class'     => array( 'mf-scrub', $fixed ? 'mf-scrub--fixed' : 'mf-scrub--box' ),
				'data-sensitivity' => str_replace( ',', '.', (string) mf_num( $settings['sensitivity'], 0.8 ) ),
				'style'     => $wrap_style,
			)
		);

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

		$video_style = 'width:100%;height:100%;object-fit:cover;object-position:'
			. esc_attr( $settings['object_position'] ? $settings['object_position'] : '70% center' ) . ';display:block;';

		echo '<div ' . $this->get_render_attribute_string( 'wrap' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<video ' . $this->get_render_attribute_string( 'video' ) . ' style="' . esc_attr( $video_style ) . '"></video>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>';
	}
}

/**
 * تبدیل خروجی اسلایدر به رشتهٔ واحددار (مثلاً 420px).
 */
if ( ! function_exists( 'mf_dim_unit' ) ) {
	function mf_dim_unit( $v ) {
		if ( ! is_array( $v ) || empty( $v['size'] ) ) {
			return '420px';
		}
		$unit = isset( $v['unit'] ) && $v['unit'] ? $v['unit'] : 'px';
		return $v['size'] . $unit;
	}
}
