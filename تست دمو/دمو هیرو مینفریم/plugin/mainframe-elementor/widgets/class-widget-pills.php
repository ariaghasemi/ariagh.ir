<?php
/**
 * ویجت: دکمه‌های قرصی
 *
 * ردیف دکمه‌های گرد: قرص‌های پر (سفید) و خط‌دار؛
 * دکمهٔ خط‌دار می‌تواند ایمیل را با کلیک کپی کند.
 * ورود با fade پس از تاخیر تنظیم‌شده (پیش‌فرض 400ms دمو).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget_Mainframe_Pills extends \Elementor\Widget_Base {

	public function get_name() {
		return 'mainframe-pills';
	}

	public function get_title() {
		return 'دکمه‌های قرصی';
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return array( 'mainframe' );
	}

	public function get_keywords() {
		return array( 'دکمه', 'قرص', 'pill', 'cta', 'کپی ایمیل' );
	}

	public function get_script_depends() {
		return array( 'mainframe-frontend' );
	}

	public function get_style_depends() {
		return array( 'mainframe-frontend' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_items',
			array(
				'label' => 'دکمه‌ها',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'type',
			array(
				'label'   => 'نوع دکمه',
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'link',
				'options' => array(
					'link' => 'لینک — به آدرس دلخواه',
					'copy' => 'کپی ایمیل — با کلیک، ایمیل کپی می‌شود',
				),
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'       => 'برچسب',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'Pitch us an idea',
				'description' => 'متن داخل دکمه (فقط برای نوع لینک).',
				'condition'   => array( 'type' => 'link' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'     => 'پیوند',
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => array( 'url' => '#' ),
				'condition' => array( 'type' => 'link' ),
			)
		);

		$repeater->add_control(
			'copy_prefix',
			array(
				'label'       => 'متن قبل از ایمیل',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'Reach us:',
				'description' => 'پیشوند دکمهٔ کپی؛ در دمو «Reach us:» است.',
				'condition'   => array( 'type' => 'copy' ),
			)
		);

		$repeater->add_control(
			'email',
			array(
				'label'       => 'ایمیل',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'hello@mainframe.co',
				'description' => 'ایمیلی که هنگام کلیک در کلیپ‌بورد کپی می‌شود. ایمیل به‌صورت زیرخط‌دار نمایش داده می‌شود.',
				'condition'   => array( 'type' => 'copy' ),
			)
		);

		$repeater->add_control(
			'style',
			array(
				'label'   => 'ظاهر',
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => array(
					'solid'   => 'پر (سفید) — مثل چهار دکمهٔ دمو',
					'outline' => 'خط‌دار — مثل دکمهٔ ایمیل دمو',
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => 'آیتم‌ها',
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array(
						'type'    => 'link',
						'label'   => 'Pitch us an idea',
						'link'    => array( 'url' => '#' ),
						'style'   => 'solid',
					),
					array(
						'type'    => 'link',
						'label'   => 'Come work here',
						'link'    => array( 'url' => '#' ),
						'style'   => 'solid',
					),
					array(
						'type'    => 'link',
						'label'   => 'Send a brief hello',
						'link'    => array( 'url' => '#' ),
						'style'   => 'solid',
					),
					array(
						'type'    => 'link',
						'label'   => 'See how we operate',
						'link'    => array( 'url' => '#' ),
						'style'   => 'solid',
					),
					array(
						'type'        => 'copy',
						'copy_prefix' => 'Reach us:',
						'email'       => 'hello@mainframe.co',
						'style'       => 'outline',
					),
				),
				'description' => 'ترتیب آیتم‌ها همان ترتیب نمایش است. دمو: چهار قرص پر + یک قرص خط‌دارِ کپی‌ایمیل.',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_behavior',
			array(
				'label' => 'ورود دکمه‌ها',
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'fade_delay',
			array(
				'label'       => 'تاخیر ورود (میلی‌ثانیه)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 400,
				'min'         => 0,
				'max'         => 5000,
				'step'        => 50,
				'description' => 'چند میلی‌ثانیه پس از لود صفحه دکمه‌ها ظاهر شوند. 400 همان دمو — مستقل از تایپ‌نویسر.',
			)
		);

		$this->add_control(
			'fade_duration',
			array(
				'label'       => 'مدت محو (میلی‌ثانیه)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 400,
				'min'         => 100,
				'max'         => 2000,
				'step'        => 50,
				'description' => 'مدت انیمیشن ورود: شفافیت از 0 به 1 و جابه‌جایی از 8 پیکسل پایین به جای خودش (دمو: 0.4 ثانیه).',
			)
		);

		$this->add_control(
			'copy_feedback',
			array(
				'label'       => 'متن بازخورد کپی',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'کپی شد',
				'description' => 'پس از کپی موفق ایمیل، متن دکمه موقتاً به این عبارت تغییر می‌کند و پس از مدت زیر برمی‌گردد. خالی = بدون بازخورد.',
			)
		);

		$this->add_control(
			'copy_duration',
			array(
				'label'       => 'مدت نمایش بازخورد (میلی‌ثانیه)',
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 1600,
				'min'         => 400,
				'max'         => 5000,
				'step'        => 100,
				'description' => 'چند میلی‌ثانیه متن «کپی شد» نمایش داده شود.',
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
			'direction',
			array(
				'label'       => 'جهت ردیف',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'ltr',
				'options'     => array(
					'ltr' => 'چپ به راست (دمو)',
					'rtl' => 'راست به چپ — برای سایت فارسی',
				),
				'description' => 'ترتیب قرارگیری دکمه‌ها. تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-pills' => 'direction: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'justify',
			array(
				'label'       => 'چیدمان افقی',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'flex-start',
				'options'     => array(
					'flex-start'  => 'چسبیده به شروع (دمو)',
					'center'      => 'وسط‌چین',
					'flex-end'    => 'چسبیده به انتها',
					'space-between' => 'همه‌جا (توزیع مساوی)',
				),
				'description' => 'تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-pills' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'gap',
			array(
				'label'       => 'فاصلهٔ بین دکمه‌ها',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'خالی = دمو؛ مثال: 12px یا 12px 8px (عمودی افقی)',
				'description' => 'خالی بگذارید تا فاصلهٔ دقیق دمو اعمال شود. مقدار CSS: «12px» یا «8px 12px». تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-pills' => 'gap: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'radius',
			array(
				'label'       => 'گِردی گوشه',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'خالی = کاملاً گرد (9999px)؛ مثال: 8px',
				'description' => 'خالی = قرص کاملاً گرد (دمو). مقدار دلخواه مثل 10px برای گوشه‌های کمتر. تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-pill' => 'border-radius: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'padding',
			array(
				'label'       => 'حاشیهٔ داخلی دکمه',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'خالی = دمو؛ مثال: 10px 22px (عمودی افقی)',
				'description' => 'خالی = اندازهٔ دمو (0.3em 16px). مقدار CSS دلخواه وارد کنید. تغییر آنی است.',
				'selectors'   => array(
					'{{WRAPPER}} .mf-pill' => 'padding: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'font_size',
			array(
				'label'       => 'اندازهٔ فونت',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'خودکار: 13px در موبایل، 15px از 640px به بالا',
				'description' => 'خالی بگذارید تا دقیقاً مقیاس دمو اعمال شود (13 تا 15 پیکسل). مقدار دلخواه مثل 16px جایگزین می‌شود.',
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'       => 'آیکون کپی',
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => 'آیکون دو مستطیلِ هم‌پوشان 12×12 کنار دکمهٔ ایمیل (همان آیکون دمو).',
			)
		);

		$this->add_control(
			'solid_bg',
			array(
				'label'     => 'پس‌زمینهٔ قرص پر',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
			)
		);

		$this->add_control(
			'solid_text',
			array(
				'label'     => 'متن قرص پر',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#000000',
			)
		);

		$this->add_control(
			'solid_hover_bg',
			array(
				'label'       => 'هاور قرص پر — پس‌زمینه',
				'type'        => \Elementor\Controls_Manager::COLOR,
				'default'     => '#000000',
				'description' => 'با رفتن ماوس، قرص سفید مشکی می‌شود (دمو).',
			)
		);

		$this->add_control(
			'solid_hover_text',
			array(
				'label'     => 'هاور قرص پر — متن',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
			)
		);

		$this->add_control(
			'outline_border',
			array(
				'label'     => 'حاشیهٔ قرص خط‌دار',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
			)
		);

		$this->add_control(
			'outline_text',
			array(
				'label'     => 'متن قرص خط‌دار',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
			)
		);

		$this->add_control(
			'outline_hover_bg',
			array(
				'label'       => 'هاور قرص خط‌دار — پس‌زمینه',
				'type'        => \Elementor\Controls_Manager::COLOR,
				'default'     => '#FFFFFF',
				'description' => 'هاور، قرص خط‌دار را پر می‌کند (دمو).',
			)
		);

		$this->add_control(
			'outline_hover_text',
			array(
				'label'     => 'هاور قرص خط‌دار — متن',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#000000',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = is_array( $settings['items'] ) ? $settings['items'] : array();
		$email    = get_option( Mainframe_Plugin::OPT );
		$email    = is_array( $email ) && ! empty( $email['default_email'] ) ? $email['default_email'] : 'hello@mainframe.co';

		$vars = '--mf-pill-bg:' . esc_attr( $settings['solid_bg'] ) . ';'
			. '--mf-pill-text:' . esc_attr( $settings['solid_text'] ) . ';'
			. '--mf-pill-hover-bg:' . esc_attr( $settings['solid_hover_bg'] ) . ';'
			. '--mf-pill-hover-text:' . esc_attr( $settings['solid_hover_text'] ) . ';'
			. '--mf-pill-o-border:' . esc_attr( $settings['outline_border'] ) . ';'
			. '--mf-pill-o-text:' . esc_attr( $settings['outline_text'] ) . ';'
			. '--mf-pill-o-hover-bg:' . esc_attr( $settings['outline_hover_bg'] ) . ';'
			. '--mf-pill-o-hover-text:' . esc_attr( $settings['outline_hover_text'] ) . ';'
			. '--mf-pill-fade-delay:' . intval( mf_num( $settings['fade_delay'], 400 ) ) . 'ms;'
			. '--mf-pill-fade-dur:' . intval( mf_num( $settings['fade_duration'], 400 ) ) . 'ms;';

		if ( ! empty( $settings['font_size'] ) ) {
			$vars .= 'font-size:' . esc_attr( $settings['font_size'] ) . ';';
		}

		$this->add_render_attribute(
			'wrap',
			array(
				'class' => 'mf-pills',
				'style' => $vars,
			)
		);

		echo '<div ' . $this->get_render_attribute_string( 'wrap' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput

		$copy_fb   = isset( $settings['copy_feedback'] ) ? $settings['copy_feedback'] : 'کپی شد';
		$copy_dur  = intval( mf_num( isset( $settings['copy_duration'] ) ? $settings['copy_duration'] : null, 1600 ) );

		foreach ( $items as $item ) {
			$type  = isset( $item['type'] ) ? $item['type'] : 'link';
			$style = isset( $item['style'] ) ? $item['style'] : 'solid';

			if ( 'copy' === $type ) {
				$item_email = ! empty( $item['email'] ) ? $item['email'] : $email;
				$prefix     = isset( $item['copy_prefix'] ) ? $item['copy_prefix'] : 'Reach us:';
				$icon       = 'yes' === $settings['show_icon']
					? '<svg class="mf-pill-icon" width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><rect x="4.25" y="4.25" width="6.5" height="6.5" rx="1.2"/><rect x="1.25" y="1.25" width="6.5" height="6.5" rx="1.2"/></svg>'
					: '';
				echo '<button type="button" class="mf-pill mf-pill--outline mf-pill--copy" data-email="' . esc_attr( $item_email ) . '" data-copied="' . esc_attr( $copy_fb ) . '" data-copied-dur="' . esc_attr( $copy_dur ) . '" aria-label="' . esc_attr( 'کپی ' . $item_email ) . '">';
				echo '<span class="mf-pill-label">' . esc_html( $prefix ) . ' <span class="mf-pill-u">' . esc_html( $item_email ) . '</span></span>';
				echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG ثابت و داخلی
				echo '</button>';
			} else {
				$label = isset( $item['label'] ) ? $item['label'] : '';
				$href  = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '#';
				$cls   = 'solid' === $style ? 'mf-pill mf-pill--solid' : 'mf-pill mf-pill--outline';
				echo '<a class="' . esc_attr( $cls ) . '" href="' . esc_url( $href ) . '"' . mf_link_target( $item['link'] ) . '>' . esc_html( $label ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}

		echo '</div>';
	}
}
