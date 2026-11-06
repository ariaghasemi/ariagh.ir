<?php
/**
 * ویجت: برچسب مقدمه (تار)
 *
 * دو خط متن با blur که در ابتدای هیرو دیده می‌شود
 * (همان لیبل tajawoz-shodeh-ye دمو).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget_Mainframe_Intro_Label extends \Elementor\Widget_Base {

	public function get_name() {
		return 'mainframe-intro-label';
	}

	public function get_title() {
		return 'برچسب مقدمه (تار)';
	}

	public function get_icon() {
		return 'eicon-typography';
	}

	public function get_categories() {
		return array( 'mainframe' );
	}

	public function get_keywords() {
		return array( 'برچسب', 'تار', 'blur', 'مقدمه', 'لیبل' );
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
				'label' => 'متن برچسب',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'line1',
			array(
				'label'       => 'خط اول',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'Hey there, meet A.R.I.A,',
				'description' => 'خط اول برچسب. در دمو: «Hey there, meet A.R.I.A,»',
			)
		);

		$this->add_control(
			'line2',
			array(
				'label'       => 'خط دوم',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => "Mainframe's Adaptive Response Interface Agent",
				'description' => 'خط دوم؛ همیشه با شکستن خط از خط اول جدا می‌شود (در دمو یک <br> بین آن‌هاست).',
			)
		);

		$this->add_control(
			'show_line2',
			array(
				'label'       => 'نمایش خط دوم',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'label_on'    => 'روشن',
				'label_off'   => 'خاموش',
				'default'     => 'yes',
				'description' => 'خاموش: فقط خط اول نمایش داده می‌شود.',
			)
		);

		$this->add_control(
			'entrance',
			array(
				'label'       => 'انیمیشن ورود',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''       => 'هیچ (دمو — بدون انیمیشن)',
					'fade'   => 'محو شدن',
					'fade-up' => 'محو + بالا آمدن',
				),
				'description' => 'هنگام ورود به صفحه چگونه ظاهر شود. با هر رندر مجدد در ادیتور دوباره اجرا می‌شود تا نتیجه را ببینید.',
			)
		);

		$this->add_control(
			'entrance_duration',
			array(
				'label'       => 'مدت انیمیشن (میلی‌ثانیه)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 800,
				'min'         => 100,
				'max'         => 3000,
				'step'        => 50,
				'description' => 'فقط وقتی انیمیشن ورود فعال باشد اعمال می‌شود.',
			)
		);

		$this->add_control(
			'blur',
			array(
				'label'       => 'میزان تاری (پیکسل)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 4,
				'min'         => 0,
				'max'         => 12,
				'step'        => 0.5,
				'description' => 'شعاع فیلتر blur. 4 همان مقدار دمو است؛ صفر یعنی تار نکردن، بالای 6 دیگر متن خوانا نمی‌ماند.',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => 'ظاهر',
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
				'description' => 'تغییر آنی است و بدون رندر مجدد اعمال می‌شود.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-intro' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'opacity',
			array(
				'label'       => 'شفافیت (درصد)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 100,
				'min'         => 0,
				'max'         => 100,
				'step'        => 1,
				'description' => '100 = دیده‌شدن کامل (دمو). برای محوتر شدن کم کنید. تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-intro' => 'opacity: calc({{VALUE}} / 100);',
				),
			)
		);

		$this->add_control(
			'letter_spacing',
			array(
				'label'       => 'فاصلهٔ حروف',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'مثلاً: 0.03em',
				'description' => 'خالی = حالت عادی. تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-intro' => 'letter-spacing: {{VALUE}};',
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
				'description' => 'خالی = فونت متن سایت (دمو). برای فونت تیتر: var(--mf-heading, inherit). تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-intro' => 'font-family: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'font_size',
			array(
				'label'       => 'اندازهٔ فونت',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'clamp(18px, 4vw, 26px)',
				'description' => 'مثل دمو با clamp: حداقل 18، حداکثر 26 پیکسل بر اساس عرض صفحه.',
			)
		);

		$this->add_control(
			'line_height',
			array(
				'label'       => 'ارتفاع خط',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '1.3',
				'description' => 'فاصلهٔ بین دو خط برچسب (دمو: 1.3).',
			)
		);

		$this->add_control(
			'color',
			array(
				'label' => 'رنگ متن',
				'type'  => \Elementor\Controls_Manager::COLOR,
				'default' => '#FFFFFF',
			)
		);

		$this->add_control(
			'weight',
			array(
				'label'   => 'ضخامت فونت',
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '400',
				'options' => array(
					'300' => '300 — نازک',
					'400' => '400 — معمولی (دمو)',
					'500' => '500 — نیمه‌به‌ظاهر',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => 'جهت متن',
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ltr',
				'options' => array(
					'ltr'  => 'چپ به راست (دمو)',
					'rtl'  => 'راست به چپ — برای فارسی/عربی',
					'auto' => 'خودکار (ارث از قالب)',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$blur = mf_num( $settings['blur'], 4 );

		$wrap_style = 'font-size:' . esc_attr( $settings['font_size'] ) . ';'
			. 'line-height:' . esc_attr( $settings['line_height'] ) . ';'
			. 'color:' . esc_attr( $settings['color'] ) . ';'
			. 'font-weight:' . esc_attr( $settings['weight'] ) . ';'
			. 'direction:' . esc_attr( $settings['direction'] ) . ';';

		$entrance = isset( $settings['entrance'] ) ? $settings['entrance'] : '';
		$classes  = array( 'mf-intro' );
		if ( $entrance ) {
			$classes[] = 'mf-intro--' . sanitize_html_class( $entrance );
			$wrap_style .= '--mf-intro-dur:' . intval( mf_num( isset( $settings['entrance_duration'] ) ? $settings['entrance_duration'] : null, 800 ) ) . 'ms;';
		}

		$this->add_render_attribute(
			'wrap',
			array(
				'class' => $classes,
				'style' => $wrap_style,
			)
		);

		$this->add_render_attribute(
			'blur',
			array(
				'class' => 'mf-intro-blur',
				'style' => 'filter:blur(' . esc_attr( $blur ) . 'px);',
			)
		);

		$show_line2 = 'yes' === ( isset( $settings['show_line2'] ) ? $settings['show_line2'] : 'yes' );

		echo '<div ' . $this->get_render_attribute_string( 'wrap' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div ' . $this->get_render_attribute_string( 'blur' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo esc_html( $settings['line1'] );
		if ( $show_line2 && '' !== (string) $settings['line2'] ) {
			echo '<br>';
			echo esc_html( $settings['line2'] );
		}
		echo '</div>';
		echo '</div>';
	}
}
