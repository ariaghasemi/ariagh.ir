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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => 'ظاهر متن',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
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
				'class'      => 'mf-typewriter',
				'style'      => $style,
				'data-text'  => $settings['text'],
				'data-speed' => (string) mf_num( $settings['speed'], 38 ),
				'data-delay' => (string) mf_num( $settings['delay'], 600 ),
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
