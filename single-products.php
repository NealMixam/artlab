<?php get_header(); ?>

<?php if(have_posts()): while(have_posts()): the_post(); ?>

    <div class="single-product" style="max-width:1200px;margin:40px auto;display:grid;grid-template-columns:1fr 1fr;gap:30px;align-items:start;">

        <div class="product-gallery">
            <div class="gallery-grid">
                <?php
                // Получаем все изображения, прикреплённые к продукту
                $images = get_attached_media('image', get_the_ID());
                if ($images && count($images) > 0):
                    $images = array_values($images);
                    foreach($images as $img):
                        $url_large = wp_get_attachment_image_url($img->ID, 'large');
                        $url_medium = wp_get_attachment_image_url($img->ID, 'medium');
                        ?>
                        <a href="<?php echo esc_url($url_large); ?>" class="gallery-item">
                            <img src="<?php echo esc_url($url_medium); ?>" alt="<?php the_title(); ?>" />
                        </a>
                    <?php endforeach; endif; ?>

                <?php
                // Получаем 360° изображения из метаполя
                $images_360_ids = get_post_meta(get_the_ID(), '_product_360_images', true);
                $images_360_urls = [];

                if (!empty($images_360_ids) && is_array($images_360_ids)) {
                    foreach ($images_360_ids as $img_id) {
                        $url = wp_get_attachment_url($img_id);
                        if ($url) $images_360_urls[] = $url;
                    }
                }

                // Если есть хотя бы 2 изображения для 360°
                if (count($images_360_urls) > 1): ?>
                    <a href="#" class="gallery-item" data-lg-custom-html="#product-360-wrapper">
                        <img src="<?php echo esc_url($images_360_urls[0]); ?>" alt="360° <?php the_title(); ?>" />
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div id="product-360-wrapper" style="display:none;">
            <div id="product-360" style="width:600px;height:600px;"></div>
        </div>

        <div class="product-info">
            <h1><?php the_title(); ?></h1>
            <div class="product-content"><?php the_content(); ?></div>
            <div class="product-meta">
                <p><strong>Бренд:</strong> <?php echo get_the_term_list(get_the_ID(),'product_brand','',', '); ?></p>
                <p><strong>Характеристика покрытия:</strong> <?php echo get_the_term_list(get_the_ID(),'product_finish','',', '); ?></p>
                <p><strong>Стиль интерьера:</strong> <?php echo get_the_term_list(get_the_ID(),'product_style','',', '); ?></p>
                <p><strong>Сложность нанесения:</strong> <?php echo get_the_term_list(get_the_ID(),'product_application','',', '); ?></p>
            </div>
            <a href="/products/" class="btn-back">← Вернуться в каталог</a>
        </div>

    </div>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/threesixty.js"></script>

    <!-- Передаем массив 360° изображений в JS -->
    <script>
        const images360 = <?php echo json_encode($images_360_urls); ?>;
        console.log('images360:', images360);

        if (images360.length > 1) {
            const elem = document.getElementById('product-360');
            if (elem) {
                const wrapper = document.getElementById('product-360-wrapper');
                wrapper.style.display = 'block';

                new ThreeSixty(elem, {
                    image: images360,
                    width: 600,
                    height: 600,
                    drag: true
                });
            }
        } else {
            console.log('Недостаточно изображений для 360° просмотра');
        }
    </script>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
