<?php
/* Template Name: About Us */
get_header();
?>
<?php get_sidebar(); ?>

<section class="about-page">
    <div class="container">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post(); ?>
                <h1 class="section-title"><?php the_title(); ?></h1>
                <div class="">
                    <?php the_content(); ?>
                </div>
            <?php endwhile;
        endif;
        ?>
    </div>
</section>

<?php get_footer(); ?>
