<?php
/**
 * Widget: Typewriter.
 *
 * Reveals a line of copy one character at a time, with a blinking caret that
 * disappears the moment the sentence is complete.
 *
 * @package MainframeHero
 */

namespace MFH\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Typewriter extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mfh-typewriter';
	}

	/**
	 * Panel title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'متنِ تایپ‌شونده', 'mainframe-hero' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-animation-text';
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
		return array( 'typewriter', 'typing', 'text', 'تایپ', 'متن' );
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
	 * Register the controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->section_content();
		$this->section_caret();
		$this->section_style();
	}

	/**
	 * Content section.
	 *
	 * @return void
	 */
	private function section_content() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'متن', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'text',
			array(
				'label'       => __( 'جمله', 'mainframe-hero' ),
				'description' => __( 'متنی که حرف‌به‌حرف ظاهر می‌شود. هرچه بلندتر باشد، مدتِ تایپ طولانی‌تر می‌شود: زمان تقریبی برابر است با شمارِ حروف ضرب‌در سرعت.', 'mainframe-hero' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Glad you stopped in. Good taste tends to find us. Now, what are we building?',
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'       => __( 'سرعت (میلی‌ثانیه برای هر حرف)', 'mainframe-hero' ),
				'description' => __( 'فاصلهٔ میانِ دو حرف. مقدارِ کمتر تندتر است. مقدارِ این طرح ۳۸ میلی‌ثانیه است.', 'mainframe-hero' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 5,
				'max'         => 500,
				'step'        => 1,
				'default'     => 38,
			)
		);

		$this->add_control(
			'start_delay',
			array(
				'label'       => __( 'تأخیرِ آغاز (میلی‌ثانیه)', 'mainframe-hero' ),
				'description' => __( 'چه قدر پس از بارگذاریِ صفحه تایپ شروع شود. این تأخیر فقط روی تایپ اثر دارد، نه روی دکمه‌ها.', 'mainframe-hero' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 10000,
				'step'        => 50,
				'default'     => 600,
			)
		);

		$this->add_control(
			'when',
			array(
				'label'       => __( 'زمانِ اجرا', 'mainframe-hero' ),
				'description' => __( '«بلافاصله» با بارگذاریِ صفحه شروع می‌شود. «هنگامِ دیده‌شدن» صبر می‌کند تا بخش واردِ دید شود — برای بخش‌های پایینِ صفحه مناسب است.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'load',
				'options'     => array(
					'load'    => __( 'بلافاصله', 'mainframe-hero' ),
					'visible' => __( 'هنگامِ دیده‌شدن', 'mainframe-hero' ),
				),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => __( 'برچسبِ اچ‌تی‌ام‌ال', 'mainframe-hero' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'p',
				'options' => array(
					'p'   => 'p',
					'h1'  => 'h1',
					'h2'  => 'h2',
					'h3'  => 'h3',
					'div' => 'div',
				),
			)
		);

		$this->add_control(
			'reduced_motion',
			array(
				'label'       => __( 'رفتار در حالتِ کاهشِ حرکت', 'mainframe-hero' ),
				'description' => __( 'وقتی بازدیدکننده کاهشِ حرکت را فعال کرده باشد، «نمایشِ کامل» کلِ جمله را بی‌درنگ نشان می‌دهد و از تایپ صرف‌نظر می‌کند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'instant',
				'options'     => array(
					'instant' => __( 'نمایشِ کامل', 'mainframe-hero' ),
					'keep'    => __( 'تایپ ادامه یابد', 'mainframe-hero' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Caret section.
	 *
	 * @return void
	 */
	private function section_caret() {
		$this->start_controls_section(
			'section_caret',
			array(
				'label' => __( 'نشانگر', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_caret',
			array(
				'label'        => __( 'نمایشِ نشانگر', 'mainframe-hero' ),
				'description'  => __( 'خطِ چشمک‌زنِ کنارِ متن که هنگامِ تایپ دیده می‌شود و با پایانِ جمله ناپدید می‌گردد.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'caret_color',
			array(
				'label'     => __( 'رنگِ نشانگر', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'condition' => array( 'show_caret' => 'yes' ),
				'selectors' => array(
					'{{WRAPPER}} .mfh-type__caret' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'caret_width',
			array(
				'label'      => __( 'پهنای نشانگر (پیکسل)', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 12 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 2 ),
				'condition'  => array( 'show_caret' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-type__caret' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'caret_height',
			array(
				'label'      => __( 'بلندای نشانگر', 'mainframe-hero' ),
				'description' => __( 'بر حسبِ اندازهٔ قلم (em). مقدارِ ۱/۱ یعنی کمی بلندتر از یک خط.', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array( 'em' => array( 'min' => 0.3, 'max' => 2, 'step' => 0.05 ) ),
				'default'    => array( 'unit' => 'em', 'size' => 1.1 ),
				'condition'  => array( 'show_caret' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-type__caret' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'caret_speed',
			array(
				'label'      => __( 'زمانِ چشمک (ثانیه)', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0.2, 'max' => 3, 'step' => 0.1 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 1 ),
				'condition'  => array( 'show_caret' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-type__caret' => 'animation-duration: {{SIZE}}s;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style section.
	 *
	 * @return void
	 */
	private function section_style() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'متن', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'fluid',
			array(
				'label'        => __( 'اندازهٔ شناور', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'شناور', 'mainframe-hero' ),
				'label_off'    => __( 'ثابت', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'fluid_min',
			array(
				'label'     => __( 'کمینه (پیکسل)', 'mainframe-hero' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 8, 'max' => 200 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 18 ),
				'condition' => array( 'fluid' => 'yes' ),
			)
		);

		$this->add_control(
			'fluid_vw',
			array(
				'label'     => __( 'تناسب با عرضِ صفحه (vw)', 'mainframe-hero' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0.5, 'max' => 20, 'step' => 0.1 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 4 ),
				'condition' => array( 'fluid' => 'yes' ),
			)
		);

		$this->add_control(
			'fluid_max',
			array(
				'label'     => __( 'بیشینه (پیکسل)', 'mainframe-hero' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 8, 'max' => 200 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 26 ),
				'condition' => array( 'fluid' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'typography',
				'label'          => __( 'تایپوگرافی', 'mainframe-hero' ),
				'selector'       => '{{WRAPPER}} .mfh-type',
				'fields_options' => array(
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 26 ) ),
					'font_weight' => array( 'default' => '400' ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.35 ) ),
				),
			)
		);

		$this->add_control(
			'color',
			array(
				'label'     => __( 'رنگ', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-type' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'min_height',
			array(
				'label'       => __( 'کمینهٔ ارتفاع (پیکسل)', 'mainframe-hero' ),
				'description' => __( 'جلوگیری از پرشِ صفحه هنگامی که متن سطر‌به‌سطر اضافه می‌شود. ارتفاع را پیشاپیش رزرو می‌کند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 400 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 54 ),
				'selectors'   => array(
					'{{WRAPPER}} .mfh-type' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'تراز', 'mainframe-hero' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'start'  => array(
						'title' => __( 'راست / چپ', 'mainframe-hero' ),
						'icon'  => 'eicon-text-align-right',
					),
					'center' => array(
						'title' => __( 'میان', 'mainframe-hero' ),
						'icon'  => 'eicon-text-align-center',
					),
					'end'    => array(
						'title' => __( 'چپ / راست', 'mainframe-hero' ),
						'icon'  => 'eicon-text-align-left',
					),
				),
				'default'   => 'start',
				'selectors' => array(
					'{{WRAPPER}} .mfh-type' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'space_below',
			array(
				'label'      => __( 'فاصله تا بخشِ بعدی', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-type' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build the front-end markup.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$tag      = isset( $settings['html_tag'] ) ? $settings['html_tag'] : 'p';
		$text     = isset( $settings['text'] ) ? (string) $settings['text'] : '';

		$payload = array(
			'text'          => $text,
			'speed'         => isset( $settings['speed'] ) ? (int) $settings['speed'] : 38,
			'startDelay'    => isset( $settings['start_delay'] ) ? (int) $settings['start_delay'] : 600,
			'when'          => isset( $settings['when'] ) ? $settings['when'] : 'load',
			'reducedMotion' => isset( $settings['reduced_motion'] ) ? $settings['reduced_motion'] : 'instant',
		);

		$this->add_render_attribute( 'wrapper', 'class', 'mfh-type' );
		$this->add_render_attribute( 'wrapper', 'data-mfh-type', wp_json_encode( $payload ) );

		// The full sentence always ships in the markup inside a visually hidden
		// span, so search engines and screen readers get it even before — or
		// without — JavaScript. The animated copy is hidden from them instead.
		if ( mfh_bool( $settings['fluid'] ) ) {
			$min = isset( $settings['fluid_min']['size'] ) ? (float) $settings['fluid_min']['size'] : 18;
			$vw  = isset( $settings['fluid_vw']['size'] ) ? (float) $settings['fluid_vw']['size'] : 4;
			$max = isset( $settings['fluid_max']['size'] ) ? (float) $settings['fluid_max']['size'] : 26;
			$this->add_render_attribute( 'wrapper', 'style', sprintf( 'font-size: clamp(%1$spx, %2$svw, %3$spx);', $min, $vw, $max ) );
		}

		?>
		<<?php echo esc_attr( $tag ); ?> <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<span class="mfh-type__text" aria-hidden="true"></span><?php
			if ( mfh_bool( $settings['show_caret'] ) ) :
				?><span class="mfh-type__caret" aria-hidden="true"></span><?php
			endif;
			?><span class="mfh-type__sr"><?php echo esc_html( $text ); ?></span>
		</<?php echo esc_attr( $tag ); ?>>
		<?php
	}
}
