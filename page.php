<?php
/**
 * The template for displaying all single pages
 *
 * Path: wp-content/themes/hapomeo/page.php
 * Version: 1.0
 * توضیحات: قالب صفحات عمومی سایت (مثل درباره ما، تماس با ما)
 */

get_header(); ?>

<main class="container mx-auto px-4 py-8 md:py-12 flex-grow">
    
    <?php while ( have_posts() ) : the_post(); ?>

        <!-- Breadcrumbs (مسیر راهنما) -->
        <div class="text-sm text-slate-500 mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap">
            <?php 
            if ( function_exists('woocommerce_breadcrumb') ) {
                // استفاده از بردکرامب هوشمند ووکامرس اگر فعال باشد
                woocommerce_breadcrumb(array(
                    'delimiter'   => '<i class="fas fa-chevron-left text-[10px] opacity-50 mx-2"></i>',
                    'wrap_before' => '<nav class="flex items-center">',
                    'wrap_after'  => '</nav>',
                ));
            } else {
                // فال‌بک ساده برای زمانی که ووکامرس نصب نیست
                echo '<a href="' . home_url() . '" class="hover:text-orange-500"><i class="fas fa-home"></i></a>';
                echo '<i class="fas fa-chevron-left text-[10px] opacity-50 mx-2"></i>';
                echo '<span class="font-bold text-slate-800">' . get_the_title() . '</span>';
            }
            ?>
        </div>

        <!-- Main Page Container -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden max-w-5xl mx-auto">
            
            <!-- Page Header With Decor -->
            <div class="bg-slate-50/50 p-8 md:p-12 border-b border-slate-100 text-center relative overflow-hidden">
                <!-- اشکال تزئینی پس‌زمینه -->
                <div class="absolute top-0 left-0 w-40 h-40 bg-orange-100 rounded-full blur-3xl opacity-40 -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-40 h-40 bg-blue-100 rounded-full blur-3xl opacity-40 translate-x-1/2 translate-y-1/2"></div>
                
                <h1 class="relative z-10 text-3xl md:text-5xl font-black text-slate-800 leading-tight mb-2"><?php the_title(); ?></h1>
                
                <!-- اگر صفحه تصویر شاخص داشته باشد، اینجا نمایش می‌دهیم -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="mt-8 -mb-16 relative z-10 shadow-2xl rounded-2xl inline-block max-w-2xl w-full">
                        <?php the_post_thumbnail('large', array('class' => 'rounded-2xl w-full h-auto')); ?>
                    </div>
                    <div class="h-8 md:h-12"></div> <!-- Spacer for negative margin -->
                <?php endif; ?>
            </div>

            <!-- Page Content -->
            <div class="p-6 md:p-12 text-slate-600 leading-loose text-justify">
                <!-- کلاس prose از Tailwind Typography برای استایل‌دهی به محتوای خام وردپرس استفاده می‌شود -->
                <div class="prose prose-slate prose-lg max-w-none 
                            prose-headings:font-black prose-headings:text-slate-800 
                            prose-h1:text-3xl prose-h2:text-2xl prose-h3:text-xl
                            prose-p:leading-8 prose-p:mb-6
                            prose-a:text-orange-500 prose-a:no-underline hover:prose-a:underline prose-a:font-bold
                            prose-img:rounded-3xl prose-img:shadow-lg prose-img:my-8
                            prose-strong:text-slate-900 prose-strong:font-bold
                            prose-ul:list-disc prose-ul:pr-5 prose-ol:list-decimal prose-ol:pr-5
                            prose-blockquote:border-r-4 prose-blockquote:border-orange-500 prose-blockquote:bg-orange-50 prose-blockquote:py-2 prose-blockquote:pr-4 prose-blockquote:rounded-r-lg prose-blockquote:italic">
                    
                    <?php the_content(); ?>
                    
                </div>
            </div>

        </div>

    <?php endwhile; ?>

</main>

<?php get_footer(); ?>