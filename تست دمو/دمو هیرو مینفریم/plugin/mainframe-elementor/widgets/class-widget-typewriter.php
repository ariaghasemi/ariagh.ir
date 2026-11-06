<?php
/**
 * ویجت: تایپ‌نویسر
 *
 * رفتار: پس از startDelay میلی‌ثانیه، هر speed میلی‌ثانیه یک کاراکتر؛
 * بصر چشمک‌زن تا پایان؛ پس از اتمام بصر پنهان می‌شود.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget_Mainframe_Typewriter extends \Elementor\Widget_Base {

	public function get_name() {
		return 'mainframe-typewriter';
	}

	public function get_title() {
		return 'تایپ‌نویسر';
	}

	public function get_icon() {
		return 'eicon-typed-text';
	}

	public function get_categories() {
		return array( 'mainframe' );
	}

	public function get_keywords() {
		return array( 'تایپ', 'نویسر', 'متن متحرک', 'typewriter', 'تایپوگرافی' );
	}

	public function get_script_depends() {
		return array( 'mainframe-frontend' );
	}

	public function get_style_depends() {
		return array( 'mainframe-frontend' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => 'متن و رفتار',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'text',
			array(
				'label'       => 'متن',
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => 'Glad you stopped in. Good taste tends to find us. Now, what are we building?',
				'rows'        => 3,
				'description' => 'متنی که حرف‌به‌حرف تایپ می‌شود. ساده باشد؛ برچسب‌های HTML پردازش نمی‌شوند. پیش‌فرض جملهٔ دموی Mainframe است.',
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'       => 'سرعت (میلی‌ثانیه هر حرف)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 38,
				'min'         => 10,
				'max'         => 300,
				'step'        => 1,
				'description' => 'فاصلهٔ زمانی بین هر کاراکتر. 38 همان مقدار دمو است و برای انگلیسی خوانا است؛ برای فارسی 45 تا 55 پیشنهاد می‌شود. عدد کوچک‌تر = تایپ سریع‌تر.',
			)
		);

		$this->add_control(
			'delay',
			array(
				'label'       => 'تاخیر شروع (میلی‌ثانیه)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 600,
				'min'         => 0,
				'max'         => 5000,
				'step'        => 50,
				'description' => 'مکث قبل از شروع تایپ؛ در دمو 600 میلی‌ثانیه است تا اول صفحه جا بیفتد.',
			)
		);

		$this->add_control(
			'start_on_view',
			array(
				'label'       => 'شروع هنگام دیده‌شدن',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label_on'    => 'روشن',
				'label_off'   => 'خاموش',
				'default'     => '',
				'description' => 'روشن: تایپ فقط وقتی ویجت وارد دید صفحه شود شروع می‌شود (مناسب المان‌های پایین‌صفحه). خاموش: فوری هنگام لود (دمو).',
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'       => 'تکرار (حلقه)',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label_on'    => 'روشن',
				'label_off'   => 'خاموش',
				'default'     => '',
				'description' => 'روشن: پس از پایان تایپ، مکث کرده و دوباره از اول تایپ می‌شود.',
			)
		);

		$this->add_control(
			'loop_pause',
			array(
				'label'       => 'مکث بین تکرارها (میلی‌ثانیه)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 1200,
				'min'         => 200,
				'max'         => 10000,
				'step'        => 100,
				'condition'   => array( 'loop' => 'yes' ),
				'description' => 'چقدر مکث کند قبل از شروع دوباره. 1200 مقدار پیشنهادی.',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => 'ظاهر متن',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'align',
			array(
				'label'       => 'چیدمان متن',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'start',
				'options'     => array(
					'start'  => 'از ابتدا (چپ در انگلیسی)',
					'center' => 'وسط‌چین',
					'end'    => 'از انتها (راست در انگلیسی)',
					'left'   => 'چپ',
					'right'  => 'راست',
				),
				'description' => 'چینش خط تایپ‌شده. تغییر آنی است و بدون رندر مجدد اعمال می‌شود.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-typewriter' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'letter_spacing',
			array(
				'label'       => 'فاصلهٔ حروف',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'مثلاً: 0.05em یا 1px',
				'description' => 'خالی = حالت عادی. تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-typewriter' => 'letter-spacing: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'font_family',
			array(
				'label'       => 'فونت دلخواه',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => "خالی = فونت متن سایت؛ مثال: 'Vazirmatn', sans-serif",
				'description' => 'خالی بگذارید تا از فونت متن سایت استفاده شود (همان دمو). برای فونت تیتر: var(--mf-heading, inherit) وارد کنید. تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-typewriter' => 'font-family: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'font_size',
			array(
				'label'       => 'اندازهٔ فونت',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'clamp(18px, 4vw, 26px)',
				'description' => 'با تابع CSS clamp روی همهٔ صفحه‌ها خوانا می‌ماند (مثل دمو: حداقل 18، حداکثر 26 پیکسل). هر مقدار CSS معتبر دیگری هم می‌شود: 20px، 1.4rem، …',
			)
		);

		$this->add_control(
			'line_height',
			array(
				'label'       => 'ارتفاع خط',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '1.35',
				'description' => 'نسبت ارتفاع خط به اندازهٔ فونت. 1.35 همان مقدار دمو.',
			)
		);

		$this->add_control(
			'min_height',
			array(
				'label'       => 'حداقل ارتفاع',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '54px',
				'description' => 'جلوگیری از پرش چیدمان بقیهٔ صفحه حین تایپ. 54px در دمو کافی است؛ اگر متن چندخطی دارید بزرگ‌ترش کنید.',
			)
		);

		$this->add_control(
			'color',
			array(
				'label'       => 'رنگ متن',
				'type'        => \Elementor\Controls_Manager::COLOR,
				'default'     => '#FFFFFF',
			)
		);

		$this->add_control(
			'weight',
			array(
				'label'       => 'ضخامت فونت',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '400',
				'options'     => array(
					'300' => '300 — نازک',
					'400' => '400 — معمولی (دمو)',
					'500' => '500 — نیمه‌به‌ظاهر',
					'700' => '700 — ضخیم',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'       => 'جهت متن',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'ltr',
				'options'     => array(
					'ltr'  => 'چپ به راست (دمو)',
					'rtl'  => 'راست به چپ — برای جمله‌های فارسی/عربی',
					'auto' => 'خودکار (ارث از قالب)',
				),
				'description' => 'برای جمله‌های فارسی «راست به چپ» را بگذارید تا نقطه‌گذاری و فاصله‌ها درست رندر شود.',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cursor',
			array(
				'label' => 'بصر چشمک‌زن',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'cursor',
			array(
				'label'       => 'نمایش بصر',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => 'خط عمودی کنار متن؛ با انیمیشن 1 ثانیه‌ای چشمک می‌زند و به‌محض پایان تایپ پنهان می‌شود (رفتار دقیق دمو).',
			)
		);

		$this->add_control(
			'cursor_color',
			array(
				'label'     => 'رنگ بصر',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'condition' => array( 'cursor' => 'yes' ),
			)
		);

		$this->add_control(
			'cursor_blink',
			array(
				'label'       => 'سرعت چشمک',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '1s',
				'options'     => array(
					'1s'   => 'معمولی — ۱ ثانیه (دمو)',
					'0.5s' => 'سریع — نیم ثانیه',
					'2s'   => 'کند — ۲ ثانیه',
					'0s'   => 'ثابت (بدون چشمک)',
				),
				'description' => 'دورهٔ چشمک‌زدن بصر. تغییر آنی است.',
				'condition'   => array( 'cursor' => 'yes' ),
				'selectors'   => array(
					'{{WRAPPER}} .mf-typewriter-cursor' => 'animation-duration: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$style = 'font-size:' . esc_attr( $settings['font_size'] ) . ';'
			. 'line-height:' . esc_attr( $settings['line_height'] ) . ';'
			. 'min-height:' . esc_attr( $settings['min_height'] ) . ';'
			. 'color:' . esc_attr( $settings['color'] ) . ';'
			. 'font-weight:' . esc_attr( $settings['weight'] ) . ';'
			. 'direction:' . esc_attr( $settings['direction'] ) . ';';

		$this->add_render_attribute(
			'wrap',
			array(
				'class'          => 'mf-typewriter',
				'style'          => $style,
				'data-text'      => $settings['text'],
				'data-speed'     => (string) mf_num( $settings['speed'], 38 ),
				'data-delay'     => (string) mf_num( $settings['delay'], 600 ),
				'data-loop'      => 'yes' === ( isset( $settings['loop'] ) ? $settings['loop'] : '' ) ? '1' : '0',
				'data-loop-pause' => (string) mf_num( isset( $settings['loop_pause'] ) ? $settings['loop_pause'] : null, 1200 ),
				'data-onview'    => 'yes' === ( isset( $settings['start_on_view'] ) ? $settings['start_on_view'] : '' ) ? '1' : '0',
			)
		);

		echo '<p ' . $this->get_render_attribute_string( 'wrap' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<span class="mf-typewriter-text"></span>';
		if ( 'yes' === $settings['cursor'] ) {
			echo '<span class="mf-typewriter-cursor" aria-hidden="true" style="background:' . esc_attr( $settings['cursor_color'] ) . '"></span>';
		}
		echo '</p>';
	}
}
