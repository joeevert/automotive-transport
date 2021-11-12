<?php
/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */

namespace Elementor;

class Capabl_Homepage_Slider extends \Elementor\Widget_Base {

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
    return 'capabl-homepage-slider';
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
    return __('Capabl Homepage Slider', 'cpl');
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
        'label' => __( 'Content', 'plugin-name' ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'list_title', [
        'label' => __( 'Title & Slide ID', 'plugin-domain' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => __( 'List Title' , 'plugin-domain' ),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'bg_image',
      [
        'label' => __( 'Choose Image', 'plugin-domain' ),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $repeater->add_control(
      'list_content', [
        'label' => __( 'Content', 'plugin-domain' ),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => __( 'List Content' , 'plugin-domain' ),
        'show_label' => false,
      ]
    );

    $this->add_control(
      'list',
      [
        'label' => __( 'Repeater List', 'plugin-domain' ),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'list_title' => __( 'Title #1', 'plugin-domain' ),
            'list_content' => __( 'Item content. Click the edit button to change this text.', 'plugin-domain' ),
          ]
        ],
        'title_field' => '{{{ list_title }}}',
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
    $html = '';

    if ( $settings['list'] ) {
      $html .= '
        <div id="homepage-swiper" class="swiper-container">
          <div class="swiper-wrapper">';

      foreach (  $settings['list'] as $item ) {
        $html .= '
          <div class="swiper-slide wrapper elementor-repeater-item-' . $item['_id'] . ' ' . $item['list_title'] . '">
            <div class="swiper-slide__bg-img" style="background-image: url(' . $item['bg_image']['url'] . ');">
              '.$item['list_content'].' 
            </div>
          </div>';
      }

      $html .= '
          </div>
        </div>';
    }

    echo $html;

  }

}