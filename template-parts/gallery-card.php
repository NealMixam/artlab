<?php
$gallery_page = get_permalink( get_page_by_path( 'gallery' ) );
$anchor = 'gallery-' . get_post_field( 'post_name', get_the_ID() );
?>

<a href="<?php echo esc_url( $gallery_page . '#' . $anchor ); ?>" class="gallery-card">
    <?php if (has_post_thumbnail()) : ?>
        <?php the_post_thumbnail('medium'); ?>
    <?php endif; ?>

    <h3><?php the_title(); ?></h3>
</a>
