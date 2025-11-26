<?php
/* Template Name: Gallery Page */
get_header();
?>
<?php get_sidebar(); ?>

<main class="gallery-page container">
    <h1 class="gallery-heading">Галерея продукции</h1>

    <?php
    $gallery_items = new WP_Query([
            'post_type' => 'gallery_item',
            'posts_per_page' => -1
    ]);

    if ($gallery_items->have_posts()) :
        $index = 0;
        while ($gallery_items->have_posts()) : $gallery_items->the_post();
            $index++;
            $reverse = $index % 2 === 0 ? ' reverse' : '';
            ?>

            <section class="gallery-block<?php echo $reverse; ?>">
                <div class="gallery-text">
                    <h2><?php the_title(); ?></h2>
                    <div class="gallery-description">
                        <?php
                        $content = apply_filters('the_content', get_the_content());

                        // Убираем все <img> из контента, чтобы остался только текст
                        $text_only = preg_replace('/<img[^>]+>/i', '', $content);
                        echo $text_only;
                        ?>
                    </div>
                </div>

                <div class="gallery-images">
                    <?php
                    // Получаем все <img> из контента
                    preg_match_all('/<img[^>]+>/i', $content, $images);
                    foreach ($images[0] as $img) {
                        // Извлекаем src из img тега
                        preg_match('/src="([^"]+)"/i', $img, $src_match);
                        if (!empty($src_match[1])) {
                            $image_src = $src_match[1];
                            // Получаем оригинальное изображение (убираем размеры из URL)
                            $original_src = preg_replace('/-\d+x\d+\.(jpg|jpeg|png|gif)/i', '.$1', $image_src);

                            echo '<div class="gallery-img">';
                            echo '<a href="' . esc_url($original_src) . '" data-src="' . esc_url($original_src) . '">';
                            echo $img;
                            echo '</a>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </section>

        <?php endwhile;
        wp_reset_postdata();
    else :
        echo '<p>Пока нет элементов галереи.</p>';
    endif;
    ?>
</main>

<?php get_footer(); ?>