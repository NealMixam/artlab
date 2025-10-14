<?php get_header(); ?>

<div class="products-archive">

    <h1 class="products-title">Каталог товаров</h1>

    <div class="products-layout">
        <aside class="products-filters">
            <?php get_template_part('template-parts/product', 'filters'); ?>
        </aside>

        <div class="products-content">
            <div id="products-grid" class="products-grid">
                <?php
                if(have_posts()){
                    while(have_posts()){ the_post();
                        get_template_part('template-parts/product','card');
                    }
                } else {
                    echo '<p>Ничего не найдено</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
