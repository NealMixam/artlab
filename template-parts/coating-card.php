<div class="coating-card">
    <div class="coating-image">
        <?php if(has_post_thumbnail()) {
            the_post_thumbnail('medium');
        } ?>
    </div>

    <h2 class="coating-title"><?php the_title(); ?></h2>

    <div class="coating-meta">
        <?php 
        $types = get_the_term_list(get_the_ID(), 'coating_type', '', ', ');
        if ( ! is_wp_error($types) && $types ) {
            echo '<p>Тип покрытия: ' . $types . '</p>';
        }

        $brands = get_the_term_list(get_the_ID(), 'coating_brand', '', ', ');
        if ( ! is_wp_error($brands) && $brands ) {
            echo '<p>Бренд: ' . $brands . '</p>';
        }
        ?>
    </div>

    <?php if($price = get_post_meta(get_the_ID(),'price',true)): ?>
        <p class="coating-price">Цена: <?php echo esc_html($price); ?> ₽</p>
    <?php endif; ?>

    <a href="/coatings/" class="btn-details">Подробнее</a>
</div>
