<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body <?php body_class(); ?>>

<div class="site-wrapper">
<header class="site-header">
    <div class="container header-inner">
        <div class="site-branding">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
                <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/logo.png'; ?>" alt="Logo" width="200" height="70"/>
            </a>
        </div>
        <div class="site-contacts desktop-only">
            <?php get_template_part('template-parts/social-icons'); ?>
            <a class="btn-phone" href="tel:+79295401177">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.1 8.81 19.79 19.79 0 0 1 .03.18 2 2 0 0 1 2 0h3a2 2 0 0 1 2 1.72c.12.83.37 1.64.72 2.4a2 2 0 0 1-.45 2.18l-1.27 1.27a16 16 0 0 0 6.88 6.88l1.27-1.27a2 2 0 0 1 2.18-.45c.76.35 1.57.6 2.4.72A2 2 0 0 1 22 16.92z"/>
                </svg>
                +7(929) 540-11-77</a>
        </div>

        <button class="burger-menu" aria-label="Открыть меню">
            <span class="burger-bar"></span>
            <span class="burger-bar"></span>
            <span class="burger-bar"></span>
        </button>

        <nav class="mobile-menu">
            <?php
            if ( has_nav_menu('sidebar-menu') ) {
                wp_nav_menu([
                        'theme_location' => 'sidebar-menu',
                        'container' => false,
                        'menu_class' => 'mobile-menu-list',
                ]);
            } else {
                echo '<p>Меню пока не назначено.</p>';
            }
            ?>
            <div class="mobile-contacts mobile-only">
                <a class="btn-phone" href="tel:+79295401177">+7 (929) 540-11-77</a>
                <?php get_template_part('template-parts/social-icons'); ?>
            </div>
        </nav>
    </div>
</header>

    <div class="main-container">
        <?php get_sidebar(); ?>

        <div class="content-wrapper">
            <main class="site-main">


