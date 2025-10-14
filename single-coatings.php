<?php get_header(); ?>

<?php if(have_posts()): while(have_posts()): the_post(); ?>

<div class="single-coating">

    <h1><?php the_title(); ?></h1>

    <!-- Галерея / изображение -->
    <div class="coating-gallery">
        <?php if(has_post_thumbnail()) the_post_thumbnail('large'); ?>
        <?php if( have_rows('gallery') ): ?>
            <div class="coating-gallery-thumbs">
                <?php while( have_rows('gallery') ): the_row(); 
                    $img = get_sub_field('image'); ?>
                    <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>">
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Содержание -->
    <div class="coating-content">
        <?php the_content(); ?>
    </div>

    <!-- Таксономии / характеристики -->
    <div class="coating-meta">
        <p>Тип покрытия: <?php echo get_the_term_list(get_the_ID(),'coating_type','',', '); ?></p>
        <p>Бренд: <?php echo get_the_term_list(get_the_ID(),'coating_brand','',', '); ?></p>
        <?php if($price = get_post_meta(get_the_ID(),'price',true)): ?>
            <p>Цена: <?php echo esc_html($price); ?> ₽</p>
        <?php endif; ?>
    </div>

    <!-- Кнопка назад -->
    <a href="/coatings/" class="btn-back">Вернуться в каталог покрытий</a>

</div>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
