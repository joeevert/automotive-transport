<?php
if ( ! function_exists( 'register_custom_nav_menus' ) ) {

	function register_custom_nav_menus(){
		register_nav_menus(array(
			'primary'           => __('Primary Menu', 'cpl'),
			'footer-env-imp'    => __('Footer Menu Environmental Impact', 'cpl'),
      'footer-iq'         => __('Footer Menu Instant Quote', 'cpl'),
      'footer-about'      => __('Footer Menu About', 'cpl'),
			'mobile'            => __('Mobile Menu', 'cpl')
		));
	}
	add_action( 'after_setup_theme', 'register_custom_nav_menus', 0 );
}