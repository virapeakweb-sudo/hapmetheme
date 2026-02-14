<?php
/**
 * Template Name: آرشیو بلاگ اختصاصی
 * Path: wp-content/themes/your-theme/page-blog.php
 * Description: قالب اختصاصی برای نمایش لیست مقالات (مجله خبری) با طراحی هماهنگ
 */

get_header(); 

// تنظیمات صفحه‌بندی
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$args = array(
    'post_type'      => 'post',
    'posts_per_page' => 9, // تعداد پست در هر صفحه
    'paged'          => $paged,
    'post_status'    => 'publish'
);
$blog_query = new WP_Query( $args );
?>

<!-- فراخوانی Tailwind و فونت (جهت اطمینان) -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Vazirmatn', sans-serif; background-color: #f8fafc; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    /* استایل‌های اختصاصی کارت‌ها */
    .blog-card:hover .post-thumb img { transform: scale(1.05); }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<div class="blog-archive-wrapper" dir="rtl">
    
    <!-- Hero / Title Section -->
    <div class="bg-white border-b border-slate-100 py-8 md:py-12">
        <div class="container mx-auto px-4 md:p-6 text-center">
            <span class="text-orange-500 font-bold text-sm bg-orange-50 px-3 py-1 rounded-full mb-4 inline-block">مجله خبری</span>
            <h1 class="text-3xl md:text-4xl font-black text-slate-800 mb-4"><?php the_title(); ?></h1>
            
            <!-- Breadcrumbs -->
            <div class="flex justify-center items-center text-xs text-slate-500 gap-2">
                <a href="<?php echo home_url(); ?>" class="hover:text-orange-500 transition-colors"><i class="fas fa-home"></i> خانه</a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 font-medium">بلاگ</span>
            </div>
        </div>
    </div>

    <main class="container mx-auto p-4 md:p-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Main Content: Posts Grid -->
            <div class="lg:col-span-8">
                
                <?php if ( $blog_query->have_posts() ) : ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php 
                        while ( $blog_query->have_posts() ) : $blog_query->the_post(); 
                            $post_id = get_the_ID();
                            // دریافت اولین دسته‌بندی
                            $categories = get_the_category();
                            $cat_name = !empty($categories) ? $categories[0]->name : 'عمومی';
                            $cat_link = !empty($categories) ? get_category_link($categories[0]->term_id) : '#';
                            
                            // آواتار نویسنده
                            $author_id = get_the_author_meta('ID');
                            $author_avatar = get_avatar_url($author_id);
                        ?>
                        
                        <article class="blog-card bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden group flex flex-col h-full hover:shadow-lg transition-all duration-300">
                            <!-- Thumbnail -->
                            <a href="<?php the_permalink(); ?>" class="post-thumb relative h-56 overflow-hidden block">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <img src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title(); ?>" class="w-full h-full object-cover transition-transform duration-700">
                                <?php else: ?>
                                    <div class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                        <i class="fas fa-image text-4xl"></i>
                                    </div>
                                <?php endif; ?>
                                <!-- Category Badge -->
                                <span class="absolute top-4 right-4 bg-white/90 backdrop-blur text-orange-600 text-xs font-bold px-3 py-1.5 rounded-xl shadow-sm hover:bg-orange-500 hover:text-white transition-colors">
                                    <?php echo esc_html($cat_name); ?>
                                </span>
                            </a>
                            
                            <!-- Content -->
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex items-center gap-2 text-xs text-slate-400 mb-3">
                                    <div class="flex items-center gap-1">
                                        <i class="far fa-calendar-alt text-orange-400"></i>
                                        <span><?php echo get_the_date(); ?></span>
                                    </div>
                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                    <div class="flex items-center gap-1">
                                        <i class="far fa-clock text-orange-400"></i>
                                        <!-- محاسبه ساده زمان مطالعه -->
                                        <span><?php echo ceil(str_word_count(strip_tags(get_the_content())) / 250); ?> دقیقه</span>
                                    </div>
                                </div>

                                <h2 class="text-lg font-bold text-slate-800 mb-3 leading-snug group-hover:text-orange-500 transition-colors">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <p class="text-sm text-slate-500 mb-4 line-clamp-3 leading-relaxed flex-grow">
                                    <?php echo get_the_excerpt(); ?>
                                </p>
                                
                                <!-- Footer -->
                                <div class="pt-4 border-t border-slate-50 flex justify-between items-center mt-auto">
                                    <div class="flex items-center gap-2">
                                        <img src="<?php echo esc_url($author_avatar); ?>" class="w-8 h-8 rounded-full border border-slate-100" alt="<?php the_author(); ?>">
                                        <span class="text-xs font-bold text-slate-700"><?php the_author(); ?></span>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="text-xs font-bold text-orange-500 hover:bg-orange-50 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                                        ادامه مطلب <i class="fas fa-arrow-left"></i>
                                    </a>
                                </div>
                            </div>
                        </article>

                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center">
                        <?php
                        $pagination = paginate_links( array(
                            'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                            'format'    => '?paged=%#%',
                            'current'   => max( 1, get_query_var( 'paged' ) ),
                            'total'     => $blog_query->max_num_pages,
                            'prev_text' => '<i class="fas fa-chevron-right"></i>',
                            'next_text' => '<i class="fas fa-chevron-left"></i>',
                            'type'      => 'array',
                        ) );

                        if ( ! empty( $pagination ) ) {
                            echo '<div class="flex gap-2">';
                            foreach ( $pagination as $page ) {
                                // استایل‌دهی به لینک‌های صفحه‌بندی
                                $page = str_replace( 'page-numbers', 'w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:border-orange-500 hover:text-orange-500 transition-all font-bold shadow-sm', $page );
                                $page = str_replace( 'current', '!bg-orange-500 !text-white !border-orange-500 shadow-orange-200', $page );
                                echo $page;
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <?php wp_reset_postdata(); ?>

                <?php else : ?>
                    <div class="text-center py-20 bg-white rounded-3xl border border-slate-100">
                        <i class="fas fa-inbox text-6xl text-slate-200 mb-4 block"></i>
                        <p class="text-slate-500 font-bold">هنوز مقاله‌ای منتشر نشده است.</p>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <aside class="lg:col-span-4 space-y-8">
                <div class="sticky top-28 space-y-6">
                    
                    <!-- Search Widget -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2 pb-3 border-b border-slate-50">
                            <i class="fas fa-search text-orange-500"></i> جستجو در بلاگ
                        </h3>
                        <form role="search" method="get" class="relative" action="<?php echo home_url( '/' ); ?>">
                            <input type="hidden" name="post_type" value="post" />
                            <input type="search" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pr-4 pl-10 text-sm focus:outline-none focus:border-orange-400 focus:ring-1 focus:ring-orange-400 transition-all" placeholder="جستجو کنید..." value="<?php echo get_search_query(); ?>" name="s">
                            <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-500 transition-colors">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Categories Widget -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2 pb-3 border-b border-slate-50">
                            <i class="fas fa-folder-open text-orange-500"></i> دسته‌بندی‌ها
                        </h3>
                        <ul class="space-y-2">
                            <?php 
                            $categories = get_categories( array(
                                'orderby' => 'name',
                                'parent'  => 0
                            ) );
                            foreach ( $categories as $category ) {
                                echo '<li>
                                    <a href="' . get_category_link( $category->term_id ) . '" class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-orange-50 group transition-colors">
                                        <span class="text-slate-600 text-sm font-medium group-hover:text-orange-600 transition-colors">' . esc_html( $category->name ) . '</span>
                                        <span class="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md group-hover:bg-white transition-colors">' . $category->count . '</span>
                                    </a>
                                </li>';
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- Popular Posts Widget (Based on Views) -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2 pb-3 border-b border-slate-50">
                            <i class="fas fa-fire text-orange-500"></i> محبوب‌ترین‌ها
                        </h3>
                        <div class="space-y-4">
                            <?php
                            $popular_posts = new WP_Query( array(
                                'posts_per_page' => 4,
                                'meta_key' => 'dorak_post_views_count', // کلید متا بازدید که در فایل single.php دیدم
                                'orderby' => 'meta_value_num',
                                'order' => 'DESC',
                                'ignore_sticky_posts' => 1
                            ) );
                            
                            if ( $popular_posts->have_posts() ) :
                                while ( $popular_posts->have_posts() ) : $popular_posts->the_post();
                            ?>
                                <a href="<?php the_permalink(); ?>" class="flex gap-3 group items-center">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 shrink-0 relative border border-slate-100">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                        <span class="absolute top-0 right-0 w-4 h-4 bg-orange-500 rounded-bl-lg flex items-center justify-center text-[8px] text-white font-bold">
                                            <?php echo $popular_posts->current_post + 1; ?>
                                        </span>
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="text-xs font-bold text-slate-700 leading-snug mb-1 group-hover:text-orange-500 transition-colors line-clamp-2"><?php the_title(); ?></h4>
                                        <div class="flex items-center gap-2 text-[10px] text-slate-400">
                                            <span><i class="far fa-eye"></i> <?php echo get_post_meta( get_the_ID(), 'dorak_post_views_count', true ) ?: '0'; ?></span>
                                            <span><i class="far fa-calendar"></i> <?php echo get_the_date('d M'); ?></span>
                                        </div>
                                    </div>
                                </a>
                            <?php 
                                endwhile; 
                                wp_reset_postdata();
                            else: 
                                echo '<p class="text-xs text-slate-400">مطلبی یافت نشد.</p>';
                            endif;
                            ?>
                        </div>
                    </div>

                    <!-- Newsletter Banner -->
                    <div class="bg-gradient-to-br from-orange-500 to-orange-400 rounded-3xl p-6 text-center text-white relative overflow-hidden shadow-lg shadow-orange-200">
                        <i class="fas fa-envelope-open-text text-6xl absolute -bottom-4 -left-4 text-white opacity-20 rotate-12"></i>
                        <h4 class="font-bold text-lg mb-2 relative z-10">عضویت در خبرنامه</h4>
                        <p class="text-xs text-orange-100 mb-4 relative z-10 leading-relaxed">برای دریافت آخرین اخبار و مقالات آموزشی، ایمیل خود را وارد کنید.</p>
                        <form class="relative z-10">
                            <input type="email" placeholder="ایمیل شما..." class="w-full rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none shadow-sm mb-2">
                            <button type="button" class="w-full bg-slate-800 text-white text-xs font-bold py-2.5 rounded-xl hover:bg-slate-900 transition-colors shadow-md">عضویت</button>
                        </form>
                    </div>

                </div>
            </aside>

        </div>
    </main>

</div>

<?php get_footer(); ?>