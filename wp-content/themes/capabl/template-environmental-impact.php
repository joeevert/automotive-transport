<?php get_header();
/*
Template Name: Environmental Impact
Template Post Type: page
*/

$post = get_post(get_the_ID());

?>

    <main>

        <?php if ( have_posts() ) :

            while ( have_posts() ) :

                the_post();

                the_content();

            endwhile;

            the_posts_navigation();

        else :

            get_template_part( 'templates/content', 'none' );

        endif;
        ?>
      <div id="environmentalImpactScene">

        <div id="otps-01" class="ot-parallax-section">
          <h2 class="text-white">testing this movement in section 01</h2>
        </div>

        <div id="otps-02" class="ot-parallax-section">
          <h2 class="text-white">testing this movement in section 02</h2>
        </div>

        <div id="otps-03" class="ot-parallax-section">
          <h2 class="text-white">testing this movement in section 03</h2>
        </div>

      </div>

    </main>

<?php get_footer(); ?>