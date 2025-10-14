<aside class="site-sidebar">
    <?php
    if ( has_nav_menu('sidebar-menu') ) {
        wp_nav_menu([
            'theme_location' => 'sidebar-menu',
            'container' => 'nav',
            'container_class' => 'sidebar-nav',
            'menu_class' => 'sidebar-menu',
        ]);
    } else {
        echo '<p>Меню в сайдбаре пока не назначено.</p>';
    }
    ?>
</aside>
