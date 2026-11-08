<?php
/**
 * Widget: Blur Intro.
 *
 * The two soft lines that sit above the typewriter: present, but out of focus.
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

class Blur_Intro extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mfh-blur-intro';
	}

	/**
	 * Panel title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'متنِ آغازینِ محو', 'mainframe-hero' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-text-area';
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
		return array( 'blur', 'intro', 'text', 'محو', 'متن' );
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
	 * Register the controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->section_content();
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
			'line_one',
			array(
				'label'       => __( 'سطرِ نخست', 'mainframe-hero' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Hey there, meet A.R.I.A,',
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'show_line_two',
			array(
				'label'        => __( 'سطرِ دوم', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'line_two',
			array(
				'label'     => __( 'متنِ سطرِ دوم', 'mainframe-hero' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => "Mainframe's Adaptive Response Interface Agent",
				'condition' => array( 'show_line_two' => 'yes' ),
				'dynamic'   => array( 'active' => true ),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'       => __( 'برچسبِ اچ‌تی‌ام‌ال', 'mainframe-hero' ),
				'description' => __( 'برای سئو و ساختارِ صفحه مهم است. پیش‌فرض پاراگراف است.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'p',
				'options'     => array(
					'p'   => 'p',
					'h1'  => 'h1',
					'h2'  => 'h2',
					'h3'  => 'h3',
					'div' => 'div',
					'span' => 'span',
				),
			)
		);

		$this->add_control(
			'interactive',
			array(
				'label'        => __( 'قابلِ انتخاب‌کردن', 'mainframe-hero' ),
				'description'  => __( 'در این طرح این متن تزیینی است و نه انتخاب می‌شود و نه روی رویدادهای اشاره اثر می‌گذارد. اگر می‌خواهید بازدیدکننده بتواند آن را انتخاب کند، این گزینه را روشن کنید.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => '',
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
				'description'  => __( 'با فعال‌بودن، اندازهٔ قلم میانِ کمینه و بیشینه به‌تناسبِ عرضِ صفحه تغییر می‌کند — همان رفتارِ طرحِ اصلی. در غیر این صورت اندازهٔ ثابتِ زیر به کار می‌رود.', 'mainframe-hero' ),
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
				'label'       => __( 'تناسب با عرضِ صفحه (vw)', 'mainframe-hero' ),
				'description' => __( 'هر یک واحد یعنی یک درصد از عرضِ صفحه. مقدارِ ۴ یعنی اندازهٔ قلم چهار درصدِ عرضِ پنجره.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 0.5, 'max' => 20, 'step' => 0.1 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 4 ),
				'condition'   => array( 'fluid' => 'yes' ),
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
				'selector'       => '{{WRAPPER}} .mfh-blur',
				'fields_options' => array(
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 26 ) ),
					'font_weight' => array( 'default' => '400' ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.3 ) ),
				),
			)
		);

		$this->add_control(
			'blur',
			array(
				'label'       => __( 'میزانِ تاری (پیکسل)', 'mainframe-hero' ),
				'description' => __( 'در این طرح ۴ پیکسل است: متن خوانده می‌شود اما واضح نیست و توجهِ اصلی را نمی‌دزدد.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 30, 'step' => 0.5 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 4 ),
				'selectors'   => array(
					'{{WRAPPER}} .mfh-blur' => 'filter: blur({{SIZE}}{{UNIT}});',
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
					'{{WRAPPER}} .mfh-blur' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .mfh-blur' => 'text-align: {{VALUE}};',
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
					'{{WRAPPER}} .mfh-blur' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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

		$this->add_render_attribute( 'text', 'class', 'mfh-blur' );

		if ( ! mfh_bool( $settings['interactive'] ) ) {
			$this->add_render_attribute( 'text', 'class', 'mfh-blur--inert' );
		}

		if ( mfh_bool( $settings['fluid'] ) ) {
			$min = isset( $settings['fluid_min']['size'] ) ? (float) $settings['fluid_min']['size'] : 18;
			$vw  = isset( $settings['fluid_vw']['size'] ) ? (float) $settings['fluid_vw']['size'] : 4;
			$max = isset( $settings['fluid_max']['size'] ) ? (float) $settings['fluid_max']['size'] : 26;
			$this->add_render_attribute( 'text', 'style', sprintf( 'font-size: clamp(%1$spx, %2$svw, %3$spx);', $min, $vw, $max ) );
		}
		?>
		<<?php echo esc_attr( $tag ); ?> <?php $this->print_render_attribute_string( 'text' ); ?>>
			<?php echo esc_html( $settings['line_one'] ); ?>
			<?php if ( mfh_bool( $settings['show_line_two'] ) ) : ?>
				<br />
				<?php echo esc_html( $settings['line_two'] ); ?>
			<?php endif; ?>
		</<?php echo esc_attr( $tag ); ?>>
		<?php
	}
}
