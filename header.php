<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <div class="container header-inner">
        <div class="site-branding">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
                <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/logo.png'; ?>" alt="Logo" width="200px" height="70px"/>
            </a>
            <p class="site-description"><?php bloginfo('description'); ?></p>
        </div>
        <div class="site-contacts">
            <a href="tel:+79258647733">+7(925)-864-77-33</a>
        </div>

<!--        <nav class="main-nav">-->
<!--            --><?php
//            wp_nav_menu([
//                'theme_location' => 'primary',
//                'container'      => false,
//                'menu_class'     => 'main-menu',
//            ]);
//            ?>
<!--        </nav>-->
    </div>
</header>

<main class="site-content">
    <div class="container <?php echo (is_post_type_archive(['products', 'coatings'])) ? '' : 'layout'; ?>">


