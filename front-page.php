<?php
/* Path: wp-content/themes/hapomeo/front-page.php
توضیحات: نسخه داینامیک صفحه اصلی. محصولات و مقالات از دیتابیس وردپرس/ووکامرس خوانده می‌شوند.
*/

get_header(); ?>

    <main class="flex-grow container mx-auto p-4 md:p-6 space-y-12">
        
        <!-- Hero Section (بدون تغییر - استاتیک باقی می‌ماند چون معمولاً بنر تبلیغاتی است) -->
        <section class="relative pt-6 pb-12 lg:pt-16 lg:pb-24 overflow-hidden bg-orange-100 rounded-[2.5rem]" id="hero">
            <div class="absolute top-0 right-0 -z-10 w-full h-full">
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50 -mb-20 -ml-20"></div>
                <div class="absolute top-20 left-20 w-4 h-4 bg-orange-300 rounded-full opacity-30 animate-pulse"></div>
            </div>

            <div class="container mx-auto px-4">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    <!-- Text Content -->
                    <div class="lg:w-1/2 text-center lg:text-right pr-5 relative z-10">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-orange-200 text-orange-600 text-xs md:text-sm font-bold mb-6 shadow-sm animate-fade-in-up">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                            </span>
                            ارسال رایگان برای خریدهای بالای 3 میلیون تومان
                        </div>
                        <h2 class="text-4xl lg:text-4xl font-black text-slate-800 leading-tight mb-6 animate-fade-in-up delay-100">
                            بهترین ها برای
                            <span class="text-orange-500"> بهترین دوست</span>
                        </h2>
                        <p class="text-slate-600 text-lg leading-relaxed mb-8 max-w-xl mx-auto lg:mx-0 animate-fade-in-up delay-200">
                            تخصصی‌ترین فروشگاه غذا و لوازم جانبی. ما بهترین‌ها را گلچین کرده‌ایم تا دوست کوچک شما همیشه سالم و پرانرژی باشد.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-in-up delay-300">
                            <a href="<?php echo wc_get_page_permalink( 'shop' ); ?>" class="group relative px-8 py-4 bg-orange-500 rounded-2xl text-white font-bold shadow-xl shadow-orange-200 overflow-hidden transition-all hover:scale-105 hover:shadow-orange-300">
                                <span class="relative flex items-center justify-center gap-2">خرید کنید <i class="fas fa-paw"></i></span>
                            </a>
                        </div>
                    </div>
                    <!-- Hero Image -->
                    <div class="lg:w-1/2 relative z-10 w-full">
                        <img src="https://hapoomeo.com/wp-content/uploads/2026/01/top-banner.webp" alt="پت شاپ هاپومیو" class="relative z-10 w-full h-auto rounded-[2.5rem]   transform hover:scale-[1.02] transition-transform duration-500">
                    </div>
                </div>
            </div>
        </section>


        
        <!-- Categories (لینک‌ها داینامیک شدند) -->
        <section>
            <h2 class="text-2xl font-bold text-center mb-8 text-slate-800">دسته‌بندی‌های محبوب</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php
                // لیست دسته‌بندی‌های پیشنهادی (اسلاگ‌ها را بر اساس دسته‌بندی‌های واقعی خود تغییر دهید)
                $cats = [
                    ['slug' => 'dog', 'name' => 'محصولات سگ', 'icon' => 'fa-dog', 'color' => 'green'],
                    ['slug' => 'cat', 'name' => 'محصولات گربه', 'icon' => 'fa-cat', 'color' => 'orange'],
                    ['slug' => 'health', 'name' => 'بهداشتی و درمانی', 'icon' => 'fa-pump-medical', 'color' => 'green'],
                    ['slug' => 'pet-accessories', 'name' => 'لوازم و اسباب‌بازی', 'icon' => 'fa-shapes', 'color' => 'orange'],
                ];

                foreach($cats as $cat_info) {
                    // گرفتن لینک واقعی دسته‌بندی
                    $term = get_term_by('slug', $cat_info['slug'], 'product_cat');
                    $link = $term ? get_term_link($term) : '#';
                    $count = $term ? $term->count : 0;
                    ?>
                    <a href="<?php echo esc_url($link); ?>" class="group bg-white p-6 rounded-3xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all text-center border border-slate-100">
                        <div class="w-20 h-20 bg-<?php echo $cat_info['color']; ?>-50 text-<?php echo $cat_info['color']; ?>-500 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl group-hover:bg-<?php echo $cat_info['color']; ?>-500 group-hover:text-white transition-colors duration-300 transform group-hover:rotate-6">
                            <i class="fas <?php echo $cat_info['icon']; ?>"></i>
                        </div>
                        <h3 class="font-bold text-slate-700 group-hover:text-<?php echo $cat_info['color']; ?>-600 transition-colors"><?php echo $cat_info['name']; ?></h3>
                        <p class="text-xs text-slate-400 mt-2"><?php echo $count; ?> کالا</p>
                    </a>
                    <?php
                }
                ?>
            </div>
        </section>

        <!-- Best Selling Products (بخش داینامیک محصولات) -->
        <section>
            <div class="flex flex-col md:flex-row justify-between items-start mb-8 gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3">
                        <span class="w-2 h-8 bg-orange-500 rounded-full block"></span>
                        پرفروش‌ترین محصولات
                    </h2>
                    <p class="text-slate-500 text-sm mt-2 mr-5">محبوب‌ترین انتخاب‌های خریداران</p>
                </div>
                <a href="<?php echo wc_get_page_permalink( 'shop' ); ?>" class="text-orange-500 hover:text-orange-600 font-bold text-sm bg-orange-50 px-4 py-2 rounded-lg transition-colors flex items-center">
                    مشاهده همه <i class="fas fa-arrow-left mr-2"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                <?php
                // کوئری برای گرفتن ۸ محصول پرفروش
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => 8,
                    'meta_key'       => 'total_sales',
                    'orderby'        => 'meta_value_num',
                    'status'         => 'publish',
                );
                
                // اگر محصول پرفروشی نباشد (سایت تازه تاسیس)، محصولات اخیر را نشان می‌دهد
                $products = new WP_Query( $args );
                if ( ! $products->have_posts() ) {
                    $args['meta_key'] = '';
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
                    $products = new WP_Query( $args );
                }

                if ( $products->have_posts() ) :
                    while ( $products->have_posts() ) : $products->the_post();
                        // فراخوانی تمپلیت کارت محصول که قبلا در content-product.php ساختیم
                        wc_get_template_part( 'content', 'product' );
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="col-span-4 text-center text-slate-500">هنوز محصولی اضافه نشده است.</p>';
                endif;
                ?>
            </div>
        </section>

        <!-- New Arrivals Banner -->
        <section class="rounded-[2.5rem] overflow-hidden relative group ">
            <a href="<?php echo wc_get_page_permalink( 'shop' ); ?>" class="block w-full h-full">
                <picture>
                    <source 
                        media="(max-width: 767px)" 
                        srcset="https://hapoomeo.com/wp-content/uploads/2026/01/cat-banner-mobile-2.webp">
                    
                    <img 
                        src="https://hapoomeo.com/wp-content/uploads/2026/01/cat-banner-1-scaled.webp" 
                        alt="انواع غذای گربه - برندهای برتر" 
                        class="w-full h-auto object-cover transition-transform duration-700 "
                    >
                </picture>
            </a>
        </section>


        <!-- Blog Section (داینامیک شده) -->
        <section>
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-book-open text-orange-500"></i> آخرین مقالات
                    </h2>
                    <p class="text-slate-500 text-sm mt-2">دانستنی‌های مفید برای سلامت پت شما</p>
                </div>
                <a href="https://hapoomeo.com/blog/" class="text-orange-500 font-bold text-sm hover:text-orange-600">مشاهده همه <i class="fas fa-arrow-left"></i></a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $blog_query = new WP_Query( array(
                    'posts_per_page' => 3,
                    'post_type' => 'post', // فقط نوشته‌های وبلاگ
                    'ignore_sticky_posts' => 1
                ));

                if ( $blog_query->have_posts() ) :
                    while ( $blog_query->have_posts() ) : $blog_query->the_post();
                        ?>
                        <a href="<?php the_permalink(); ?>" class="blog-card block bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-lg transition-all border border-slate-100 group">
                            <div class="overflow-hidden h-48 relative bg-slate-100">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail('medium', array('class' => 'blog-image w-full h-full object-cover transition-transform duration-500')); ?>
                                <?php else : ?>
                                    <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-image text-3xl"></i></div>
                                <?php endif; ?>
                                
                                <?php 
                                $categories = get_the_category();
                                if ( ! empty( $categories ) ) : ?>
                                    <span class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-orange-600 shadow-sm">
                                        <?php echo esc_html( $categories[0]->name ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center text-xs text-slate-400 mb-3 gap-2">
                                    <i class="far fa-calendar"></i> <?php echo get_the_date(); ?>
                                </div>
                                <h3 class="font-bold text-slate-800 text-lg mb-3 leading-snug group-hover:text-orange-500 transition-colors line-clamp-2">
                                    <?php the_title(); ?>
                                </h3>
                                <div class="text-slate-500 text-sm leading-relaxed mb-4 line-clamp-2">
                                    <?php the_excerpt(); ?>
                                </div>
                                <span class="read-more-btn inline-block border border-slate-200 text-slate-600 text-xs font-bold py-2 px-4 rounded-full transition-colors">ادامه مطلب</span>
                            </div>
                        </a>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="text-slate-500 col-span-3">هنوز مقاله‌ای منتشر نشده است.</p>';
                endif;
                ?>
            </div>
        </section>

    </main>

<?php get_footer(); ?>