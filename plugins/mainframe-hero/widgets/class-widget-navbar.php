<?php
/**
 * Widget: Navbar.
 *
 * The fixed top bar: wordmark with its asterisk, comma-separated links, an
 * underlined call to action, and a hamburger that collapses into a cross.
 *
 * @package MainframeHero
 */

namespace MFH\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Navbar extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mfh-navbar';
	}

	/**
	 * Panel title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'ناوبار', 'mainframe-hero' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-menu-bar';
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
		return array( 'nav', 'menu', 'header', 'منو', 'هدر', 'ناوبار' );
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
		$this->section_brand();
		$this->section_links();
		$this->section_mobile();
		$this->section_style_bar();
		$this->section_style_brand();
		$this->section_style_links();
		$this->section_style_overlay();
	}

	/* --------------------------------------------------------------------- */
	/* Content                                                                */
	/* --------------------------------------------------------------------- */

	/**
	 * Brand section.
	 *
	 * @return void
	 */
	private function section_brand() {
		$this->start_controls_section(
			'section_brand',
			array(
				'label' => __( 'نشان', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'brand_text',
			array(
				'label'       => __( 'نامِ نشان', 'mainframe-hero' ),
				'description' => __( 'متنِ سمتِ راستِ ناوبار. برای نمادِ ثبت‌شده می‌توانید از نویسهٔ ® استفاده کنید.', 'mainframe-hero' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Mainframe®',
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'brand_link',
			array(
				'label'       => __( 'پیوندِ نشان', 'mainframe-hero' ),
				'description' => __( 'معمولاً به صفحهٔ اصلی اشاره می‌کند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => home_url( '/' ) ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'show_mark',
			array(
				'label'        => __( 'نمایشِ نشانهٔ ستاره', 'mainframe-hero' ),
				'description'  => __( 'نویسهٔ تزیینیِ کنارِ نام را نشان می‌دهد.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'mark_char',
			array(
				'label'       => __( 'نویسهٔ نشانه', 'mainframe-hero' ),
				'description' => __( 'هر نویسه‌ای می‌تواند باشد. پیش‌فرضِ این طرح ستارهٔ شش‌پر است.', 'mainframe-hero' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '✳︎',
				'condition'   => array( 'show_mark' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Links section.
	 *
	 * @return void
	 */
	private function section_links() {
		$this->start_controls_section(
			'section_links',
			array(
				'label' => __( 'پیوندها', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'عنوان', 'mainframe-hero' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'پیوند', 'mainframe-hero' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'   => __( 'پیوند', 'mainframe-hero' ),
				'type'    => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'links',
			array(
				'label'       => __( 'فهرستِ پیوندها', 'mainframe-hero' ),
				'description' => __( 'به ترتیب نمایش داده می‌شوند. در نمای رومیزی میان‌شان ویرگول گذاشته می‌شود.', 'mainframe-hero' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'text' => 'Labs' ),
					array( 'text' => 'Studio' ),
					array( 'text' => 'Openings' ),
					array( 'text' => 'Shop' ),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->add_control(
			'show_separator',
			array(
				'label'        => __( 'جداکنندهٔ ویرگول', 'mainframe-hero' ),
				'description'  => __( 'در نمای رومیزی میانِ پیوندها «،» می‌گذارد. در نمای موبایل اثری ندارد.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_cta',
			array(
				'label'        => __( 'نمایشِ دکمهٔ تماس', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'متنِ دکمهٔ تماس', 'mainframe-hero' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Get in touch',
				'condition' => array( 'show_cta' => 'yes' ),
				'dynamic'   => array( 'active' => true ),
			)
		);

		$this->add_control(
			'cta_link',
			array(
				'label'     => __( 'پیوندِ دکمهٔ تماس', 'mainframe-hero' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'show_cta' => 'yes' ),
				'dynamic'   => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Mobile section.
	 *
	 * @return void
	 */
	private function section_mobile() {
		$this->start_controls_section(
			'section_mobile',
			array(
				'label' => __( 'نمای موبایل', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'breakpoint',
			array(
				'label'       => __( 'نقطهٔ شکست (پیکسل)', 'mainframe-hero' ),
				'description' => __( 'از این عرض به پایین، پیوندها پنهان و دکمهٔ همبرگر نمایش داده می‌شود. پیش‌فرضِ این طرح ۷۶۷ پیکسل است که با نقطهٔ شکستِ رایجِ ۷۶۸ پیکسل هم‌خوانی دارد.', 'mainframe-hero' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 320,
				'max'         => 1600,
				'default'     => 767,
			)
		);

		$this->add_control(
			'show_overlay_cta',
			array(
				'label'        => __( 'دکمهٔ تماس در پوشش', 'mainframe-hero' ),
				'description'  => __( 'در پوششِ موبایل، زیرِ پیوندها یک دکمهٔ تماسِ خط‌دار هم نمایش داده می‌شود.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'hamburger_gap',
			array(
				'label'       => __( 'فاصلهٔ خط‌های همبرگر', 'mainframe-hero' ),
				'description' => __( 'فاصلهٔ میانِ سه خط. برای این‌که در حالتِ باز، ضربدرِ درستی ساخته شود، مقدار باید با مجموعِ «ضخامتِ خط» و همین فاصله هماهنگ باشد.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 1, 'max' => 20 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 5 ),
				'selectors'   => array(
					'{{WRAPPER}} .mfh-nav__burger' => '--mfh-burger-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hamburger_thickness',
			array(
				'label'      => __( 'ضخامتِ خط (پیکسل)', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 10 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 2 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav__burger' => '--mfh-burger-thickness: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hamburger_width',
			array(
				'label'      => __( 'عرضِ خط (پیکسل)', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav__burger' => '--mfh-burger-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/* --------------------------------------------------------------------- */
	/* Style                                                                  */
	/* --------------------------------------------------------------------- */

	/**
	 * Bar style.
	 *
	 * @return void
	 */
	private function section_style_bar() {
		$this->start_controls_section(
			'section_style_bar',
			array(
				'label' => __( 'میله', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => __( 'فاصلهٔ درونی', 'mainframe-hero' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => 16,
					'right'    => 20,
					'bottom'   => 16,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bar_justify',
			array(
				'label'       => __( 'چیدمان', 'mainframe-hero' ),
				'description' => __( '«دو سر» نشان را در یک سو و دکمه‌ها را در سوی دیگر می‌گذارد و پیوندها در میانه می‌نشینند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'space-between',
				'options'     => array(
					'space-between' => __( 'دو سر', 'mainframe-hero' ),
					'flex-start'    => __( 'چپ / راست', 'mainframe-hero' ),
					'center'        => __( 'میان', 'mainframe-hero' ),
					'flex-end'      => __( 'انتهای دیگر', 'mainframe-hero' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .mfh-nav' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'z_index',
			array(
				'label'     => __( 'لایه (z-index)', 'mainframe-hero' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 10,
				'selectors' => array(
					'{{WRAPPER}} .mfh-nav' => 'z-index: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_z_index',
			array(
				'label'       => __( 'لایهٔ پوشش (z-index)', 'mainframe-hero' ),
				'description' => __( 'یک واحد کمتر از لایهٔ میله نگه داشته شود تا دکمهٔ بستن روی پوشش بماند و همیشه قابلِ کلیک باشد.', 'mainframe-hero' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 9,
				'selectors'   => array(
					'{{WRAPPER}} .mfh-nav__overlay' => 'z-index: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Brand style.
	 *
	 * @return void
	 */
	private function section_style_brand() {
		$this->start_controls_section(
			'section_style_brand',
			array(
				'label' => __( 'نشان', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'brand_typography',
				'label'          => __( 'تایپوگرافیِ نام', 'mainframe-hero' ),
				'description'    => __( 'در این طرح نام با یک قلمِ نمایشیِ متمایز تنظیم می‌شود و بقیهٔ صفحه با قلمِ متن.', 'mainframe-hero' ),
				'selector'       => '{{WRAPPER}} .mfh-nav__brand-text',
				'fields_options' => array(
					'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 26 ) ),
				),
			)
		);

		$this->add_control(
			'brand_color',
			array(
				'label'     => __( 'رنگِ نام', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-nav__brand-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'brand_gap',
			array(
				'label'      => __( 'فاصله تا نشانه', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav__brand' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mark_size',
			array(
				'label'      => __( 'اندازهٔ نشانه', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 30 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav__brand-mark' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mark_spacing',
			array(
				'label'      => __( 'فاصلهٔ حروفِ نشانه', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array( 'em' => array( 'min' => -0.2, 'max' => 0.5, 'step' => 0.01 ) ),
				'default'    => array( 'unit' => 'em', 'size' => -0.02 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav__brand-mark' => 'letter-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mark_color',
			array(
				'label'     => __( 'رنگِ نشانه', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-nav__brand-mark' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Links style.
	 *
	 * @return void
	 */
	private function section_style_links() {
		$this->start_controls_section(
			'section_style_links',
			array(
				'label' => __( 'پیوندها', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'links_typography',
				'label'          => __( 'تایپوگرافی', 'mainframe-hero' ),
				'selector'       => '{{WRAPPER}} .mfh-nav__links a, {{WRAPPER}} .mfh-nav__cta',
				'fields_options' => array(
					'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 23 ) ),
				),
			)
		);

		$this->add_control(
			'links_color',
			array(
				'label'     => __( 'رنگ', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-nav__links a, {{WRAPPER}} .mfh-nav__cta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'links_gap',
			array(
				'label'      => __( 'فاصلهٔ میان پیوندها', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav__links' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'links_opacity_hover',
			array(
				'label'       => __( 'کدری در حالتِ اشاره', 'mainframe-hero' ),
				'description' => __( 'مقداری میانِ صفر و یک. در این طرح پیوندها هنگامِ اشاره به ۶۰ درصد می‌رسند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 0.6 ),
				'selectors'   => array(
					'{{WRAPPER}} .mfh-nav__links a:hover, {{WRAPPER}} .mfh-nav__cta:hover' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Overlay style.
	 *
	 * @return void
	 */
	private function section_style_overlay() {
		$this->start_controls_section(
			'section_style_overlay',
			array(
				'label' => __( 'پوششِ موبایل', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_bg',
			array(
				'label'     => __( 'رنگِ پس‌زمینه', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.9)',
				'selectors' => array(
					'{{WRAPPER}} .mfh-nav__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_blur',
			array(
				'label'      => __( 'تاریِ پس‌زمینه (پیکسل)', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav__overlay' => 'backdrop-filter: blur({{SIZE}}{{UNIT}}); -webkit-backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'overlay_typography',
				'label'          => __( 'تایپوگرافیِ پیوندها', 'mainframe-hero' ),
				'selector'       => '{{WRAPPER}} .mfh-nav__overlay a',
				'fields_options' => array(
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 32 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'رنگِ پیوندها', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-nav__overlay a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_gap',
			array(
				'label'      => __( 'فاصلهٔ میانِ ردیف‌ها', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 32 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav__overlay' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'overlay_padding',
			array(
				'label'      => __( 'فاصله از لبه', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 32 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-nav__overlay' => 'padding-inline: {{SIZE}}{{UNIT}};',
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

		$breakpoint = isset( $settings['breakpoint'] ) ? max( 320, (int) $settings['breakpoint'] ) : 767;
		$uid        = 'mfh-nav-' . $this->get_id();

		// The breakpoint cannot live in a media query — custom properties are
		// not valid there — so it travels as data and JavaScript applies the
		// matching layout class.
		$payload = array(
			'breakpoint' => $breakpoint,
		);

		$this->add_render_attribute( 'root', 'class', 'mfh-nav-root' );
		$this->add_render_attribute( 'root', 'id', $uid );
		$this->add_render_attribute( 'root', 'data-mfh-nav', wp_json_encode( $payload ) );

		$brand_url = isset( $settings['brand_link']['url'] ) ? $settings['brand_link']['url'] : '';
		$brand_tag = $brand_url ? 'a' : 'span';

		$links = isset( $settings['links'] ) && is_array( $settings['links'] ) ? $settings['links'] : array();
		?>
		<div <?php $this->print_render_attribute_string( 'root' ); ?>>
			<header class="mfh-nav">
				<<?php echo esc_attr( $brand_tag ); ?>
					class="mfh-nav__brand"
					<?php if ( $brand_url ) : ?>
					href="<?php echo esc_url( $brand_url ); ?>"
					<?php endif; ?>
				>
					<span class="mfh-nav__brand-text"><?php echo esc_html( $settings['brand_text'] ); ?></span>
					<?php if ( mfh_bool( $settings['show_mark'] ) ) : ?>
						<span class="mfh-nav__brand-mark" aria-hidden="true"><?php echo esc_html( $settings['mark_char'] ); ?></span>
					<?php endif; ?>
				</<?php echo esc_attr( $brand_tag ); ?>>

				<?php if ( $links ) : ?>
					<nav class="mfh-nav__links" aria-label="<?php esc_attr_e( 'فهرستِ اصلی', 'mainframe-hero' ); ?>">
						<?php foreach ( $links as $index => $item ) : ?>
							<?php
							$url = isset( $item['link']['url'] ) ? $item['link']['url'] : '';
							?>
							<span class="mfh-nav__item">
								<a href="<?php echo $url ? esc_url( $url ) : '#'; ?>"><?php echo esc_html( $item['text'] ); ?></a><?php
								echo ( mfh_bool( $settings['show_separator'] ) && $index < count( $links ) - 1 ) ? '<span class="mfh-nav__sep">, </span>' : '';
								?>
							</span>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>

				<?php if ( mfh_bool( $settings['show_cta'] ) ) : ?>
					<?php
					$cta_url = isset( $settings['cta_link']['url'] ) ? $settings['cta_link']['url'] : '';
					?>
					<a class="mfh-nav__cta" href="<?php echo $cta_url ? esc_url( $cta_url ) : '#'; ?>"><?php echo esc_html( $settings['cta_text'] ); ?></a>
				<?php endif; ?>

				<button
					class="mfh-nav__burger"
					type="button"
					aria-expanded="false"
					aria-controls="<?php echo esc_attr( $uid ); ?>-overlay"
					aria-label="<?php esc_attr_e( 'فهرست', 'mainframe-hero' ); ?>"
				>
					<span class="mfh-nav__bar mfh-nav__bar--top"></span>
					<span class="mfh-nav__bar mfh-nav__bar--mid"></span>
					<span class="mfh-nav__bar mfh-nav__bar--bot"></span>
				</button>
			</header>

			<div class="mfh-nav__overlay" id="<?php echo esc_attr( $uid ); ?>-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'فهرست', 'mainframe-hero' ); ?>">
				<?php foreach ( $links as $item ) : ?>
					<?php $url = isset( $item['link']['url'] ) ? $item['link']['url'] : ''; ?>
					<a href="<?php echo $url ? esc_url( $url ) : '#'; ?>"><?php echo esc_html( $item['text'] ); ?></a>
				<?php endforeach; ?>

				<?php if ( mfh_bool( $settings['show_cta'] ) && mfh_bool( $settings['show_overlay_cta'] ) ) : ?>
					<?php $cta_url = isset( $settings['cta_link']['url'] ) ? $settings['cta_link']['url'] : ''; ?>
					<a class="mfh-nav__overlay-cta" href="<?php echo $cta_url ? esc_url( $cta_url ) : '#'; ?>"><?php echo esc_html( $settings['cta_text'] ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
