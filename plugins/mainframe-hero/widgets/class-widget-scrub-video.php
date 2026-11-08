<?php
/**
 * Widget: Hero Motion Video.
 *
 * The character of the page. Upload any video and the widget turns it into a
 * scrubbed background: moving the pointer across the window runs the clip
 * forward or backward, exactly like the default hero video.
 *
 * @package MainframeHero
 */

namespace MFH\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Scrub_Video extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mfh-scrub-video';
	}

	/**
	 * Panel title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'ویدیوی حرکتی هیرو', 'mainframe-hero' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-video-camera';
	}

	/**
	 * Panel category.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( \MFH\Widgets::CATEGORY );
	}

	/**
	 * Search keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'video', 'hero', 'scrub', 'ویدیو', 'هیرو', 'اسکراب' );
	}

	/**
	 * Style dependencies.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'mainframe-hero-frontend' );
	}

	/**
	 * Script dependencies.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'mainframe-hero-frontend' );
	}

	/**
	 * Help link in the editor.
	 *
	 * @return string
	 */
	public function get_help_url() {
		return admin_url( 'admin.php?page=mainframe-hero&tab=video' );
	}

	/**
	 * Register the controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->section_media();
		$this->section_behaviour();
		$this->section_accessibility();
		$this->section_style_overlay();
	}

	/* --------------------------------------------------------------------- */
	/* Content                                                                */
	/* --------------------------------------------------------------------- */

	/**
	 * Media section.
	 *
	 * @return void
	 */
	private function section_media() {
		$this->start_controls_section(
			'section_media',
			array(
				'label' => __( 'رسانه', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'video',
			array(
				'label'       => __( 'ویدیوی پس‌زمینه', 'mainframe-hero' ),
				'description' => __( 'ویدیویی را انتخاب کنید که با حرکتِ ماوس جابه‌جا می‌شود. فرمت‌های MP4، WebM و OGG پشتیبانی می‌شوند. برای بهترین نتیجه یک فایل MP4 با کدک H.264 بارگذاری کنید.', 'mainframe-hero' ),
				'type'        => Controls_Manager::MEDIA,
				'media_type'  => 'video',
				'default'     => array( 'url' => '' ),
			)
		);

		$this->add_control(
			'poster',
			array(
				'label'       => __( 'تصویر جایگزین', 'mainframe-hero' ),
				'description' => __( 'تصویری که تا پیش از آماده‌شدنِ ویدیو نمایش داده می‌شود. اگر خالی بماند، افزونه به‌طور خودکار نخستین فریمِ ویدیو را به‌عنوان تصویرِ آغازین درخواست می‌کند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => '' ),
			)
		);

		$this->add_control(
			'auto_setup',
			array(
				'label'        => __( 'تنظیم خودکار', 'mainframe-hero' ),
				'description'  => __( 'با فعال‌بودن این گزینه، افزونه تنظیماتِ فنیِ لازم را خودش اعمال می‌کند: بی‌صدا، پخش درون‌خطی، پیش‌بارگذاری کامل، بدون پخش خودکار، و قفل‌شدنِ نسبتِ تصویر. برای رفتارِ دقیقاً مشابه ویدیوی پیش‌فرض این گزینه را روشن بگذارید.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'خودکار', 'mainframe-hero' ),
				'label_off'    => __( 'دستی', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'preload',
			array(
				'label'       => __( 'پیش‌بارگذاری', 'mainframe-hero' ),
				'description' => __( '«خودکار» کلِ ویدیو را از پیش می‌گیرد و برای اسکرابِ روان مناسب است. «فقط فراداده» فقط اطلاعاتِ فایل را می‌گیرد و سبک‌تر است.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'auto',
				'options'     => array(
					'auto'     => __( 'خودکار', 'mainframe-hero' ),
					'metadata' => __( 'فقط فراداده', 'mainframe-hero' ),
					'none'     => __( 'هیچ', 'mainframe-hero' ),
				),
				'condition'   => array( 'auto_setup!' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Behaviour section.
	 *
	 * @return void
	 */
	private function section_behaviour() {
		$this->start_controls_section(
			'section_behaviour',
			array(
				'label' => __( 'رفتارِ اسکراب', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'sensitivity',
			array(
				'label'       => __( 'حساسیت', 'mainframe-hero' ),
				'description' => __( 'چه بخشی از ویدیو با یک بار عبورِ ماوس از عرضِ صفحه طی شود. مقدار ۰/۸ یعنی هر بار عبورِ کامل، هشتاد درصدِ زمانِ ویدیو را جابه‌جا می‌کند — همان مقدارِ ویدیوی پیش‌فرض.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 0.05,
						'max'  => 3,
						'step' => 0.05,
					),
				),
				'default'     => array( 'unit' => 'px', 'size' => 0.8 ),
			)
		);

		$this->add_control(
			'axis',
			array(
				'label'       => __( 'محورِ حرکت', 'mainframe-hero' ),
				'description' => __( 'محوری که حرکتِ ماوس روی آن اندازه‌گیری می‌شود. پیش‌فرضِ این طرح افقی است.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'x',
				'options'     => array(
					'x' => __( 'افقی', 'mainframe-hero' ),
					'y' => __( 'عمودی', 'mainframe-hero' ),
				),
			)
		);

		$this->add_control(
			'invert',
			array(
				'label'        => __( 'وارونه‌کردنِ جهت', 'mainframe-hero' ),
				'description'  => __( 'جهتِ حرکت را برعکس می‌کند: با رفتن به راست، ویدیو به عقب برمی‌گردد.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'wrap',
			array(
				'label'        => __( 'چرخش در دو سر', 'mainframe-hero' ),
				'description'  => __( 'با رسیدن به انتها یا ابتدای ویدیو، حرکت از سر دیگر ادامه پیدا می‌کند و اسکراب هیچ‌وقت متوقف نمی‌شود.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'start_time',
			array(
				'label'       => __( 'زمانِ آغاز (ثانیه)', 'mainframe-hero' ),
				'description' => __( 'ویدیو از این نقطه شروع می‌شود. مقدارِ صفر یعنی آغازِ فایل.', 'mainframe-hero' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'step'        => 0.1,
				'default'     => 0,
			)
		);

		$this->add_control(
			'object_position_x',
			array(
				'label'       => __( 'جای‌گیری افقی تصویر (درصد)', 'mainframe-hero' ),
				'description' => __( 'کادرِ ویدیو روی کدام بخش از تصویر متمرکز شود. مقدارِ ۷۰ در ویدیوی پیش‌فرض سوژه را کمی به راست می‌برد.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'range'       => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'     => array( 'unit' => '%', 'size' => 70 ),
				// Written as a custom property rather than an inline style so
				// it lands in Elementor's own generated stylesheet.
				'selectors'   => array(
					'{{WRAPPER}} .mfh-scrub__video' => '--mfh-pos-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'object_position_y',
			array(
				'label'       => __( 'جای‌گیری عمودی تصویر (درصد)', 'mainframe-hero' ),
				'description' => __( 'مقدار ۵۰ یعنی میانِ تصویر.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'range'       => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'     => array( 'unit' => '%', 'size' => 50 ),
				'selectors'   => array(
					'{{WRAPPER}} .mfh-scrub__video' => '--mfh-pos-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ease',
			array(
				'label'       => __( 'نرمیِ حرکت', 'mainframe-hero' ),
				'description' => __( 'زمانِ رسیدنِ ویدیو به نقطهٔ هدف بر حسب ثانیه. مقدارِ کمتر تندتر و تیزتر است، مقدارِ بیشتر نرم‌تر و کش‌دارتر. صفر یعنی بدون نرمی.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1.5,
						'step' => 0.05,
					),
				),
				'default'     => array( 'unit' => 'px', 'size' => 0.25 ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Accessibility section.
	 *
	 * @return void
	 */
	private function section_accessibility() {
		$this->start_controls_section(
			'section_accessibility',
			array(
				'label' => __( 'دسترسی‌پذیری', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'reduced_motion',
			array(
				'label'       => __( 'رفتار در حالتِ کاهشِ حرکت', 'mainframe-hero' ),
				'description' => __( 'وقتی بازدیدکننده در سیستم خود گزینهٔ کاهشِ حرکت را فعال کرده باشد چه اتفاقی بیفتد. «ثابت بماند» اسکراب را خاموش می‌کند و ویدیو روی زمانِ آغاز می‌ایستد.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'freeze',
				'options'     => array(
					'freeze' => __( 'ثابت بماند', 'mainframe-hero' ),
					'keep'   => __( 'اسکراب ادامه یابد', 'mainframe-hero' ),
				),
			)
		);

		$this->add_control(
			'pointer_only',
			array(
				'label'        => __( 'فقط برای اشاره‌گرِ دقیق', 'mainframe-hero' ),
				'description'  => __( 'اسکراب تنها برای موس و قلم فعال می‌ماند و در صفحهٔ لمسی غیرفعال می‌شود، جایی که حرکتِ افقیِ ماوس معنایی ندارد.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'aria_label',
			array(
				'label'       => __( 'برچسب برای صفحه‌خوان', 'mainframe-hero' ),
				'description' => __( 'توضیحِ کوتاهی از محتوای ویدیو برای کسانی که آن را نمی‌بینند. اگر خالی بماند، ویدیو برای ابزارهای کمکی نادیده گرفته می‌شود.', 'mainframe-hero' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	/* --------------------------------------------------------------------- */
	/* Style                                                                  */
	/* --------------------------------------------------------------------- */

	/**
	 * Overlay + box style.
	 *
	 * @return void
	 */
	private function section_style_overlay() {
		$this->start_controls_section(
			'section_style_box',
			array(
				'label' => __( 'کادر', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'       => __( 'ارتفاع', 'mainframe-hero' ),
				'description' => __( 'ارتفاعِ کادرِ ویدیو. مقدارِ ۱۰۰vh یعنی به بلندای صفحهٔ نمایش.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'vh', 'px', '%' ),
				'range'       => array(
					'vh' => array( 'min' => 10, 'max' => 200 ),
					'px' => array( 'min' => 100, 'max' => 2000 ),
					'%'  => array( 'min' => 0, 'max' => 200 ),
				),
				'default'     => array( 'unit' => 'vh', 'size' => 100 ),
				'selectors'   => array(
					'{{WRAPPER}} .mfh-scrub' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'z_index',
			array(
				'label'       => __( 'لایه (z-index)', 'mainframe-hero' ),
				'description' => __( 'ویدیوی پس‌زمینه معمولاً روی لایهٔ صفر می‌نشیند و محتوا روی لایه‌های بالاتر.', 'mainframe-hero' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'selectors'   => array(
					'{{WRAPPER}} .mfh-scrub' => 'z-index: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'position',
			array(
				'label'       => __( 'نحوهٔ چسبیدن', 'mainframe-hero' ),
				'description' => __( 'برای یک هیروی تمام‌صفحه، «ثابت» ویدیو را هنگامِ اسکرولِ صفحه در جای خود نگه می‌دارد.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fixed',
				'options'     => array(
					'static'   => __( 'عادی', 'mainframe-hero' ),
					'relative' => __( 'نسبی', 'mainframe-hero' ),
					'absolute' => __( 'مطلق', 'mainframe-hero' ),
					'fixed'    => __( 'ثابت', 'mainframe-hero' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .mfh-scrub' => 'position: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_overlay',
			array(
				'label' => __( 'لایهٔ تیره', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'       => __( 'رنگِ لایه', 'mainframe-hero' ),
				'description' => __( 'لایه‌ای که روی ویدیو می‌نشیند تا خواناییِ متن حفظ شود.', 'mainframe-hero' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#000000',
				'selectors'   => array(
					'{{WRAPPER}} .mfh-scrub__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_opacity',
			array(
				'label'       => __( 'تیرگی', 'mainframe-hero' ),
				'description' => __( 'صفر یعنی بدون لایه، یک یعنی کاملاً پوشیده.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					),
				),
				'default'     => array( 'unit' => 'px', 'size' => 0.25 ),
				'selectors'   => array(
					'{{WRAPPER}} .mfh-scrub__overlay' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* --------------------------------------------------------------------- */
	/* Render                                                                 */
	/* --------------------------------------------------------------------- */

	/**
	 * Build the front-end markup.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$video_url  = isset( $settings['video']['url'] ) ? esc_url_raw( $settings['video']['url'] ) : '';
		$poster_url = isset( $settings['poster']['url'] ) ? esc_url_raw( $settings['poster']['url'] ) : '';

		if ( ! $video_url ) {
			if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				echo '<div class="mfh-empty">' . esc_html__( 'هنوز ویدیویی انتخاب نشده است. از بخش «رسانه» یک ویدیو بارگذاری کنید.', 'mainframe-hero' ) . '</div>';
			}
			return;
		}

		$auto      = mfh_bool( $settings['auto_setup'] );
		$preload   = $auto ? 'auto' : ( isset( $settings['preload'] ) ? $settings['preload'] : 'auto' );
		$start     = isset( $settings['start_time'] ) ? (float) $settings['start_time'] : 0;
		$sens      = isset( $settings['sensitivity']['size'] ) ? (float) $settings['sensitivity']['size'] : 0.8;
		$ease      = isset( $settings['ease']['size'] ) ? (float) $settings['ease']['size'] : 0.25;

		// Without an explicit poster we ask the browser for the first frame by
		// appending a media fragment — no server-side transcoding needed.
		if ( ! $poster_url && $start <= 0 ) {
			$poster_url = $video_url . '#t=0.1';
		}

		$payload = array(
			'sensitivity'   => $sens,
			'axis'          => isset( $settings['axis'] ) ? $settings['axis'] : 'x',
			'invert'        => mfh_bool( $settings['invert'] ) ? 1 : 0,
			'wrap'          => mfh_bool( $settings['wrap'] ) ? 1 : 0,
			'start'         => $start,
			'ease'          => $ease,
			'reducedMotion' => isset( $settings['reduced_motion'] ) ? $settings['reduced_motion'] : 'freeze',
			'pointerOnly'   => mfh_bool( $settings['pointer_only'] ) ? 1 : 0,
		);

		$this->add_render_attribute( 'wrapper', 'class', 'mfh-scrub' );
		$this->add_render_attribute( 'wrapper', 'data-mfh-scrub', wp_json_encode( $payload ) );

		$this->add_render_attribute(
			'video',
			array(
				'class'    => 'mfh-scrub__video',
				'src'      => $video_url,
				'muted'    => 'muted',
				'playsinline' => 'playsinline',
				'preload'  => esc_attr( $preload ),
			)
		);

		if ( $poster_url ) {
			$this->add_render_attribute( 'video', 'poster', esc_url( $poster_url ) );
		}

		if ( ! empty( $settings['aria_label'] ) ) {
			$this->add_render_attribute( 'video', 'aria-label', esc_attr( $settings['aria_label'] ) );
		} else {
			$this->add_render_attribute( 'video', 'aria-hidden', 'true' );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<video <?php $this->print_render_attribute_string( 'video' ); ?>></video>
			<span class="mfh-scrub__overlay" aria-hidden="true"></span>
		</div>
		<?php
	}
}
