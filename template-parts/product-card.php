<div class="product-card">
    <div class="product-image">
        <?php if(has_post_thumbnail()) the_post_thumbnail('medium'); ?>
    </div>
    <h2 class="product-title"><?php the_title(); ?></h2>
    <?php if($price = get_post_meta(get_the_ID(),'price',true)): ?>
        <p class="product-price">Цена: <?php echo esc_html($price); ?> ₽</p>
    <?php endif; ?>
    <a href="<?php the_permalink(); ?>" class="btn-details">Подробнее</a>
</div>
