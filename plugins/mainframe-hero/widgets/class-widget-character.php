<?php
/**
 * Widget: Reactive Character.
 *
 * Upload any video and the widget configures itself to match the reference
 * character: it reads the real duration and dimensions, hunts for the first
 * frame that actually contains an image, builds a poster from it, and derives
 * a sensitivity that makes one comfortable pointer sweep cover the clip.
 *
 * All of that happens in the browser. No frame is uploaded, nothing is written
 * to the server, and results are cached per URL for the session.
 *
 * @package MainframeHero
 */

namespace MFH\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Character extends Widget_Base {

	/**
	 * Documented defaults, shared by the panel, the docs and the front end.
	 *
	 * @var array
	 */
	private static $defaults = array(
		// How many seconds of footage one full-width pointer sweep should cover.
		'comfort_seconds'   => 9.6,
		'sensitivity'       => 0.8,
		// Mean luminance (0-255) a frame must reach to count as "has content".
		'luminance_floor'   => 18,
		// How many frames to sample when hunting for the first live frame.
		'sample_count'      => 24,
		'pointer_smoothing' => 0.06,
		'character_drift'   => 26,
	);

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mfh-character';
	}

	/**
	 * Panel title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'کاراکتر واکنشی', 'mainframe-hero' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-person';
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
		return array( 'character', 'video', 'mouse', 'follow', 'کاراکتر', 'شخصیت' );
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
		return admin_url( 'admin.php?page=mainframe-hero#character' );
	}

	/**
	 * Read a documented default.
	 *
	 * @param string $key Default key.
	 * @param mixed  $fallback Value when the key is unknown.
	 *
	 * @return mixed
	 */
	private static function default_value( $key, $fallback = null ) {
		return array_key_exists( $key, self::$defaults ) ? self::$defaults[ $key ] : $fallback;
	}

	/**
	 * Register the controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->section_source();
		$this->section_auto();
		$this->section_motion();
		$this->section_framing();
		$this->section_docs();
	}

	/* --------------------------------------------------------------------- */
	/* Small control helpers                                                  */
	/* --------------------------------------------------------------------- */

	/**
	 * A documentation block inside the panel.
	 *
	 * @param string $id     Unique control id.
	 * @param string $title  Heading shown in the panel.
	 * @param string $body   Body HTML. Already escaped by the caller.
	 * @param string $where  'before' or 'after' — where to draw the separator.
	 *
	 * @return void
	 */
	private function help_block( $id, $title, $body, $where = 'before' ) {
		$this->add_control(
			$id,
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					'<div class="mfh-doc"><p class="mfh-doc__title">%s</p><div class="mfh-doc__body">%s</div></div>',
					esc_html( $title ),
					$body // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				),
				'separator'       => $where,
				'content_classes' => 'mfh-doc-box',
			)
		);
	}

	/**
	 * A number control.
	 *
	 * @param string $id      Control id.
	 * @param string $label   Label.
	 * @param float  $default Default value.
	 * @param array  $extra   Overrides merged last.
	 *
	 * @return void
	 */
	private function number( $id, $label, $default, $extra = array() ) {
		$this->add_control(
			$id,
			array_merge(
				array(
					'label'   => $label,
					'type'    => Controls_Manager::NUMBER,
					'default' => $default,
				),
				$extra
			)
		);
	}

	/**
	 * A slider control.
	 *
	 * @param string $id      Control id.
	 * @param string $label   Label.
	 * @param float  $default Default value.
	 * @param array  $range   Range definition.
	 * @param array  $extra   Overrides merged last.
	 *
	 * @return void
	 */
	private function slider( $id, $label, $default, $range, $extra = array() ) {
		$this->add_control(
			$id,
			array_merge(
				array(
					'label'   => $label,
					'type'    => Controls_Manager::SLIDER,
					'default' => array( 'size' => $default, 'unit' => 'px' ),
					'range'   => $range,
				),
				$extra
			)
		);
	}

	/**
	 * Read a slider setting, falling back to its default.
	 *
	 * @param array  $settings Settings array.
	 * @param string $key      Setting key.
	 * @param mixed  $fallback Fallback value.
	 *
	 * @return float
	 */
	private function slider_value( $settings, $key, $fallback = 0 ) {
		if ( ! isset( $settings[ $key ] ) || ! is_array( $settings[ $key ] ) ) {
			return (float) $fallback;
		}

		return isset( $settings[ $key ]['size'] ) && '' !== $settings[ $key ]['size']
			? (float) $settings[ $key ]['size']
			: (float) $fallback;
	}

	/**
	 * Resolve the video URL from either a library upload or a raw URL.
	 *
	 * @param array $settings Settings array.
	 *
	 * @return string
	 */
	private function resolve_video_url( $settings ) {
		$type = isset( $settings['mf_source_type'] ) ? $settings['mf_source_type'] : 'library';

		if ( 'url' === $type ) {
			return isset( $settings['mf_video_url']['url'] ) ? esc_url_raw( $settings['mf_video_url']['url'] ) : '';
		}

		return isset( $settings['mf_video']['url'] ) ? esc_url_raw( $settings['mf_video']['url'] ) : '';
	}

	/* --------------------------------------------------------------------- */
	/* Content                                                                */
	/* --------------------------------------------------------------------- */

	/**
	 * Source section.
	 *
	 * @return void
	 */
	private function section_source() {
		$this->start_controls_section(
			'section_source',
			array(
				'label' => __( 'منبع', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'mf_source_type',
			array(
				'label'   => __( 'منبع ویدیو', 'mainframe-hero' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'library',
				'options' => array(
					'library' => __( 'کتابخانهٔ رسانه', 'mainframe-hero' ),
					'url'     => __( 'نشانی مستقیم', 'mainframe-hero' ),
				),
			)
		);

		$this->add_control(
			'mf_video',
			array(
				'label'       => __( 'ویدیو', 'mainframe-hero' ),
				'description' => __( 'یک ویدیو انتخاب کنید. افزونه بلافاصله آن را تحلیل می‌کند و زمانِ آغاز، حساسیت، تصویر پیش‌فرض و کادربندی را همانند ویدیوی پیش‌فرض تنظیم می‌کند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::MEDIA,
				'media_type'  => 'video',
				'default'     => array( 'url' => '' ),
				'condition'   => array( 'mf_source_type' => 'library' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'mf_video_url',
			array(
				'label'       => __( 'نشانی ویدیو', 'mainframe-hero' ),
				'description' => __( 'برای این‌که تحلیلِ فریم کار کند، سرور باید هدرِ CORS بفرستد. در غیر این صورت تنظیمِ خودکار فقط مدت‌زمان را می‌خواند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://…/clip.mp4',
				'condition'   => array( 'mf_source_type' => 'url' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'mf_poster',
			array(
				'label'       => __( 'تصویر پیش‌فرض (اختیاری)', 'mainframe-hero' ),
				'description' => __( 'پیش از بارگیریِ ویدیو نمایش داده می‌شود. اگر خالی باشد و «ساختِ تصویر پیش‌فرض» روشن باشد، افزونه آن را از فریمِ آغاز می‌سازد.', 'mainframe-hero' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => '' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Auto-configuration section.
	 *
	 * @return void
	 */
	private function section_auto() {
		$this->start_controls_section(
			'section_auto',
			array(
				'label' => __( 'تحلیل و تنظیم خودکار', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'mf_auto',
			array(
				'label'        => __( 'تنظیم خودکار', 'mainframe-hero' ),
				'description'  => __( 'با انتخابِ ویدیو، افزونه مدت‌زمان را می‌خواند، نخستین فریمِ غیرسیاه را پیدا می‌کند، تصویرِ پیش‌فرض را می‌سازد و حساسیت را حساب می‌کند. مقادیرِ دستی در این حالت نادیده گرفته می‌شوند.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'روشن', 'mainframe-hero' ),
				'label_off'    => __( 'خاموش', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->number(
			'mf_luminance_floor',
			__( 'آستانهٔ روشنایی', 'mainframe-hero' ),
			self::default_value( 'luminance_floor' ),
			array(
				'min'         => 0,
				'max'         => 200,
				'step'        => 1,
				'condition'   => array( 'mf_auto' => 'yes' ),
				'description' => __( 'میانگینِ روشناییِ یک فریم (۰ تا ۲۵۵) که بالاتر از آن، فریم «دارای تصویر» حساب می‌شود. اگر ویدیوی شما بسیار تاریک است این عدد را کم کنید.', 'mainframe-hero' ),
			)
		);

		$this->number(
			'mf_sample_count',
			__( 'تعداد نمونه', 'mainframe-hero' ),
			self::default_value( 'sample_count' ),
			array(
				'min'         => 4,
				'max'         => 96,
				'step'        => 1,
				'condition'   => array( 'mf_auto' => 'yes' ),
				'description' => __( 'چند فریم برای یافتنِ فریمِ آغاز بررسی شود. عددِ بیشتر یعنی دقتِ بیشتر و زمانِ تحلیلِ بیشتر.', 'mainframe-hero' ),
			)
		);

		$this->add_control(
			'mf_generate_poster',
			array(
				'label'        => __( 'ساختِ تصویر پیش‌فرض', 'mainframe-hero' ),
				'description'  => __( 'تصویری که از فریمِ آغاز ساخته می‌شود در مرورگرِ کاربر تولید و در همان نشست نگه داشته می‌شود؛ چیزی روی سرور ذخیره نمی‌شود.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'mf_auto' => 'yes' ),
			)
		);

		$this->number(
			'mf_initial_time',
			__( 'زمانِ آغاز (ثانیه)', 'mainframe-hero' ),
			0,
			array(
				'min'         => 0,
				'step'        => 0.01,
				'description' => __( 'نمایشِ ویدیو از این ثانیه شروع می‌شود. وقتی تنظیمِ خودکار روشن باشد، این مقدار پس از تحلیل با نخستین فریمِ دارایِ تصویر پر می‌شود و می‌توانید آن را دستی هم تغییر دهید.', 'mainframe-hero' ),
			)
		);

		$this->add_control(
			'mf_auto_status',
			array(
				'type'      => Controls_Manager::RAW_HTML,
				'raw'       => '<div class="mfh-doc"><p class="mfh-doc__title">'
					. esc_html__( 'گزارشِ تحلیل', 'mainframe-hero' )
					. '</p><div class="mfh-doc__body" data-mfh-status><p class="mfh-doc__note">'
					. esc_html__( 'هنوز ویدیویی تحلیل نشده است.', 'mainframe-hero' )
					. '</p></div></div>',
				'separator' => 'before',
			)
		);

		$this->help_block(
			'mf_auto_explain',
			__( 'دقیقاً چه چیزی خودکار می‌شود', 'mainframe-hero' ),
			sprintf(
				'<ol class="mfh-doc__list"><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ol><p class="mfh-doc__note">%s</p>',
				esc_html__( 'مدت‌زمان و ابعادِ واقعیِ ویدیو از متادیتا خوانده می‌شود.', 'mainframe-hero' ),
				esc_html__( 'چند فریم روی یک بومِ پنهان نمونه‌برداری می‌شود تا نخستین فریمی که تصویر دارد پیدا شود؛ همان می‌شود «زمانِ آغاز».', 'mainframe-hero' ),
				esc_html__( 'حساسیت از مدت‌زمان محاسبه می‌شود تا یک پیمایشِ کاملِ پنجره حدودِ ده ثانیه از ویدیو را بپیماید؛ ویدیوهای کوتاه‌تر در یک پیمایشِ کامل دیده می‌شوند.', 'mainframe-hero' ),
				esc_html__( 'تصویرِ پیش‌فرض از همان فریمِ آغاز ساخته می‌شود تا پیش از بارگیری، قاب خالی نماند.', 'mainframe-hero' ),
				esc_html__( 'تحلیل در مرورگر انجام می‌شود؛ چیزی روی سرور پردازش یا ذخیره نمی‌شود. برای ویدیوهای روی دامنهٔ دیگر، سرور باید هدرِ CORS بفرستد، وگرنه فقط مدت‌زمان در دسترس خواهد بود.', 'mainframe-hero' )
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Motion section.
	 *
	 * @return void
	 */
	private function section_motion() {
		$this->start_controls_section(
			'section_motion',
			array(
				'label' => __( 'رفتارِ حرکت', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'mf_mode',
			array(
				'label'       => __( 'حالتِ حرکت', 'mainframe-hero' ),
				'description' => __( '«اسکراب با اشاره‌گر» همان رفتارِ ویدیوی پس‌زمینه است: ویدیو پخش نمی‌شود و با حرکتِ اشاره‌گر جابه‌جا می‌گردد. «پخشِ خودکار» ویدیو را در حلقه پخش می‌کند و کاراکتر فقط اشاره‌گر را تعقیب می‌کند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'scrub',
				'options'     => array(
					'scrub'    => __( 'اسکراب با اشاره‌گر', 'mainframe-hero' ),
					'autoplay' => __( 'پخش خودکار', 'mainframe-hero' ),
				),
			)
		);

		$this->number(
			'mf_sensitivity',
			__( 'حساسیتِ اسکراب', 'mainframe-hero' ),
			self::default_value( 'sensitivity' ),
			array(
				'min'       => 0.05,
				'max'       => 3,
				'step'      => 0.05,
				'condition' => array( 'mf_mode' => 'scrub' ),
			)
		);

		$this->add_control(
			'mf_follow_heading',
			array(
				'label'     => __( 'دنبال کردنِ اشاره‌گر', 'mainframe-hero' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->slider(
			'mf_follow_x',
			__( 'جابه‌جاییِ افقی', 'mainframe-hero' ),
			self::default_value( 'character_drift' ),
			array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
			array( 'description' => __( 'بیشینهٔ جابه‌جاییِ کاراکتر بر حسبِ پیکسل وقتی اشاره‌گر در دو لبهٔ پنجره است.', 'mainframe-hero' ) )
		);

		$this->slider(
			'mf_follow_y',
			__( 'جابه‌جاییِ عمودی', 'mainframe-hero' ),
			18,
			array( 'px' => array( 'min' => 0, 'max' => 200 ) )
		);

		$this->slider(
			'mf_follow_rotate',
			__( 'چرخش', 'mainframe-hero' ),
			3,
			array( 'deg' => array( 'min' => 0, 'max' => 30 ) ),
			array( 'default' => array( 'size' => 3, 'unit' => 'deg' ) )
		);

		$this->number(
			'mf_smoothing',
			__( 'نرمیِ تعقیب', 'mainframe-hero' ),
			self::default_value( 'pointer_smoothing' ),
			array(
				'min'         => 0.01,
				'max'         => 1,
				'step'        => 0.01,
				'description' => __( 'هر فریم چه سهمی از فاصلهٔ باقی‌مانده طی شود. عددِ کوچک‌تر یعنی حرکتِ نرم‌تر و تأخیرِ بیشتر؛ عددِ بزرگ‌تر یعنی پاسخِ تندتر.', 'mainframe-hero' ),
			)
		);

		$this->add_control(
			'mf_idle',
			array(
				'label'        => __( 'جنبشِ ملایم در حالتِ سکون', 'mainframe-hero' ),
				'description'  => __( 'وقتی اشاره‌گر حرکت نمی‌کند، کاراکتر با یک نوسانِ بسیار آرام بالا و پایین می‌رود تا صحنه کاملاً بی‌جان نباشد.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'روشن', 'mainframe-hero' ),
				'label_off'    => __( 'خاموش', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->slider(
			'mf_idle_amount',
			__( 'اندازهٔ نوسان', 'mainframe-hero' ),
			10,
			array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
			array( 'condition' => array( 'mf_idle' => 'yes' ) )
		);

		$this->end_controls_section();
	}

	/**
	 * Framing section.
	 *
	 * @return void
	 */
	private function section_framing() {
		$this->start_controls_section(
			'section_framing',
			array(
				'label' => __( 'کادربندی', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->slider(
			'mf_width',
			__( 'پهنا', 'mainframe-hero' ),
			520,
			array(
				'px' => array( 'min' => 80, 'max' => 1600 ),
				'%'  => array( 'min' => 5, 'max' => 100 ),
				'vw' => array( 'min' => 5, 'max' => 100 ),
			),
			array(
				'size_units' => array( 'px', '%', 'vw' ),
				'selectors'  => array( '{{WRAPPER}} .mfh-character' => 'width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'mf_aspect',
			array(
				'label'       => __( 'نسبتِ تصویر', 'mainframe-hero' ),
				'description' => __( '«نسبتِ خودِ ویدیو» ابعاد را پس از بارگیری از متادیتا می‌گیرد، بنابراین با عوض کردنِ ویدیو نیازی به تنظیمِ دوباره نیست.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'auto',
				'options'     => array(
					'auto'   => __( 'نسبتِ خودِ ویدیو', 'mainframe-hero' ),
					'1/1'    => '1:1',
					'3/4'    => '3:4',
					'4/5'    => '4:5',
					'16/9'   => '16:9',
					'custom' => __( 'دلخواه', 'mainframe-hero' ),
				),
				'selectors'   => array( '{{WRAPPER}} .mfh-character__video' => 'aspect-ratio: {{VALUE}};' ),
			)
		);

		$this->number(
			'mf_aspect_custom',
			__( 'نسبتِ دلخواه (پهنا ÷ ارتفاع)', 'mainframe-hero' ),
			1,
			array(
				'min'       => 0.2,
				'max'       => 5,
				'step'      => 0.01,
				'condition' => array( 'mf_aspect' => 'custom' ),
				'selectors' => array( '{{WRAPPER}} .mfh-character__video' => 'aspect-ratio: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'mf_object_fit',
			array(
				'label'     => __( 'نحوهٔ پر کردن', 'mainframe-hero' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'cover'   => __( 'پوششِ کامل (برش می‌خورد)', 'mainframe-hero' ),
					'contain' => __( 'نمایشِ کامل (حاشیه می‌ماند)', 'mainframe-hero' ),
				),
				'selectors' => array( '{{WRAPPER}} .mfh-character__video' => 'object-fit: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'mf_mask',
			array(
				'label'       => __( 'ماسک', 'mainframe-hero' ),
				'description' => __( 'ماسکِ «محو شدن در پایین» باعث می‌شود کاراکتر در پس‌زمینه حل شود و لبهٔ پایینیِ ویدیو دیده نشود — همان کاری که در پیش‌نمایش می‌بینید.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'soft',
				'options'     => array(
					'none'  => __( 'بدون ماسک', 'mainframe-hero' ),
					'soft'  => __( 'محو شدن در پایین', 'mainframe-hero' ),
					'edges' => __( 'محو شدن در لبه‌ها', 'mainframe-hero' ),
					'round' => __( 'گرد', 'mainframe-hero' ),
				),
			)
		);

		$this->add_control(
			'mf_blend',
			array(
				'label'       => __( 'ترکیب با پس‌زمینه', 'mainframe-hero' ),
				'description' => __( 'برای ویدیوهایی که پس‌زمینهٔ سیاه دارند، «روشن‌تر» باعث می‌شود سیاهی ناپدید شود و فقط خودِ سوژه روی صفحه بماند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'normal',
				'options'     => array(
					'normal'     => __( 'عادی', 'mainframe-hero' ),
					'screen'     => __( 'روشن‌تر (screen)', 'mainframe-hero' ),
					'lighten'    => __( 'روشن‌تر (lighten)', 'mainframe-hero' ),
					'luminosity' => __( 'فقط روشنایی', 'mainframe-hero' ),
				),
				'selectors'   => array( '{{WRAPPER}} .mfh-character__video' => 'mix-blend-mode: {{VALUE}};' ),
			)
		);

		$this->slider(
			'mf_char_opacity',
			__( 'کدری', 'mainframe-hero' ),
			100,
			array( '%' => array( 'min' => 0, 'max' => 100 ) ),
			array(
				'default'   => array( 'size' => 100, 'unit' => '%' ),
				'selectors' => array( '{{WRAPPER}} .mfh-character' => 'opacity: {{SIZE}}%;' ),
			)
		);

		$this->add_control(
			'mf_align',
			array(
				'label'     => __( 'جای‌گیری در کانتینر', 'mainframe-hero' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => array( 'title' => __( 'بالا', 'mainframe-hero' ), 'icon' => 'eicon-v-align-top' ),
					'center'     => array( 'title' => __( 'وسط', 'mainframe-hero' ), 'icon' => 'eicon-v-align-middle' ),
					'flex-end'   => array( 'title' => __( 'پایین', 'mainframe-hero' ), 'icon' => 'eicon-v-align-bottom' ),
				),
				'selectors' => array( '{{WRAPPER}} .mfh-character' => 'align-items: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Documentation section.
	 *
	 * @return void
	 */
	private function section_docs() {
		$this->start_controls_section(
			'section_docs',
			array(
				'label' => __( 'راهنما', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->help_block(
			'mf_docs',
			__( 'چطور یک کاراکترِ تازه اضافه کنم', 'mainframe-hero' ),
			sprintf(
				'<ol class="mfh-doc__list"><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ol><p class="mfh-doc__note">%s</p>',
				esc_html__( 'در بخشِ «منبع» یک ویدیو انتخاب کنید. ترجیحاً با پس‌زمینهٔ سیاه و بدون صدا.', 'mainframe-hero' ),
				esc_html__( 'چند لحظه صبر کنید؛ پیامِ «تنظیماتِ خودکار اعمال شد» در بالای پنل ظاهر می‌شود و زمانِ آغاز و حساسیت پر می‌شوند.', 'mainframe-hero' ),
				esc_html__( 'اگر ویدیو سیاهِ خالص ندارد، «ترکیب با پس‌زمینه» را روی حالتِ عادی بگذارید و «ماسک» را روی «بدون ماسک».', 'mainframe-hero' ),
				esc_html__( 'اندازه و محل را با کانتینرِ المنتور تنظیم کنید؛ خودِ ابزارک فقط محتوایش را کنترل می‌کند.', 'mainframe-hero' ),
				esc_html__( 'اگر پیامِ خطا دیدید، نشانیِ ویدیو را بررسی کنید. ویدیوهای روی دامنهٔ دیگر بدون هدرِ CORS قابلِ تحلیلِ پیکسلی نیستند؛ در آن حالت تنظیمِ خودکار فقط مدت‌زمان را می‌خواند.', 'mainframe-hero' )
			)
		);

		$this->end_controls_section();
	}

	/* --------------------------------------------------------------------- */
	/* Render                                                                */
	/* --------------------------------------------------------------------- */

	/**
	 * Read the auto-configuration settings into a plain array.
	 *
	 * @param array $settings Settings array.
	 *
	 * @return array
	 */
	private function auto_config_values( $settings ) {
		return array(
			'auto'       => mfh_bool( isset( $settings['mf_auto'] ) ? $settings['mf_auto'] : 'yes' ),
			'floor'      => isset( $settings['mf_luminance_floor'] ) ? (float) $settings['mf_luminance_floor'] : (float) self::default_value( 'luminance_floor' ),
			'samples'    => isset( $settings['mf_sample_count'] ) ? (int) $settings['mf_sample_count'] : (int) self::default_value( 'sample_count' ),
			'makePoster' => mfh_bool( isset( $settings['mf_generate_poster'] ) ? $settings['mf_generate_poster'] : 'yes' ),
		);
	}

	/**
	 * Build the front-end markup.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$src      = $this->resolve_video_url( $settings );

		if ( ! $src ) {
			if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				echo '<div class="mfh-empty">' . esc_html__( 'هنوز ویدیویی انتخاب نشده است. از بخش «منبع» یک ویدیو بارگذاری کنید.', 'mainframe-hero' ) . '</div>';
			}

			return;
		}

		$config = array(
			'src'          => $src,
			'poster'       => isset( $settings['mf_poster']['url'] ) ? $settings['mf_poster']['url'] : '',
			'mode'         => isset( $settings['mf_mode'] ) ? $settings['mf_mode'] : 'scrub',
			'sensitivity'  => isset( $settings['mf_sensitivity'] ) ? (float) $settings['mf_sensitivity'] : (float) self::default_value( 'sensitivity' ),
			'followX'      => $this->slider_value( $settings, 'mf_follow_x', self::default_value( 'character_drift' ) ),
			'followY'      => $this->slider_value( $settings, 'mf_follow_y', 18 ),
			'rotate'       => $this->slider_value( $settings, 'mf_follow_rotate', 3 ),
			'smoothing'    => isset( $settings['mf_smoothing'] ) ? (float) $settings['mf_smoothing'] : (float) self::default_value( 'pointer_smoothing' ),
			'idle'         => mfh_bool( isset( $settings['mf_idle'] ) ? $settings['mf_idle'] : 'yes' ),
			'idleAmount'   => $this->slider_value( $settings, 'mf_idle_amount', 10 ),
			'aspect'       => isset( $settings['mf_aspect'] ) ? $settings['mf_aspect'] : 'auto',
			'aspectCustom' => isset( $settings['mf_aspect_custom'] ) ? (float) $settings['mf_aspect_custom'] : 1,
			'initial'      => isset( $settings['mf_initial_time'] ) ? (float) $settings['mf_initial_time'] : 0,
			'auto'         => $this->auto_config_values( $settings ),
		);

		$mask = isset( $settings['mf_mask'] ) ? $settings['mf_mask'] : 'soft';

		$this->add_render_attribute( 'wrapper', 'class', 'mfh-character' );
		$this->add_render_attribute( 'wrapper', 'class', 'mfh-character--mask-' . esc_attr( $mask ) );
		$this->add_render_attribute( 'wrapper', 'data-mfh-character', mfh_json_attr( $config ) );

		$this->add_render_attribute(
			'video',
			array(
				'class'       => 'mfh-character__video',
				'src'         => esc_url( $src ),
				'muted'       => 'muted',
				'playsinline' => 'playsinline',
				'preload'     => 'auto',
				'aria-hidden' => 'true',
			)
		);

		if ( $config['poster'] ) {
			$this->add_render_attribute( 'video', 'poster', esc_url( $config['poster'] ) );
		}

		if ( 'autoplay' === $config['mode'] ) {
			$this->add_render_attribute( 'video', 'loop', 'loop' );
			$this->add_render_attribute( 'video', 'autoplay', 'autoplay' );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<video <?php $this->print_render_attribute_string( 'video' ); ?>></video>
		</div>
		<?php
	}
}
