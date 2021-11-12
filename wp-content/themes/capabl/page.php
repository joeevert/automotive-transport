<?php get_header();

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

    </main>

<?php get_footer(); ?>