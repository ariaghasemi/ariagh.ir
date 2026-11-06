<?php
/**
 * ویجت: نوار بالا (ناوبری)
 *
 * هدر شناور دمو: لوگو + نشانه، لینک‌های وسطی با جداکنندهٔ کاما،
 * CTA زیرخط‌دار، همبرگر موبایل و اورلی تیره با انیمیشن باز/بسته.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget_Mainframe_Navbar extends \Elementor\Widget_Base {

	public function get_name() {
		return 'mainframe-navbar';
	}

	public function get_title() {
		return 'نوار بالا (ناوبری)';
	}

	public function get_icon() {
		return 'eicon-menu';
	}

	public function get_categories() {
		return array( 'mainframe' );
	}

	public function get_keywords() {
		return array( 'هدر', 'ناوبری', 'منو', 'navbar', 'همبرگر', 'اورلی' );
	}

	public function get_script_depends() {
		return array( 'mainframe-frontend' );
	}

	public function get_style_depends() {
		return array( 'mainframe-frontend' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_brand',
			array(
				'label' => 'لوگو',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo_text',
			array(
				'label'       => 'متن لوگو',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'Mainframe®',
				'description' => 'با فونت تیتر (--mf-heading) نمایش داده می‌شود؛ تنظیم آن در «تنظیمات ← فونت‌ها».',
			)
		);

		$this->add_control(
			'logo_link',
			array(
				'label'   => 'پیوند لوگو',
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_responsive_control(
			'logo_size',
			array(
				'label'       => 'اندازهٔ لوگو (پیکسل)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 26,
				'min'         => 12,
				'max'         => 60,
				'description' => 'دسکتاپ 26 و موبایل 21 در قالب آماده تنظیم شده است (همان دمو). با کنترل واکنش‌گرا برای تبلت/موبایل می‌توانید جداگانه بگذارید.',
			)
		);

		$this->add_control(
			'show_mark',
			array(
				'label'       => 'نمایش نشانه',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => 'کاراکتر تزئینی کنار لوگو (در دمو ✳︎).',
			)
		);

		$this->add_control(
			'mark_text',
			array(
				'label'       => 'نشانه',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '✳︎',
				'description' => 'هر کاراکتر یا اموجی‌ای که بخواهید؛ پیش‌فرض نشانهٔ دمو.',
				'condition'   => array( 'show_mark' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'mark_size',
			array(
				'label'     => 'اندازهٔ نشانه (پیکسل)',
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 30,
				'min'       => 12,
				'max'       => 70,
				'description' => 'دسکتاپ 30 و موبایل 25 در قالب آماده.',
				'condition' => array( 'show_mark' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_links',
			array(
				'label' => 'لینک‌ها و دعوت‌نامه',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'label',
			array(
				'label'   => 'برچسب',
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Labs',
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'   => 'پیوند',
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'links',
			array(
				'label'       => 'لینک‌های منو',
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array( 'label' => 'Labs', 'link' => array( 'url' => '#' ) ),
					array( 'label' => 'Studio', 'link' => array( 'url' => '#' ) ),
					array( 'label' => 'Openings', 'link' => array( 'url' => '#' ) ),
					array( 'label' => 'Shop', 'link' => array( 'url' => '#' ) ),
				),
				'description' => 'در دسکتاپ وسط چیده می‌شوند و در موبایل داخل اورلی لیست می‌شوند.',
			)
		);

		$this->add_control(
			'separator',
			array(
				'label'       => 'جداکنندهٔ لینک‌ها',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => ', ',
				'description' => 'بین لینک‌های دسکتاپ قرار می‌گیرد؛ در دمو «کاما + فاصله» است. خالی = بدون جداکننده.',
			)
		);

		$this->add_control(
			'cta_label',
			array(
				'label'       => 'دعوت‌نامه (CTA)',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'Get in touch',
				'description' => 'متن زیرخط‌دار سمت دیگر نوار؛ در موبایل به انتهای اورلی اضافه می‌شود.',
			)
		);

		$this->add_control(
			'cta_link',
			array(
				'label'   => 'پیوند CTA',
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'links_size',
			array(
				'label'       => 'اندازهٔ لینک‌ها و CTA (پیکسل)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 23,
				'min'         => 12,
				'max'         => 48,
				'description' => '23 همان اندازهٔ دمو.',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => 'چیدمان',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'position',
			array(
				'label'       => 'موقعیت',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'fixed',
				'options'     => array(
					'fixed'   => 'ثابت — شناور روی ویدیو (دمو)',
					'absolute'=> 'مطلق — نسبت به نزدیک‌ترین والد',
					'static'  => 'عادی — در جریان محتوا',
				),
				'description' => 'در حالت ثابت، نوار از جریان محتوا خارج می‌شود و همیشه بالای صفحه می‌ماند.',
			)
		);

		$this->add_control(
			'z_index',
			array(
				'label'       => 'لایه (z-index)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 10,
				'min'         => 0,
				'max'         => 999,
				'description' => 'دمو: نوار 10، اورلی موبایل 9، محتوای هیرو 1، ویدیو 0 — نوار همیشه بالاترین است.',
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'       => 'حاشیهٔ داخلی',
				'type'        => \Elementor\Controls_Manager::DIMENSIONS,
				'default'     => array(
					'top'    => 20,
					'right'  => 32,
					'bottom' => 20,
					'left'   => 32,
					'unit'   => 'px',
					'isLinked' => true,
				),
				'description' => 'پیش‌فرض دمو: دسکتاپ 20px 32px و موبایل 16px 20px — مقادیر موبایل/تبلت در قالب آماده از قبل ست شده‌اند.',
			)
		);

		$this->add_control(
			'hover_opacity',
			array(
				'label'       => 'شفافیت هاور لینک‌ها (درصد)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 60,
				'min'         => 10,
				'max'         => 100,
				'description' => 'هنگام هاور روی لینک‌ها و CTA اعمال می‌شود؛ دمو: 60٪.',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_mobile',
			array(
				'label' => 'منوی موبایل (اورلی)',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'breakpoint',
			array(
				'label'       => 'نقطهٔ شکست همبرگر (پیکسل)',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '768',
				'options'     => array(
					'768'  => '768 — مثل دمو (زیر تبلت)',
					'1024' => '1024 — فقط موبایل‌های باریک',
				),
				'description' => 'زیر این عرض فقط همبرگر دیده می‌شود؛ بالای آن لینک‌ها و CTA. دمو دقیقاً 768 (breakpoint md در تیلویند) است.',
			)
		);

		$this->add_control(
			'overlay_bg',
			array(
				'label'       => 'پس‌زمینهٔ اورلی',
				'type'        => \Elementor\Controls_Manager::COLOR,
				'default'     => 'rgba(0,0,0,0.9)',
				'description' => 'پس‌زمینهٔ منوی تمام‌صفحهٔ موبایل؛ دمو 90٪ مشکی.',
			)
		);

		$this->add_control(
			'overlay_blur',
			array(
				'label'       => 'تاری پس‌زمینه (پیکسل)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 12,
				'min'         => 0,
				'max'         => 40,
				'description' => 'backdrop-blur اورلی؛ دمو 12 (معادل backdrop-blur-md در تیلویند).',
			)
		);

		$this->add_control(
			'overlay_link_size',
			array(
				'label'       => 'اندازهٔ لینک‌های اورلی (پیکسل)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 32,
				'min'         => 16,
				'max'         => 60,
				'description' => '32 همان اندازهٔ دمو.',
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => 'رنگ متن اورلی',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_colors',
			array(
				'label' => 'رنگ‌ها',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'color',
			array(
				'label'       => 'رنگ متن نوار',
				'type'        => \Elementor\Controls_Manager::COLOR,
				'default'     => '#FFFFFF',
				'description' => 'لوگو، لینک‌ها و CTA در حالت دسکتاپ.',
			)
		);

		$this->add_control(
			'burger_color',
			array(
				'label'     => 'رنگ همبرگر',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'description' => 'سه خط همبرگر در موبایل.',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$links    = is_array( $settings['links'] ) ? $settings['links'] : array();

		$position  = $settings['position'] ? $settings['position'] : 'fixed';
		$z         = intval( mf_num( $settings['z_index'], 10 ) );
		$breakpoint = $settings['breakpoint'] === '1024' ? '1024' : '768';

		$pad   = mf_dim( $settings['padding'] );
		$pad_t = ! empty( $settings['padding_tablet'] ) ? mf_dim( $settings['padding_tablet'] ) : $pad;
		$pad_m = ! empty( $settings['padding_mobile'] ) ? mf_dim( $settings['padding_mobile'] ) : $pad;

		$logo_size   = intval( mf_num( $settings['logo_size'], 26 ) );
		$logo_t      = intval( mf_num( ! empty( $settings['logo_size_tablet'] ) ? $settings['logo_size_tablet'] : null, $logo_size ) );
		$logo_m      = intval( mf_num( ! empty( $settings['logo_size_mobile'] ) ? $settings['logo_size_mobile'] : null, $logo_size ) );

		$mark_size   = intval( mf_num( $settings['mark_size'], 30 ) );
		$mark_t      = intval( mf_num( ! empty( $settings['mark_size_tablet'] ) ? $settings['mark_size_tablet'] : null, $mark_size ) );
		$mark_m      = intval( mf_num( ! empty( $settings['mark_size_mobile'] ) ? $settings['mark_size_mobile'] : null, $mark_size ) );

		$hover = intval( mf_num( $settings['hover_opacity'], 60 ) );

		$vars = 'position:' . esc_attr( $position ) . ';top:0;left:0;right:0;z-index:' . $z . ';'
			. 'color:' . esc_attr( $settings['color'] ) . ';'
			. '--mf-burger-color:' . esc_attr( $settings['burger_color'] ) . ';'
			. '--mf-nav-pad:' . $pad . ';'
			. '--mf-nav-pad-t:' . $pad_t . ';'
			. '--mf-nav-pad-m:' . $pad_m . ';'
			. '--mf-logo:' . $logo_size . 'px;' . '--mf-logo-t:' . $logo_t . 'px;' . '--mf-logo-m:' . $logo_m . 'px;'
			. '--mf-mark:' . $mark_size . 'px;' . '--mf-mark-t:' . $mark_t . 'px;' . '--mf-mark-m:' . $mark_m . 'px;'
			. '--mf-links:' . intval( mf_num( $settings['links_size'], 23 ) ) . 'px;'
			. '--mf-hover-op:' . ( $hover / 100 ) . ';'
			. '--mf-overlay-bg:' . esc_attr( $settings['overlay_bg'] ) . ';'
			. '--mf-overlay-blur:' . intval( mf_num( $settings['overlay_blur'], 12 ) ) . 'px;'
			. '--mf-overlay-size:' . intval( mf_num( $settings['overlay_link_size'], 32 ) ) . 'px;'
			. '--mf-overlay-color:' . esc_attr( $settings['overlay_color'] ) . ';';

		$this->add_render_attribute(
			'header',
			array(
				'class' => array( 'mf-navbar', 'mf-bp-' . $breakpoint ),
				'style' => $vars,
			)
		);

		$logo_href = ! empty( $settings['logo_link']['url'] ) ? $settings['logo_link']['url'] : '#';
		$cta_href  = ! empty( $settings['cta_link']['url'] ) ? $settings['cta_link']['url'] : '#';
		$separator = $settings['separator'];

		echo '<header ' . $this->get_render_attribute_string( 'header' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput

		// ─── نوار دسکتاپ ───
		echo '<div class="mf-navbar-bar">';
		echo '<a class="mf-logo" href="' . esc_url( $logo_href ) . '">';
		echo '<span class="mf-logo-text">' . esc_html( $settings['logo_text'] ) . '</span>';
		if ( 'yes' === $settings['show_mark'] ) {
			echo '<span class="mf-mark" aria-hidden="true">' . esc_html( $settings['mark_text'] ) . '</span>';
		}
		echo '</a>';

		echo '<nav class="mf-nav-links">';
		foreach ( $links as $i => $link ) {
			if ( $i > 0 && '' !== $separator ) {
				echo '<span class="mf-sep">' . esc_html( $separator ) . '</span>';
			}
			$href = ! empty( $link['link']['url'] ) ? $link['link']['url'] : '#';
			echo '<a href="' . esc_url( $href ) . '">' . esc_html( $link['label'] ) . '</a>';
		}
		echo '</nav>';

		echo '<a class="mf-nav-cta" href="' . esc_url( $cta_href ) . '">' . esc_html( $settings['cta_label'] ) . '</a>';

		// همبرگر
		echo '<button type="button" class="mf-burger" aria-label="باز کردن منو" aria-expanded="false">';
		echo '<span></span><span></span><span></span>';
		echo '</button>';
		echo '</div>';

		// ─── اورلی موبایل ───
		echo '<div class="mf-nav-overlay">';
		foreach ( $links as $link ) {
			$href = ! empty( $link['link']['url'] ) ? $link['link']['url'] : '#';
			echo '<a href="' . esc_url( $href ) . '">' . esc_html( $link['label'] ) . '</a>';
		}
		echo '<a class="mf-overlay-cta" href="' . esc_url( $cta_href ) . '">' . esc_html( $settings['cta_label'] ) . '</a>';
		echo '</div>';

		echo '</header>';
	}
}
