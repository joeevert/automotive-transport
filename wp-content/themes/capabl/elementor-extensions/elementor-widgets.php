<?php

/*
 * Register Elementor Custom Widgets
 */

class ETT_Elementor_Widgets {

  protected static $instance = null;

  public static function get_instance() {
    if ( ! isset( static::$instance ) ) {
      static::$instance = new static;
    }

    return static::$instance;
  }

  protected function __construct() {

    // Add all the widgets here
    require_once ('capabl-color-card.php');
    require_once ('capabl-homepage-slider.php');

    add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
  }

  public function register_widgets() {
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Capabl_Color_Card() );
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Capabl_Homepage_Slider() );
  }
}

add_action( 'init', 'register_elementor_widgets_init' );
function register_elementor_widgets_init() {
  ETT_Elementor_Widgets::get_instance();
}

//Add custom Elementor categories
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );
function add_elementor_widget_categories( $elements_manager ) {

  $elements_manager->add_category(
    'capabl',
    [
      'title' => __( 'Capabl', 'cpl' ),
    ]
  );
}
