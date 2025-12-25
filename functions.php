<?php
function twentytwentyfive_child_enqueue_styles() {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        ['parent-style']
    );
}
add_action('wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_styles');
    
function create_custom_post_types() {
    register_post_type('products', [
        'labels'=>['name'=>'Товары','singular_name'=>'Товар'],
        'public'=>true,
        'has_archive'=>true,
        'rewrite'=>['slug'=>'products'],
        'show_in_rest'=>true,
        'supports'=>['title','editor','thumbnail','excerpt']
    ]);

    register_post_type('coatings', [
        'labels'=>['name'=>'Покрытия','singular_name'=>'Покрытие'],
        'public'=>true,
        'has_archive'=>true,
        'rewrite'=>['slug'=>'coatings'],
        'show_in_rest'=>true,
        'supports'=>['title','editor','thumbnail','excerpt']
    ]);
}
add_action('init','create_custom_post_types');

function create_custom_taxonomies() {
    // Товары
    register_taxonomy('product_brand','products',['labels'=>['name'=>'Бренды'],'hierarchical'=>false,'show_in_rest'=>true,'rewrite'=>['slug'=>'brand']]);
    register_taxonomy('product_style','products',['labels'=>['name'=>'Стиль интерьера'],'hierarchical'=>false,'show_in_rest'=>true,'rewrite'=>['slug'=>'style']]);
    register_taxonomy('product_application','products',['labels'=>['name'=>'Сложность нанесения'],'hierarchical'=>false,'show_in_rest'=>true,'rewrite'=>['slug'=>'application']]);
    register_taxonomy('product_finish','products',['labels'=>['name'=>'Характеристика покрытия'],'hierarchical'=>false,'show_in_rest'=>true,'rewrite'=>['slug'=>'finish']]);

    register_taxonomy('coating_type','coatings',['labels'=>['name'=>'Тип покрытия'],'hierarchical'=>false,'show_in_rest'=>true,'rewrite'=>['slug'=>'coating_type']]);
}
add_action('init','create_custom_taxonomies');

function enqueue_filter_scripts() {
    wp_enqueue_script('filters-js', get_stylesheet_directory_uri().'/js/filters.js', ['jquery'], '1.0', true);
    wp_localize_script('filters-js', 'filters_vars', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('filters_nonce')
    ]);
}
add_action('wp_enqueue_scripts','enqueue_filter_scripts');

add_action('wp_ajax_filter_products','filter_products_ajax');
add_action('wp_ajax_nopriv_filter_products','filter_products_ajax');
add_action('wp_ajax_filter_coatings','filter_coatings_ajax');
add_action('wp_ajax_nopriv_filter_coatings','filter_coatings_ajax');

