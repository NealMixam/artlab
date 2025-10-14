<?php get_header(); ?>

<div class="coatings-archive">
    <h1 class="coatings-title">Каталог покрытий</h1>

    <div class="coatings-layout">
        <aside class="coatings-filters">
            <?php get_template_part('template-parts/coating', 'filters'); ?>
        </aside>

        <div class="coatings-content">
            <div id="coatings-grid" class="coatings-grid">
                <?php
                if (have_posts()) {
                    while (have_posts()) { 
                        the_post();
                        get_template_part('template-parts/coating', 'card');
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
