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
                <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/logo.png'; ?>" alt="Logo" width="200" height="70"/>
            </a>
        </div>
        <div class="site-contacts desktop-only">
            <a href="tel:+79255401177">+7(925) 540-11-77</a>
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
                <a href="tel:+79255401177">+7 (925) 540-11-77</a>
            </div>
        </nav>
    </div>
</header>


<main class="site-content site-main">
    <div class="container layout">


