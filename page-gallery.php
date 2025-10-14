<?php
/* Template Name: Gallery Page */
get_header();
?>

<?php get_sidebar(); ?>

<main class="wp-block-group alignwide gallery-page">
    <h1 class="gallery-heading">Галерея продукции</h1>

    <?php
    $gallery_items = new WP_Query(array(
        'post_type' => 'gallery_item',
        'posts_per_page' => -1
    ));

    if ($gallery_items->have_posts()) :
        echo '<div class="gallery-grid">';
        while ($gallery_items->have_posts()) : $gallery_items->the_post(); ?>
            <article class="gallery-card">
                <div class="gallery-thumb">
                    <?php if (has_post_thumbnail()) {
                        the_post_thumbnail('medium_large');
                    } ?>
                </div>
                <h3 class="gallery-title"><?php the_title(); ?></h3>
                <div class="gallery-desc"><?php the_excerpt(); ?></div>
            </article>
        <?php
        endwhile;
        echo '</div>';
        wp_reset_postdata();
    else :
        echo '<p>Пока нет элементов галереи.</p>';
    endif;
    ?>
</main>

<?php get_footer(); ?>
