<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <?php wp_head(); ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
          new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-W8BZ7N9');</script>
    <!-- End Google Tag Manager -->
</head>

<body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W8BZ7N9"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="app">
  <header class="capabl-header fade-in">
    <div class="main-nav-col">
      <div class="logo-container">
        <a href="/">
          <img class="logo" src="<?= get_stylesheet_directory_uri() . '/images/offset-transport-logo.svg'; ?>" alt="Offset Transport">
        </a>
      </div>
      <nav class="main-navbar">
        <?php
          wp_nav_menu( array(
            'theme_location'  => 'primary',
            'depth'           => 2,
            'container'       => 'div',
            'container_class' => 'main-menu-container',
            'container_id'    => 'mainMenuContainer',
            'menu_class'      => 'primary-menu',
          ));
        ?>
        <button id="mobile-menu-btn" class="hamburger hamburger--spring" type="button">
            <span class="hamburger-box">
              <span class="hamburger-inner"></span>
            </span>
        </button>
      </nav>
      <a href="/instant-quote" class="capabl-btn__round">
        Get An Instant Quote
      </a>
      <div class="header-cart-icon__container">
        <a href="/cart" class="header-cart-icon__container-link">
          <span class="fas fa-shopping-cart header-cart-icon"></span>
          <?php if( WC()->cart->get_cart_contents_count() !== 0 ) : ?>
            <span class="header-cart-count">
                                <?= WC()->cart->get_cart_contents_count(); ?>
                            </span>
          <?php endif; ?>
        </a>
      </div>
    </div>

    <!--   MOBILE MENU -->
    <div id="mobile-menu">
      <?php
        wp_nav_menu( array(
          'theme_location'  => 'mobile',
          'depth'           => 2,
          'container'       => 'div',
          'menu_id'    => 'mobile-nav'
        ));
      ?>
    </div>

  </header>