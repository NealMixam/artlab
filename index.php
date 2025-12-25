<?php get_header(); ?>
<div class="homepage">
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

    <section class="home-section about-section alt">
        <div class="about-inner">
            <div class="about-text">
                <h2 class="section-title">О нас</h2>
                <div class="about-content">
                    <?php echo wpautop(get_option('about_text', '')); ?>
                </div>
            </div>
            <div class="about-gallery">
                <?php
                $images = get_option('about_images', '');
                if (!empty($images)) {
                    $ids = explode(',', $images);
                    foreach ($ids as $id) {
                        $src = wp_get_attachment_image_url($id, 'medium');
                        echo '<div class="about-image"><img src="' . esc_url($src) . '" alt=""></div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

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
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'gallery' ) ) ); ?>" class="btn-primary">
                Смотреть все
            </a>
        </div>
    </section>

    <section class="home-section contact-section">
        <div class="contact-inner">
            <div class="contact-info">
                <h2 class="section-title">Связаться с нами</h2>
                <p>Если у вас есть вопросы по продукции или вы хотите получить консультацию — оставьте сообщение, и мы свяжемся с вами в ближайшее время.</p>
                <ul class="contact-details">
                    <li><strong>Телефон:</strong> <a href="tel:+79255401177">+7(925) 540-11-77</a></li>
                    <li><strong>Email:</strong> <a href="mailto:Artlabtasso@gmail.com">Artlabtasso@gmail.com</a></li>
                    <li><strong>Адрес:</strong> г. Москва, ул. Куусинена, 11, к. 3</li>
                </ul>
            </div>

            <div class="contact-form">
                <form method="post" class="feedback-form">
                    <?php if (isset($_POST['contact_submitted'])): ?>
                        <div class="form-message success">Спасибо! Ваше сообщение отправлено.</div>
                    <?php endif; ?>

                    <input type="hidden" name="contact_submitted" value="1">

                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input type="text" id="name" name="name" required placeholder="Ваше имя">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Ваш email">
                    </div>

                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" placeholder="+7 __ ___ ___">
                    </div>

                    <div class="form-group">
                        <label for="message">Сообщение</label>
                        <textarea id="message" name="message" rows="4" required placeholder="Ваше сообщение..."></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Отправить сообщение</button>
                </form>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>
