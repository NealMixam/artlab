<?php
/* Template Name: Contacts */
get_header();
?>
<?php get_sidebar(); ?>

<section class="contacts-page">
    <div class="container">
        <h1 class="section-title"><?php the_title(); ?></h1>
        <div class="page-content">
            <?php
            while (have_posts()) : the_post();
                the_content();
            endwhile;
            ?>
            <iframe src="https://yandex.ru/map-widget/v1/?z=12&ol=biz&oid=51016359228" width="100%" height="400" frameborder="0"></iframe>
            <div class="contacts-item">
                <span><b>Телефон: </b><a href="tel:+79255401177">+7(925) 540-11-77</a></span>
                <span><b>Email: </b><a href="mailto:Artlabtasso@gmail.com">Artlabtasso@gmail.com</a></span>
                <span><b>Адрес:</b> г.Москва, ул. Куусинена, 11, к. 3</span>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
