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

    // price
    $meta_query=[];
    if(!empty($_POST['min_price']) && !empty($_POST['max_price'])){
        $meta_query[]=['key'=>'price','value'=>[floatval($_POST['min_price']),floatval($_POST['max_price'])],'type'=>'NUMERIC','compare'=>'BETWEEN'];
    }
    elseif(!empty($_POST['min_price'])) $meta_query[]=['key'=>'price','value'=>floatval($_POST['min_price']),'type'=>'NUMERIC','compare'=>'>='];
    elseif(!empty($_POST['max_price'])) $meta_query[]=['key'=>'price','value'=>floatval($_POST['max_price']),'type'=>'NUMERIC','compare'=>'<='];
    if($meta_query) $args['meta_query']=$meta_query;

    $query=new WP_Query($args);
    ob_start();
    if($query->have_posts()){
        while($query->have_posts()){ $query->the_post();
            get_template_part('template-parts/product','card');
        }
    } else { echo '<p>Ничего не найдено</p>'; }
    wp_reset_postdata();

    wp_send_json_success(['html'=>ob_get_clean()]);
}

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
    wp_enqueue_style('lightgallery-css', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css');
    wp_enqueue_script('lightgallery-js', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.umd.min.js', array('jquery'), null, true);
    wp_enqueue_script('lightgallery-zoom', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.umd.js', array('lightgallery-js'), null, true);

    wp_enqueue_script('threesixty-js', get_stylesheet_directory_uri() . '/js/threesixty.js', array('jquery'), null, true);

    wp_enqueue_script('product-360-init', get_stylesheet_directory_uri() . '/js/product-360.js', array('lightgallery-js','threesixty-js'), null, true);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_product_gallery');

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



