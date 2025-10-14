<?php get_header(); ?>

<?php get_sidebar(); ?>

<div class="homepage">

    <!-- Блок товаров -->
    <section class="home-section">
        <h2 class="section-title">Популярные товары</h2>
        <div class="home-grid">
            <?php
            $products = new WP_Query([
                'post_type' => 'products',
                'posts_per_page' => 6
            ]);

            if ($products->have_posts()) :
                while ($products->have_posts()) : $products->the_post();
                    get_template_part('template-parts/product', 'card');
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>Товары не найдены.</p>';
            endif;
            ?>
        </div>
        <div class="home-btn-wrap">
            <a href="<?php echo get_post_type_archive_link('products'); ?>" class="btn-primary">Все товары</a>
        </div>
    </section>


    <!-- Блок покрытий -->
    <section class="home-section alt">
        <h2 class="section-title">Покрытия</h2>
        <div class="home-grid">
            <?php
            $coatings = new WP_Query([
                'post_type' => 'coatings',
                'posts_per_page' => 6
            ]);

            if ($coatings->have_posts()) :
                while ($coatings->have_posts()) : $coatings->the_post();
                    get_template_part('template-parts/coating', 'card');
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>Покрытия не найдены.</p>';
            endif;
            ?>
        </div>
        <div class="home-btn-wrap">
            <a href="<?php echo get_post_type_archive_link('coatings'); ?>" class="btn-primary">Все покрытия</a>
        </div>
    </section>


    <!-- Блок галереи -->
    <section class="home-section">
        <h2 class="section-title">Галерея проектов</h2>
        <div class="home-grid gallery-grid">
            <?php
            $gallery = new WP_Query([
                'post_type' => 'gallery_item',
                'posts_per_page' => 6
            ]);

            if ($gallery->have_posts()) :
                while ($gallery->have_posts()) : $gallery->the_post();
                    get_template_part('template-parts/gallery', 'card');
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>Галерея пуста.</p>';
            endif;
            ?>
        </div>
        <div class="home-btn-wrap">
            <a href="<?php echo get_post_type_archive_link('gallery'); ?>" class="btn-primary">Смотреть все</a>
        </div>
    </section>

</div>


<?php get_footer(); ?>