function filter_products_ajax(){
    check_ajax_referer('filters_nonce','nonce');

    $args = ['post_type'=>'products','posts_per_page'=>12,'paged'=>$_POST['paged']??1];

    $tax_query = ['relation'=>'AND'];

    if(!empty($_POST['product_finish'])) $tax_query[] = ['taxonomy'=>'product_finish','field'=>'slug','terms'=>$_POST['product_finish']];
    if(!empty($_POST['product_style'])) $tax_query[] = ['taxonomy'=>'product_style','field'=>'slug','terms'=>$_POST['product_style']];
    if(!empty($_POST['product_brand'])) $tax_query[] = ['taxonomy'=>'product_brand','field'=>'slug','terms'=>$_POST['product_brand']];
    if(!empty($_POST['product_application'])) $tax_query[] = ['taxonomy'=>'product_application','field'=>'slug','terms'=>$_POST['product_application']];
    if(count($tax_query)>1) $args['tax_query']=$tax_query;

    // price (основная цена товара)
    $meta_query=[];
    if(!empty($_POST['min_price']) && !empty($_POST['max_price'])){
        $meta_query[]=['key'=>'price','value'=>[floatval($_POST['min_price']),floatval($_POST['max_price'])],'type'=>'NUMERIC','compare'=>'BETWEEN'];
    }
    elseif(!empty($_POST['min_price'])) $meta_query[]=['key'=>'price','value'=>floatval($_POST['min_price']),'type'=>'NUMERIC','compare'=>'>='];
    elseif(!empty($_POST['max_price'])) $meta_query[]=['key'=>'price','value'=>floatval($_POST['max_price']),'type'=>'NUMERIC','compare'=>'<='];

    // price for work (цена за работу)
    if(!empty($_POST['min_work_price']) && !empty($_POST['max_work_price'])){
        $meta_query[]=['key'=>'_product_work_price','value'=>[floatval($_POST['min_work_price']),floatval($_POST['max_work_price'])],'type'=>'NUMERIC','compare'=>'BETWEEN'];
    }
    elseif(!empty($_POST['min_work_price'])) $meta_query[]=['key'=>'_product_work_price','value'=>floatval($_POST['min_work_price']),'type'=>'NUMERIC','compare'=>'>='];
    elseif(!empty($_POST['max_work_price'])) $meta_query[]=['key'=>'_product_work_price','value'=>floatval($_POST['max_work_price']),'type'=>'NUMERIC','compare'=>'<='];

    if($meta_query) {
        if(count($meta_query) > 1) {
            $meta_query['relation'] = 'AND';
        }
        $args['meta_query'] = $meta_query;
    }

    $query = new WP_Query($args);
    ob_start();
    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            get_template_part('template-parts/product','card');
        }
    } else {
        echo '<p>Ничего не найдено</p>';
    }
    wp_reset_postdata();

    wp_send_json_success(['html'=>ob_get_clean()]);
}
// function filter_products_ajax(){
//     check_ajax_referer('filters_nonce','nonce');
//
//     $args = ['post_type'=>'products','posts_per_page'=>12,'paged'=>$_POST['paged']??1];
//
//     $tax_query = ['relation'=>'AND'];
//
//     if(!empty($_POST['product_finish'])) $tax_query[] = ['taxonomy'=>'product_finish','field'=>'slug','terms'=>$_POST['product_finish']];
//     if(!empty($_POST['product_style'])) $tax_query[] = ['taxonomy'=>'product_style','field'=>'slug','terms'=>$_POST['product_style']];
//     if(!empty($_POST['product_brand'])) $tax_query[] = ['taxonomy'=>'product_brand','field'=>'slug','terms'=>$_POST['product_brand']];
//     if(!empty($_POST['product_application'])) $tax_query[] = ['taxonomy'=>'product_application','field'=>'slug','terms'=>$_POST['product_application']];
//     if(count($tax_query)>1) $args['tax_query']=$tax_query;
//
//     // price
//     $meta_query=[];
//     if(!empty($_POST['min_price']) && !empty($_POST['max_price'])){
//         $meta_query[]=['key'=>'price','value'=>[floatval($_POST['min_price']),floatval($_POST['max_price'])],'type'=>'NUMERIC','compare'=>'BETWEEN'];
//     }
//     elseif(!empty($_POST['min_price'])) $meta_query[]=['key'=>'price','value'=>floatval($_POST['min_price']),'type'=>'NUMERIC','compare'=>'>='];
//     elseif(!empty($_POST['max_price'])) $meta_query[]=['key'=>'price','value'=>floatval($_POST['max_price']),'type'=>'NUMERIC','compare'=>'<='];
//     if($meta_query) $args['meta_query']=$meta_query;
//
//     $query=new WP_Query($args);
//     ob_start();
//     if($query->have_posts()){
//         while($query->have_posts()){ $query->the_post();
//             get_template_part('template-parts/product','card');
//         }
//     } else { echo '<p>Ничего не найдено</p>'; }
//     wp_reset_postdata();
//
//     wp_send_json_success(['html'=>ob_get_clean()]);
// }

