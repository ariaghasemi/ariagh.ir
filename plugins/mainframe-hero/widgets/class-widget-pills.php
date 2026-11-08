<?php
/**
 * Widget: Action Pills.
 *
 * The row of small capsule buttons: four filled, one outlined, the last one
 * copying an address to the clipboard when it is pressed.
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

class Pills extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mfh-pills';
	}

	/**
	 * Panel title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'دکمه‌های کپسولی', 'mainframe-hero' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-button';
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
		return array( 'button', 'pill', 'cta', 'copy', 'دکمه', 'کپسول' );
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
		$this->section_items();
		$this->section_entrance();
		$this->section_style_layout();
		$this->section_style_solid();
		$this->section_style_outline();
	}

	/* --------------------------------------------------------------------- */
	/* Content                                                                */
	/* --------------------------------------------------------------------- */

	/**
	 * Items section.
	 *
	 * @return void
	 */
	private function section_items() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'دکمه‌ها', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'عنوان', 'mainframe-hero' ),
				'description' => __( 'عنوانِ کوتاه نگه داشته شود؛ این دکمه‌ها در یک سطر کنارِ هم می‌نشینند.', 'mainframe-hero' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'دکمه', 'mainframe-hero' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'style',
			array(
				'label'       => __( 'گونه', 'mainframe-hero' ),
				'description' => __( '«توپُر» پس‌زمینهٔ سفید دارد، «خطی» تنها دورخط است.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'solid',
				'options'     => array(
					'solid'   => __( 'توپُر', 'mainframe-hero' ),
					'outline' => __( 'خطی', 'mainframe-hero' ),
				),
			)
		);

		$repeater->add_control(
			'action',
			array(
				'label'       => __( 'کارکرد', 'mainframe-hero' ),
				'description' => __( '«پیوند» بازدیدکننده را به نشانی می‌برد. «رونوشت» مقدارِ زیر را در حافظهٔ رایانه می‌گذارد.', 'mainframe-hero' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'link',
				'options'     => array(
					'link' => __( 'پیوند', 'mainframe-hero' ),
					'copy' => __( 'رونوشت', 'mainframe-hero' ),
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'     => __( 'نشانی', 'mainframe-hero' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'action' => 'link' ),
				'dynamic'   => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'copy_value',
			array(
				'label'       => __( 'مقدارِ رونوشت', 'mainframe-hero' ),
				'description' => __( 'هر متنی می‌تواند باشد: رایانامه، شمارهٔ تلفن یا یک کد. با فشردنِ دکمه در حافظه گذاشته می‌شود.', 'mainframe-hero' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'hello@mainframe.co',
				'condition'   => array( 'action' => 'copy' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'show_copy_icon',
			array(
				'label'        => __( 'نمایشِ نشانِ رونوشت', 'mainframe-hero' ),
				'description'  => __( 'دو مستطیلِ کوچکِ روی‌هم‌افتاده که نشان می‌دهد این دکمه چیزی را کپی می‌کند.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'action' => 'copy' ),
			)
		);

		$repeater->add_control(
			'underline_value',
			array(
				'label'        => __( 'خط‌کشیدن زیرِ مقدار', 'mainframe-hero' ),
				'description'  => __( 'در این طرح زیرِ رایانامه خط می‌رود تا روشن باشد که همان مقدار کپی می‌شود.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'action' => 'copy' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'فهرستِ دکمه‌ها', 'mainframe-hero' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'text'  => 'Pitch us an idea',
						'style' => 'solid',
						'action' => 'link',
					),
					array(
						'text'  => 'Come work here',
						'style' => 'solid',
						'action' => 'link',
					),
					array(
						'text'  => 'Send a brief hello',
						'style' => 'solid',
						'action' => 'link',
					),
					array(
						'text'  => 'See how we operate',
						'style' => 'solid',
						'action' => 'link',
					),
					array(
						'text'            => 'Reach us: hello@mainframe.co',
						'style'           => 'outline',
						'action'          => 'copy',
						'copy_value'      => 'hello@mainframe.co',
						'show_copy_icon'  => 'yes',
						'underline_value' => 'yes',
					),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Entrance section.
	 *
	 * @return void
	 */
	private function section_entrance() {
		$this->start_controls_section(
			'section_entrance',
			array(
				'label' => __( 'ورود', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'animate_in',
			array(
				'label'        => __( 'نمایشِ تدریجی', 'mainframe-hero' ),
				'description'  => __( 'دکمه‌ها با محو شدن و لغزشِ کوتاه از پایین ظاهر می‌شوند. این حرکت روی ساعتِ خودش است و به متنِ تایپ‌شونده وابسته نیست.', 'mainframe-hero' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'بله', 'mainframe-hero' ),
				'label_off'    => __( 'خیر', 'mainframe-hero' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'entrance_delay',
			array(
				'label'       => __( 'تأخیرِ ورود (میلی‌ثانیه)', 'mainframe-hero' ),
				'description' => __( 'چه قدر پس از بارگذاریِ صفحه دکمه‌ها ظاهر شوند. در این طرح ۴۰۰ میلی‌ثانیه است.', 'mainframe-hero' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 10000,
				'step'        => 50,
				'default'     => 400,
				'condition'   => array( 'animate_in' => 'yes' ),
			)
		);

		$this->add_control(
			'entrance_distance',
			array(
				'label'      => __( 'اندازهٔ لغزش (پیکسل)', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 8 ),
				'condition'  => array( 'animate_in' => 'yes' ),
			)
		);

		$this->add_control(
			'entrance_duration',
			array(
				'label'      => __( 'زمانِ ورود (ثانیه)', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0.1, 'max' => 3, 'step' => 0.05 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0.4 ),
				'condition'  => array( 'animate_in' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* --------------------------------------------------------------------- */
	/* Style                                                                  */
	/* --------------------------------------------------------------------- */

	/**
	 * Layout style.
	 *
	 * @return void
	 */
	private function section_style_layout() {
		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => __( 'چیدمان', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'تراز', 'mainframe-hero' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'راست / چپ', 'mainframe-hero' ),
						'icon'  => 'eicon-h-align-right',
					),
					'center'     => array(
						'title' => __( 'میان', 'mainframe-hero' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'چپ / راست', 'mainframe-hero' ),
						'icon'  => 'eicon-h-align-left',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pills' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'row_gap',
			array(
				'label'      => __( 'فاصلهٔ ردیف‌ها', 'mainframe-hero' ),
				'description' => __( 'فاصلهٔ عمودی میانِ ردیف‌ها وقتی دکمه‌ها به سطرِ بعد می‌روند.', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 4 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-pills' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'typography',
				'label'          => __( 'تایپوگرافی', 'mainframe-hero' ),
				'selector'       => '{{WRAPPER}} .mfh-pill',
				'fields_options' => array(
					'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 15 ) ),
				),
			)
		);

		$this->add_control(
			'padding',
			array(
				'label'      => __( 'فاصلهٔ درونی', 'mainframe-hero' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => 0.3,
					'right'    => 20,
					'bottom'   => 0.3,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-pill' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'radius',
			array(
				'label'      => __( 'گردی', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 999 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-pill' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'gap_inline',
			array(
				'label'      => __( 'فاصلهٔ افقیِ هر دکمه', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array( 'em' => array( 'min' => 0, 'max' => 2, 'step' => 0.05 ) ),
				'default'    => array( 'unit' => 'em', 'size' => 0.2 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-pill' => 'margin-inline: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'gap_block',
			array(
				'label'      => __( 'فاصلهٔ عمودیِ هر دکمه', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em' ),
				'range'      => array( 'em' => array( 'min' => 0, 'max' => 2, 'step' => 0.05 ) ),
				'default'    => array( 'unit' => 'em', 'size' => 0.4 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-pill' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_gap',
			array(
				'label'      => __( 'فاصله تا نشان', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-pill' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'transition',
			array(
				'label'      => __( 'زمانِ تغییرِ رنگ (ثانیه)', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 2, 'step' => 0.05 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0.2 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-pill' => 'transition-duration: {{SIZE}}s;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Solid pill style.
	 *
	 * @return void
	 */
	private function section_style_solid() {
		$this->start_controls_section(
			'section_style_solid',
			array(
				'label' => __( 'دکمهٔ توپُر', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'solid_bg',
			array(
				'label'     => __( 'پس‌زمینه', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pill--solid' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'solid_color',
			array(
				'label'     => __( 'رنگِ متن', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pill--solid' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'solid_border',
			array(
				'label'     => __( 'رنگِ دورخط', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.1)',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pill--solid' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'solid_hover_bg',
			array(
				'label'     => __( 'پس‌زمینه در حالتِ اشاره', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pill--solid:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'solid_hover_color',
			array(
				'label'     => __( 'رنگِ متن در حالتِ اشاره', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pill--solid:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Outline pill style.
	 *
	 * @return void
	 */
	private function section_style_outline() {
		$this->start_controls_section(
			'section_style_outline',
			array(
				'label' => __( 'دکمهٔ خطی', 'mainframe-hero' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'outline_border',
			array(
				'label'     => __( 'رنگِ دورخط', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pill--outline' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'outline_color',
			array(
				'label'     => __( 'رنگِ متن', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pill--outline' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'outline_hover_bg',
			array(
				'label'     => __( 'پس‌زمینه در حالتِ اشاره', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pill--outline:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'outline_hover_color',
			array(
				'label'     => __( 'رنگِ متن در حالتِ اشاره', 'mainframe-hero' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .mfh-pill--outline:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => __( 'اندازهٔ نشان (پیکسل)', 'mainframe-hero' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 6, 'max' => 48 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .mfh-pill__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
		$items    = isset( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();

		if ( ! $items ) {
			return;
		}

		$animate  = mfh_bool( $settings['animate_in'] );
		$delay    = isset( $settings['entrance_delay'] ) ? (int) $settings['entrance_delay'] : 400;
		$distance = isset( $settings['entrance_distance']['size'] ) ? (int) $settings['entrance_distance']['size'] : 8;
		$duration = isset( $settings['entrance_duration']['size'] ) ? (float) $settings['entrance_duration']['size'] : 0.4;

		$this->add_render_attribute( 'wrapper', 'class', 'mfh-pills' );

		if ( $animate ) {
			// The hidden state and the transition live in the stylesheet; only
			// the timing travels with the markup.
			$this->add_render_attribute( 'wrapper', 'data-mfh-reveal', wp_json_encode( array(
				'delay'    => $delay,
				'distance' => $distance,
				'duration' => $duration,
			) ) );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$style = isset( $item['style'] ) ? $item['style'] : 'solid';
				$copy  = isset( $item['action'] ) && 'copy' === $item['action'];

				if ( $copy ) {
					$value = isset( $item['copy_value'] ) ? $item['copy_value'] : '';
					$label = isset( $item['text'] ) ? $item['text'] : '';
					$parts = explode( $value, $label, 2 );

					$this->add_render_attribute( 'pill_' . $index, 'class', 'mfh-pill mfh-pill--' . esc_attr( $style ) );
					$this->add_render_attribute( 'pill_' . $index, 'type', 'button' );
					$this->add_render_attribute( 'pill_' . $index, 'data-mfh-copy', esc_attr( $value ) );
					$this->add_render_attribute( 'pill_' . $index, 'aria-label', sprintf(
						/* translators: %s: the value that will be copied */
						__( 'رونوشتِ %s', 'mainframe-hero' ),
						$value
					) );
					?>
					<button <?php $this->print_render_attribute_string( 'pill_' . $index ); ?>>
						<?php if ( isset( $parts[0] ) ) : ?>
							<span><?php echo esc_html( $parts[0] ); ?></span>
						<?php endif; ?>

						<span class="<?php echo mfh_bool( $item['underline_value'] ) ? 'mfh-pill__value mfh-pill__value--underlined' : 'mfh-pill__value'; ?>"><?php echo esc_html( $value ); ?></span>

						<?php if ( isset( $parts[1] ) && '' !== $parts[1] ) : ?>
							<span><?php echo esc_html( $parts[1] ); ?></span>
						<?php endif; ?>

						<?php if ( mfh_bool( $item['show_copy_icon'] ) ) : ?>
							<svg class="mfh-pill__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<rect x="9" y="9" width="12" height="12" rx="2" />
								<rect x="3" y="3" width="12" height="12" rx="2" />
							</svg>
						<?php endif; ?>
					</button>
					<?php
				} else {
					$url = isset( $item['link']['url'] ) ? $item['link']['url'] : '';

					$this->add_render_attribute( 'pill_' . $index, 'class', 'mfh-pill mfh-pill--' . esc_attr( $style ) );
					$this->add_render_attribute( 'pill_' . $index, 'href', $url ? esc_url( $url ) : '#' );

					if ( ! empty( $item['link']['is_external'] ) ) {
						$this->add_render_attribute( 'pill_' . $index, 'target', '_blank' );
						$this->add_render_attribute( 'pill_' . $index, 'rel', 'noopener noreferrer' );
					}

					if ( ! empty( $item['link']['nofollow'] ) ) {
						$this->add_render_attribute( 'pill_' . $index, 'rel', 'nofollow' );
					}
					?>
					<a <?php $this->print_render_attribute_string( 'pill_' . $index ); ?>><?php echo esc_html( $item['text'] ); ?></a>
					<?php
				}
				?>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
