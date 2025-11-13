<?php get_header(); ?>
<?php get_sidebar(); ?>

<?php if (have_posts()): while (have_posts()): the_post(); ?>

    <?php
    // === GALLERY ===
    $gallery_ids = get_post_meta(get_the_ID(), '_product_gallery_images', true);
    $gallery_urls = [];

    if (!empty($gallery_ids) && is_array($gallery_ids)) {
        foreach ($gallery_ids as $img_id) {
            $url = wp_get_attachment_url($img_id);
            if ($url) $gallery_urls[] = $url;
        }
    }

    // === 360° ===
    $images_360_ids = get_post_meta(get_the_ID(), '_product_360_images', true);
    $images_360_urls = [];

    if (!empty($images_360_ids) && is_array($images_360_ids)) {
        foreach ($images_360_ids as $img_id) {
            $url = wp_get_attachment_url($img_id);
            if ($url) $images_360_urls[] = $url;
        }
    }
    ?>

    <div class="single-product">

        <div id="modal-360" class="modal-360">
            <div class="modal-360-content">
                <button class="modal-360-close">&times;</button>
                <div id="modal-360-viewer">
                    <?php if (count($images_360_urls) > 1): ?>
                        <div id="product-360-wrapper" class="product-360-trigger" style="margin-bottom:20px;">
                            <div id="product-360"></div>
                            <button id="play-pause-360" class="btn-play-pause">▶️ Play</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="product-container">
            <div class="product-gallery">
                <button id="open-360" class="btn-360">360°</button>
                <?php if (!empty($gallery_urls)): ?>
                    <div class="swiper main-slider">
                        <div class="swiper-wrapper">
                            <?php foreach ($gallery_urls as $img_url): ?>
                                <div class="swiper-slide">
                                    <a href="<?php echo esc_url($img_url); ?>" class="gallery-item">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="swiper thumbs-slider">
                        <div class="swiper-wrapper">
                            <?php foreach ($gallery_urls as $img_url): ?>
                                <div class="swiper-slide">
                                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>


        <div class="product-info">
            <a href="/products/" class="btn-back">← Вернуться в каталог</a>
            <h1><?php the_title(); ?></h1>
            <div class="product-content"><?php the_content(); ?></div>
            <div class="product-meta">
                <p><strong>Бренд:</strong> <?php echo get_the_term_list(get_the_ID(), 'product_brand', '', ', '); ?></p>
                <p><strong>Характеристика покрытия:</strong> <?php echo get_the_term_list(get_the_ID(), 'product_finish', '', ', '); ?></p>
                <p><strong>Стиль интерьера:</strong> <?php echo get_the_term_list(get_the_ID(), 'product_style', '', ', '); ?></p>
                <p><strong>Сложность нанесения:</strong> <?php echo get_the_term_list(get_the_ID(), 'product_application', '', ', '); ?></p>
            </div>

            <?php
            $price = get_post_meta(get_the_ID(), '_product_price', true);
            if ($price) :
                ?>
                <p class="product-price"><strong>Цена:</strong> <?php echo esc_html(number_format($price, 2, '.', ' ')); ?>  ₽</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        window.product360Images = <?php echo json_encode($images_360_urls); ?>;
    </script>

<?php endwhile; endif; ?>

<?php get_footer(); ?>