function filter_coatings_ajax(){
    check_ajax_referer('filters_nonce','nonce');
    $args = ['post_type'=>'coatings','posts_per_page'=>12,'paged'=>$_POST['paged']??1];
    $tax_query=[];
    if(!empty($_POST['coating_type'])) $tax_query[]=['taxonomy'=>'coating_type','field'=>'slug','terms'=>$_POST['coating_type']];
    if($tax_query) $args['tax_query']=$tax_query;

    $query=new WP_Query($args);
    ob_start();
    if($query->have_posts()){
        while($query->have_posts()){ $query->the_post();
            get_template_part('template-parts/coating','card');
        }
    } else { echo '<p>Ничего не найдено</p>'; }
    wp_reset_postdata();
    wp_send_json_success(['html'=>ob_get_clean()]);
}

function theme_enqueue_product_gallery() {
    // ВРЕМЕННО: подключаем на всех страницах для теста
    wp_enqueue_style('lightgallery-css', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css');
    wp_enqueue_script('lightgallery-js', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.umd.min.js', array('jquery'), null, true);
    wp_enqueue_script('lightgallery-zoom', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.umd.js', array('lightgallery-js'), null, true);
    wp_enqueue_script('lightgallery-init', get_stylesheet_directory_uri() . '/js/lightgallery-init.js', array('lightgallery-js'), null, true);

    // Для товаров
    if (is_singular('products')) {
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
        wp_enqueue_script('threesixty-js', get_stylesheet_directory_uri() . '/js/threesixty.js', array('jquery'), null, true);
        wp_enqueue_script('single-product-init', get_stylesheet_directory_uri() . '/js/single-product.js', array('lightgallery-js','threesixty-js'), null, true);
    }
}
add_action('wp_enqueue_scripts', 'theme_enqueue_product_gallery');
// function theme_enqueue_product_gallery() {
//     wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
//     wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
//     wp_enqueue_style('lightgallery-css', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css');
//     wp_enqueue_script('lightgallery-js', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.umd.min.js', array('jquery'), null, true);
//     wp_enqueue_script('lightgallery-zoom', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.umd.js', array('lightgallery-js'), null, true);
//
//     wp_enqueue_script('threesixty-js', get_stylesheet_directory_uri() . '/js/threesixty.js', array('jquery'), null, true);
//
//     wp_enqueue_script('single-product-init', get_stylesheet_directory_uri() . '/js/single-product.js', array('lightgallery-js','threesixty-js'), null, true);
// }
// add_action('wp_enqueue_scripts', 'theme_enqueue_product_gallery');

// === 360° Images Metabox ===
function add_360_images_metabox() {
    add_meta_box(
        'product_360_images',
        '360° Изображения продукта',
        'render_360_images_metabox',
        'products',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_360_images_metabox');

function render_360_images_metabox($post) {
    $images = get_post_meta($post->ID, '_product_360_images', true);
    ?>
    <div id="product-360-images-wrapper">
        <p>Загрузите 16 изображений (или больше) для вращения продукта.</p>
        <ul id="product-360-images-list">
            <?php
            if (!empty($images)) {
                foreach ($images as $img_id) {
                    $img_url = wp_get_attachment_url($img_id);
                    echo '<li><img src="' . esc_url($img_url) . '" style="max-width:80px;"><input type="hidden" name="product_360_images[]" value="' . esc_attr($img_id) . '"></li>';
                }
            }
            ?>
        </ul>
        <button type="button" class="button" id="upload-360-images">Добавить изображения</button>
    </div>

    <script>
        jQuery(document).ready(function($) {
            var frame;
            $('#upload-360-images').on('click', function(e) {
                e.preventDefault();
                if (frame) { frame.open(); return; }

                frame = wp.media({
                    title: 'Выберите 360° изображения',
                    button: { text: 'Добавить' },
                    multiple: true
                });

                frame.on('select', function() {
                    var attachments = frame.state().get('selection').toJSON();
                    var list = $('#product-360-images-list');
                    list.empty();
                    attachments.forEach(function(attachment) {
                        list.append(
                            '<li><img src="' + attachment.url + '" style="max-width:80px;"><input type="hidden" name="product_360_images[]" value="' + attachment.id + '"></li>'
                        );
                    });
                });

                frame.open();
            });
        });
    </script>
    <style>
        #product-360-images-list { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
        #product-360-images-list li { list-style:none; }
    </style>
    <?php
}

function save_360_images_metabox($post_id) {
    if (isset($_POST['product_360_images'])) {
        $images = array_map('intval', $_POST['product_360_images']);
        update_post_meta($post_id, '_product_360_images', $images);
    } else {
        delete_post_meta($post_id, '_product_360_images');
    }
}
add_action('save_post', 'save_360_images_metabox');


function mytheme_child_setup()
{
    register_nav_menus([
        'primary' => __('Главное меню', 'mytheme-child'),
        'sidebar-menu' => __('Меню в сайдбаре', 'mytheme-child'),
    ]);
}

add_action('after_setup_theme', 'mytheme_child_setup');


add_action('after_setup_theme', function () {
    remove_theme_support('block-templates');
});

function create_gallery_post_type() {
    register_post_type('gallery_item', array(
        'labels' => array(
            'name' => 'Галерея',
            'singular_name' => 'Элемент галереи',
            'add_new' => 'Добавить новый',
            'add_new_item' => 'Добавить элемент галереи',
            'edit_item' => 'Редактировать элемент',
            'new_item' => 'Новый элемент',
            'view_item' => 'Просмотреть элемент',
            'search_items' => 'Найти элемент',
            'not_found' => 'Элементы не найдены',
            'menu_name' => 'Галерея'
        ),
        'public' => true,
        'menu_icon' => 'dashicons-format-gallery',
        'supports' => array('title', 'thumbnail', 'editor'),
        'has_archive' => false,
        'rewrite' => array('slug' => 'gallery'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'create_gallery_post_type');

add_theme_support('post-thumbnails');

// === Цена товара ===
function add_product_price_metabox() {
    add_meta_box(
            'product_price_metabox',
            'Цена товара',
            'render_product_price_metabox',
            'products',
            'side', // боковая колонка
            'default'
    );
}
add_action('add_meta_boxes', 'add_product_price_metabox');

function render_product_price_metabox($post) {
    $price = get_post_meta($post->ID, '_product_price', true);
    ?>
    <label for="product_price">Введите цену (в ₽):</label>
    <input
            type="number"
            name="product_price"
            id="product_price"
            value="<?php echo esc_attr($price); ?>"
            step="0.01"
            min="0"
            style="width:100%;margin-top:8px;"
    />
    <?php
}

function save_product_price_metabox($post_id) {
    if (isset($_POST['product_price'])) {
        update_post_meta($post_id, '_product_price', floatval($_POST['product_price']));
    }
}
add_action('save_post', 'save_product_price_metabox');


// === Настройки секции "О нас" ===
function about_section_settings_init() {
    add_settings_section(
            'about_section',
            'Секция "О нас"',
            '__return_false',
            'reading'
    );

    add_settings_field(
            'about_text',
            'Текст "О нас"',
            'about_text_render',
            'reading',
            'about_section'
    );
    register_setting('reading', 'about_text', ['sanitize_callback' => 'wp_kses_post']);

    add_settings_field(
            'about_images',
            'Изображения "О нас"',
            'about_images_render',
            'reading',
            'about_section'
    );
    register_setting('reading', 'about_images', ['sanitize_callback' => 'sanitize_text_field']);
}
add_action('admin_init', 'about_section_settings_init');

function about_text_render() {
    $text = get_option('about_text', '');
    wp_editor($text, 'about_text', [
            'textarea_name' => 'about_text',
            'textarea_rows' => 8,
            'media_buttons' => true,
    ]);
}

function about_images_render() {
    $images = get_option('about_images', '');
    ?>
    <div id="about-images-wrapper">
        <input type="hidden" name="about_images" id="about_images" value="<?php echo esc_attr($images); ?>">
        <button type="button" class="button" id="upload-about-images">Выбрать изображения</button>
        <div id="about-images-preview" style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap;">
            <?php
            if (!empty($images)) {
                $ids = explode(',', $images);
                foreach ($ids as $id) {
                    $src = wp_get_attachment_image_src($id, 'thumbnail')[0];
                    echo '<img src="' . esc_url($src) . '" style="width:80px;height:auto;border:1px solid #ccc;">';
                }
            }
            ?>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($){
            var frame;
            $('#upload-about-images').on('click', function(e){
                e.preventDefault();
                if (frame) { frame.open(); return; }

                frame = wp.media({
                    title: 'Выберите изображения для секции "О нас"',
                    button: { text: 'Добавить' },
                    multiple: true
                });

                frame.on('select', function(){
                    var selection = frame.state().get('selection');
                    var ids = [];
                    var preview = $('#about-images-preview');
                    preview.empty();
                    selection.each(function(attachment){
                        ids.push(attachment.id);
                        preview.append('<img src="'+attachment.attributes.sizes.thumbnail.url+'" style="width:80px;height:auto;border:1px solid #ccc;">');
                    });
                    $('#about_images').val(ids.join(','));
                });

                frame.open();
            });
        });
    </script>
    <?php
}

function handle_contact_form_submission() {
    if (isset($_POST['contact_submitted'])) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $message = sanitize_textarea_field($_POST['message']);

        $to = 'admin@artlabmsk.ru';
        $subject = 'Новое сообщение с сайта';
        $body = "Имя: $name\nEmail: $email\nТелефон: $phone\n\nСообщение:\n$message";
        $headers = ['Content-Type: text/plain; charset=UTF-8', "Reply-To: $email"];

        wp_mail($to, $subject, $body, $headers);
    }
}
add_action('init', 'handle_contact_form_submission');

// === Галерея изображений продукта ===
function add_product_gallery_metabox() {
    add_meta_box(
            'product_gallery_images',
            'Галерея изображений товара',
            'render_product_gallery_metabox',
            'products',
            'normal',
            'default'
    );
}
add_action('add_meta_boxes', 'add_product_gallery_metabox');

function render_product_gallery_metabox($post) {
    $images = get_post_meta($post->ID, '_product_gallery_images', true);
    ?>
    <div id="product-gallery-wrapper">
        <p>Выберите изображения, которые будут отображаться в галерее товара.</p>
        <ul id="product-gallery-list">
            <?php
            if (!empty($images)) {
                foreach ($images as $img_id) {
                    $img_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                    echo '<li><img src="' . esc_url($img_url) . '" style="max-width:80px;"><input type="hidden" name="product_gallery_images[]" value="' . esc_attr($img_id) . '"></li>';
                }
            }
            ?>
        </ul>
        <button type="button" class="button" id="upload-product-gallery">Добавить изображения</button>
    </div>

    <script>
        jQuery(document).ready(function($) {
            var frame;
            $('#upload-product-gallery').on('click', function(e) {
                e.preventDefault();
                if (frame) { frame.open(); return; }

                frame = wp.media({
                    title: 'Выберите изображения для галереи',
                    button: { text: 'Добавить' },
                    multiple: true
                });

                frame.on('select', function() {
                    var attachments = frame.state().get('selection').toJSON();
                    var list = $('#product-gallery-list');
                    list.empty();
                    attachments.forEach(function(attachment) {
                        list.append(
                            '<li><img src="' + attachment.url + '" style="max-width:80px;"><input type="hidden" name="product_gallery_images[]" value="' + attachment.id + '"></li>'
                        );
                    });
                });

                frame.open();
            });
        });
    </script>
    <style>
        #product-gallery-list { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
        #product-gallery-list li { list-style:none; }
    </style>
    <?php
}

function save_product_gallery_metabox($post_id) {
    if (isset($_POST['product_gallery_images'])) {
        $images = array_map('intval', $_POST['product_gallery_images']);
        update_post_meta($post_id, '_product_gallery_images', $images);
    } else {
        delete_post_meta($post_id, '_product_gallery_images');
    }
}
add_action('save_post', 'save_product_gallery_metabox');

function mytheme_enqueue_scripts() {
    $script_path = get_stylesheet_directory() . '/js/sidebar-toggle.js';
    wp_enqueue_script(
            'sidebar-toggle',
            get_stylesheet_directory_uri() . '/js/sidebar-toggle.js',
            array(),
            file_exists($script_path) ? filemtime($script_path) : false,
            true
    );
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');

// === Добавляем ссылку "Копировать" в список записей ===
add_filter('post_row_actions', function($actions, $post) {
    if ($post->post_type === 'products') {
        $actions['duplicate'] = '<a href="' . wp_nonce_url(
                        admin_url('admin.php?action=duplicate_product&post=' . $post->ID),
                        'duplicate_product_' . $post->ID
                ) . '" title="Сделать копию этого товара">Копировать</a>';
    }
    return $actions;
}, 10, 2);

// === Логика копирования записи ===
add_action('admin_action_duplicate_product', function() {
    if (empty($_GET['post'])) {
        wp_die('Нет ID записи для копирования.');
    }

    $post_id = intval($_GET['post']);
    check_admin_referer('duplicate_product_' . $post_id);

    $post = get_post($post_id);
    if (!$post) {
        wp_die('Запись не найдена.');
    }

    $new_post = [
            'post_title'   => $post->post_title . ' (копия)',
            'post_content' => $post->post_content,
            'post_status'  => 'draft',
            'post_type'    => $post->post_type,
            'post_author'  => get_current_user_id(),
    ];

    $new_post_id = wp_insert_post($new_post);

    // Копируем метаданные
    $meta = get_post_meta($post_id);
    foreach ($meta as $key => $values) {
        foreach ($values as $value) {
            update_post_meta($new_post_id, $key, maybe_unserialize($value));
        }
    }

    // Копируем термины (категории, метки и т.п.)
    $taxonomies = get_object_taxonomies($post->post_type);
    foreach ($taxonomies as $taxonomy) {
        $terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'ids']);
        wp_set_object_terms($new_post_id, $terms, $taxonomy);
    }

    // Перенаправляем на экран редактирования новой записи
    wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
    exit;
});

// === Цена за работу для товара ===
function add_product_work_price_metabox() {
    add_meta_box(
        'product_work_price_metabox',
        'Цена за работу',
        'render_product_work_price_metabox',
        'products',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_product_work_price_metabox');

function render_product_work_price_metabox($post) {
    $work_price = get_post_meta($post->ID, '_product_work_price', true);
    ?>
    <label for="product_work_price">Введите цену за работу (в ₽):</label>
    <input
        type="number"
        name="product_work_price"
        id="product_work_price"
        value="<?php echo esc_attr($work_price); ?>"
        step="0.01"
        min="0"
        style="width:100%;margin-top:8px;"
        placeholder="0.00"
    />
    <p class="description">Стоимость работы по нанесению/установке товара</p>
    <?php
}

function save_product_work_price_metabox($post_id) {
    if (isset($_POST['product_work_price'])) {
        $work_price = sanitize_text_field($_POST['product_work_price']);
        if (!empty($work_price)) {
            update_post_meta($post_id, '_product_work_price', floatval($work_price));
        } else {
            delete_post_meta($post_id, '_product_work_price');
        }
    }
}
add_action('save_post', 'save_product_work_price_metabox');

add_filter('big_image_size_threshold', '__return_false');

add_filter('jpeg_quality', function() {
    return 100;
});

add_filter('wp_editor_set_quality', function() {
    return 100;
});

