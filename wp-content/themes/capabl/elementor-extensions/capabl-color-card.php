<?php
/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */

namespace Elementor;

class Capabl_Color_Card extends \Elementor\Widget_Base {

  /**
   * Get widget name.
   *
   * Retrieve oEmbed widget name.
   *
   * @return string Widget name.
   * @since 1.0.0
   * @access public
   *
   */
  public function get_name() {
    return 'capabl-color-card';
  }

  /**
   * Get widget title.
   *
   * Retrieve oEmbed widget title.
   *
   * @return string Widget title.
   * @since 1.0.0
   * @access public
   *
   */
  public function get_title() {
    return __('Capabl Color Card', 'cpl');
  }

  /**
   * Get widget icon.
   *
   * Retrieve oEmbed widget icon.
   *
   * @return string Widget icon.
   * @since 1.0.0
   * @access public
   *
   */
  public function get_icon() {
    return 'fas fa-images';
  }

  /**
   * Get widget categories.
   *
   * Retrieve the list of categories the oEmbed widget belongs to.
   *
   * @return array Widget categories.
   * @since 1.0.0
   * @access public
   *
   */
  public function get_categories() {
    return ['capabl'];
  }

  /**
   * Register oEmbed widget controls.
   *
   * Adds different input fields to allow the user to change and customize the widget settings.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function _register_controls() {

    $this->start_controls_section(
      'content_section',
      [
        'label' => __('Content', 'cpl'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'card_link',
      [
        'label' => __( 'Link', 'plugin-name' ),
        'type' => \Elementor\Controls_Manager::URL,
        'placeholder' => __( 'Enter the card link', 'plugin-name' ),

      ]
    );

    $this->add_control(
      'card_text',
      [
        'label' => __( 'Card Text', 'plugin-domain' ),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => __( 'Default description', 'plugin-domain' ),
        'placeholder' => __( 'Type your copy here', 'plugin-domain' ),
      ]
    );

    $this->add_control(
      'bg_color',
      [
        'label' => __( 'Background Color', 'plugin-domain' ),
        'type' => \Elementor\Controls_Manager::COLOR,
        'scheme' => [
          'type' => \Elementor\Scheme_Color::get_type(),
          'value' => \Elementor\Scheme_Color::COLOR_1,
        ],
        'selectors' => [
          '{{WRAPPER}} .title' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_section();

  }

  /**
   * Render oEmbed widget output on the frontend.
   *
   * Written in PHP and used to generate the final HTML.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function render() {

    $settings = $this->get_settings_for_display();

    $card_link = $settings['card_link']['url'];
    $card_text = $settings['card_text'];
    $bg_color = $settings['bg_color'];

    $html = '
			<a class="capabl-color-card__link" href="' . esc_url($card_link) .'" >
				<div class="capabl-color-card__container" style="background-color: ' . $bg_color . ';">
          <div class="capabl-color-card__text">
            ' . $card_text . '
          </div>
				</div>
			</a>';

    echo $html;

  }

}