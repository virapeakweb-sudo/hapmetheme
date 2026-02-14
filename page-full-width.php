<?php
/**
 * Template Name: تمام صفحه (Full Width)
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main class="w-full min-h-screen bg-white">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <div class="entry-content w-full">
                <?php
                /* * محتوای صفحه را لود می‌کند.
                 * اگر از صفحه‌ساز (المنتور/گوتنبرگ) استفاده می‌کنید،
                 * خود صفحه‌ساز عرض و چیدمان داخلی را مدیریت می‌کند.
                 */
                the_content(); 
                ?>
            </div>

        </article>

        <?php
    endwhile; 
    ?>

</main>

<?php get_footer(); ?>