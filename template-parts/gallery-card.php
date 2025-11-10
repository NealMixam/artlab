<div class="gallery-card">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium'); ?>
        <?php endif; ?>
        <h3><?php the_title(); ?></h3>
</div>
