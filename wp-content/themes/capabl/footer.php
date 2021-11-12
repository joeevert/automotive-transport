<footer class="capabl-footer">
  <div class="ot-footer">
    <div class="ot-footer__inner">
      <div class="container-fluid px-0">
        <div class="row mx-auto nav-row">
          <div class="col-md-2">
            <!-- not used -->
          </div>
          <div class="col-md">
            <div class="mb-4">
              <span class="ot-footer__heading">Quality automotive transport for people who care.</span>
            </div>
            <div class="footer-btn-wrapper mb-4">
              <a href="/instant-quote" class="capabl-btn__round">
                Get An Instant Quote
              </a>
            </div>
            <div class="footer-btn-wrapper mb-4">
              <a href="/contact-us" class="capabl-btn__round capabl-btn__round-outline">
                Contact Us
              </a>
            </div>

          </div>
          <div class="col">
            <div class="mb-4">
              <div class="ot-footer__heading">
                <a href="/environmental-impact">Environmental Impact</a>
              </div>
<!--              <nav class="footer-navbar">-->
<!--                --><?php
//                wp_nav_menu( array(
//                  'theme_location'  => 'footer-env-imp',
//                  'depth'           => 2,
//                  'container'       => 'div',
//                  'container_class' => 'footer-menu-container',
//                  'menu_class'      => 'footer-menu',
//                ));
//                ?>
<!--              </nav>-->
            </div>
            <div class="mb-4">
              <div class="ot-footer__heading">
                <a href="/instant-quote">Instant Quote</a>
              </div>
<!--              <nav class="footer-navbar">-->
<!--                --><?php
//                wp_nav_menu( array(
//                  'theme_location'  => 'footer-iq',
//                  'depth'           => 2,
//                  'container'       => 'div',
//                  'container_class' => 'footer-menu-container',
//                  'menu_class'      => 'footer-menu',
//                ));
//                ?>
<!--              </nav>-->
            </div>
          </div>
          <div class="col">
            <div class="mb-4">
              <div class="ot-footer__heading">About</div>
              <nav class="footer-navbar">
                <?php
                wp_nav_menu( array(
                  'theme_location'  => 'footer-about',
                  'depth'           => 2,
                  'container'       => 'div',
                  'container_class' => 'footer-menu-container',
                  'menu_class'      => 'footer-menu',
                ));
                ?>
              </nav>
            </div>
          </div>
          <div class="col-md-2">
            <!-- not used -->
          </div>
        </div>

        <div class="row mx-auto icon-row">
          <div class="col-md-2 col-6 px-0 logo-container">
            <a href="/">
              <img class="logo img-fluid" src="<?= get_stylesheet_directory_uri() . '/images/offset-transport-logo.svg'; ?>" alt="Offset Transport">
            </a>
          </div>
          <div class="col-md col-6 social-media-container">
            <a href="https://www.facebook.com/offsettransport/" target="_blank" class="social-media-link">
              <i class="fab fa-facebook-f social-media-icon"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row mx-auto copyright-row">
      <div class="col-md-6 px-0">
        <div class="legal">
          <span>Offset Transport © <?php echo date("Y"); ?>. All rights reserved.</span>
        </div>
      </div>
      <div class="col-md-6 px-0 ogs-col">
        <div class="legal">
          <span>This website proudly designed by:</span>
          <a href="https://onlinegrowthsystems.com/" target="_blank">
            <img class="ogs-logo img-fluid" src="<?= get_stylesheet_directory_uri() . '/images/ogslogo.png'; ?>" alt="Online Growth Systems">
          </a>
        </div>
      </div>
    </div>
  </div>
</footer>

</div> <!-- End #app -->

<?php wp_footer(); ?>

</body>
</html>
