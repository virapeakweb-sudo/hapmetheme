<?php
/**
 * Template Name: پست تکی (بلاگ)
 * Template Post Type: post
 * نسخه: 3.0.0 (استایل هماهنگ با صفحه محصول)
 */

get_header(); 

while ( have_posts() ) : the_post();
    
    // --- 1. جمع‌آوری داده‌ها ---
    $post_id = get_the_ID();
    
    // دریافت بازدید (با فال‌بک)
    $post_views = get_post_meta( $post_id, 'dorak_post_views_count', true );
    $post_views = ($post_views == '') ? '0' : $post_views;
    
    // برآورد زمان مطالعه
    $content = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( strip_tags( $content ) );
    $reading_time = ceil( $word_count / 250 ) . ' دقیقه';

    // تصویر شاخص
    $thumbnail_url = has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'full') : '';
    
    // اطلاعات نویسنده
    $author_id = get_the_author_meta('ID');
    $author_name = get_the_author();
    $author_avatar = get_avatar_url($author_id, ['size' => 96]);
    $author_desc = get_the_author_meta('description');

    // --- 2. تولید اسکیما (SEO Schema JSON-LD) ---
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink()
        ),
        'headline' => get_the_title(),
        'description' => get_the_excerpt(),
        'image' => $thumbnail_url,
        'author' => array(
            '@type' => 'Person',
            'name' => $author_name,
            'url' => get_author_posts_url( $author_id )
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => get_bloginfo( 'name' ),
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => get_site_icon_url()
            )
        ),
        'datePublished' => get_the_date( 'c' ),
        'dateModified' => get_the_modified_date( 'c' )
    );
?>



<!-- SEO Schema Output -->
<script type="application/ld+json">
<?php echo json_encode($schema); ?>
</script>

<style>
    body {
        background-color: #f8fafc; /* gray-50 */
    }
    
    /* Scroll Progress Bar */
    .scroll-progress {
        position: fixed; top: 0; left: 0; height: 3px; background: #f97316; z-index: 100; transition: width 0.1s;
    }

    /* Typography Fixes */
    .prose p { margin-bottom: 1.5em; line-height: 1.8; text-align: justify; color: #475569;font-family:IRANSansXV!important; }
    .prose h2, .prose h3 { color: #1e293b; font-weight: 800; margin-top: 2em; margin-bottom: 1em; }
    .prose h2 { font-size: 1.5rem; }
    .prose h3 { font-size: 1.25rem; }
    .prose ul { list-style-type: disc; padding-right: 1.5em; margin-bottom: 1.5em; }
    .prose img { border-radius: 1rem; margin: 2em auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .prose a { color: #f97316; text-decoration: none; font-weight: 600; }
    .prose a:hover { text-decoration: underline; }
    
    /* Active TOC Link */
    .toc-link.active { color: #f97316; border-right-color: #f97316; background-color: #fff7ed; font-weight: 700; }
    
</style>

<!-- نوار پیشرفت مطالعه -->
<div class="scroll-progress" id="scrollProgress" style="width: 0%"></div>

<div class="product-page-wrapper" dir="rtl">
    <div class="container mx-auto p-4 md:p-6 pb-20">
        
        <!-- Breadcrumbs -->
        <div class="text-xs text-slate-500 mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap pb-2">
            <a href="<?php echo home_url(); ?>" class="hover:text-orange-500 transition-colors"><i class="fas fa-home"></i></a>
            <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
            <a href="<?php echo home_url('/blog'); ?>" class="hover:text-orange-500 transition-colors">بلاگ</a>
            <?php 
            $categories = get_the_category();
            if ( ! empty( $categories ) ) {
                echo '<i class="fas fa-chevron-left text-[10px] opacity-50"></i>';
                echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '" class="hover:text-orange-500 transition-colors">' . esc_html( $categories[0]->name ) . '</a>';
            }
            ?>
            <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
            <span class="text-slate-800 font-medium truncate max-w-[200px]"><?php the_title(); ?></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Main Content (Article) -->
            <article class="lg:col-span-8">
                
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                    
                    <!-- Header Info -->
                    <header class="mb-8 border-b border-slate-100 pb-8">
                        <!-- Categories Badges -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php
                            if ( ! empty( $categories ) ) {
                                foreach( $categories as $category ) {
                                    echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="bg-orange-50 text-orange-600 px-3 py-1 rounded-lg text-xs font-bold hover:bg-orange-500 hover:text-white transition-all">' . esc_html( $category->name ) . '</a>';
                                }
                            }
                            ?>
                        </div>

                        <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-800 leading-tight mb-6">
                            <?php the_title(); ?>
                        </h1>

                        <!-- Meta Data -->
                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                            
                            <span class="w-px h-4 bg-slate-300 hidden sm:block"></span>
                            <div class="flex items-center gap-1">
                                <i class="far fa-calendar-alt text-orange-500"></i>
                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                            </div>
                            <span class="w-px h-4 bg-slate-300 hidden sm:block"></span>
                            <div class="flex items-center gap-1">
                                <i class="far fa-clock text-orange-500"></i>
                                <span>زمان مطالعه: <?php echo $reading_time; ?></span>
                            </div>
                            <span class="w-px h-4 bg-slate-300 hidden sm:block"></span>
                            <div class="flex items-center gap-1">
                                <i class="far fa-eye text-orange-500"></i>
                                <span><?php echo number_format_i18n($post_views); ?> بازدید</span>
                            </div>
                        </div>
                    </header>

                    <!-- Featured Image -->
                    <?php if ( has_post_thumbnail() ) : ?>
                    <figure class="mb-10 relative group rounded-2xl overflow-hidden shadow-sm border border-slate-100">
                        <?php the_post_thumbnail('full', array(
                            'class' => 'w-full h-auto object-cover transform group-hover:scale-105 transition duration-700 ease-in-out',
                            'alt'   => get_the_title()
                        )); ?>
                    </figure>
                    <?php endif; ?>

                    <!-- Content Body -->
                    <div id="post-content" class="prose prose-slate max-w-none entry-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Tags -->
                    <?php
                    $tags = get_the_tags();
                    if ($tags) : ?>
                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <span class="block text-xs font-bold text-slate-400 mb-3"><i class="fas fa-tags ml-1"></i> برچسب‌ها:</span>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach($tags as $tag) {
                                echo '<a href="' . get_tag_link($tag->term_id) . '" class="text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition-colors">#' . $tag->name . '</a>';
                            } ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Author Box -->
                

                <!-- Comments Section -->
                <div class="mt-8 bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-lg text-slate-800 mb-6 flex items-center gap-2">
                        <i class="far fa-comments text-orange-500 bg-orange-50 p-2 rounded-lg"></i>
                        نظرات کاربران
                    </h3>
                    <?php
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                    ?>
                </div>

            </article>

            <!-- Sidebar -->
            <aside class="lg:col-span-4 space-y-6">
                <div class="sticky top-28 space-y-6">
                    
                    <!-- Table of Contents (TOC) -->
                    <div id="toc-container" class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm hidden lg:block">
                        <h5 class="font-bold text-slate-800 mb-4 flex items-center gap-2 pb-3 border-b border-slate-50">
                            <i class="fas fa-list-ul text-orange-500"></i>
                            فهرست مطالب
                        </h5>
                        <nav id="toc-list" class="space-y-1 max-h-[300px] overflow-y-auto custom-scroll pl-2">
                            <!-- JS will populate this -->
                        </nav>
                    </div>

                    <!-- Smart Product Widget -->
                    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                        <?php
                        // منطق هوشمند نمایش محصول مرتبط (دستی یا خودکار)
                        $manual_ids = get_post_meta( $post_id, 'related_products', true );
                        
                        $args = array(
                            'post_type'      => 'product',
                            'posts_per_page' => 3,
                            'stock_status'   => 'instock',
                        );

                        $widget_title = 'محصولات پیشنهادی';
                        
                        if ( ! empty( $manual_ids ) ) {
                            $ids_array = explode( ',', $manual_ids );
                            $args['post__in'] = $ids_array;
                            $args['orderby'] = 'post__in';
                            $widget_title = 'محصولات مرتبط با مقاله';
                        } else {
                            $args['meta_key'] = 'total_sales';
                            $args['orderby']  = 'meta_value_num';
                            $widget_title = 'پرفروش‌ترین‌های ماه';
                        }

                        $loop = new WP_Query( $args );
                        ?>

                        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fas fa-shopping-bag text-orange-500"></i> <?php echo $widget_title; ?>
                            </h5>
                        </div>

                        <div class="p-4 space-y-4">
                            <?php
                            if ( $loop->have_posts() ) :
                                while ( $loop->have_posts() ) : $loop->the_post();
                                    global $product;
                                    ?>
                                    <a href="<?php the_permalink(); ?>" class="flex gap-4 group items-center p-2 rounded-xl hover:bg-slate-50 transition-colors">
                                        <div class="w-16 h-16 rounded-xl overflow-hidden border border-slate-200 bg-white flex-shrink-0 relative">
                                            <?php echo $product->get_image( 'thumbnail', array( 'class' => 'w-full h-full object-cover mix-blend-multiply' ) ); ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h6 class="text-xs font-bold text-slate-700 group-hover:text-orange-500 transition line-clamp-2 leading-5 mb-1">
                                                <?php the_title(); ?>
                                            </h6>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-black text-slate-800">
                                                    <?php echo $product->get_price_html(); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                    <?php
                                endwhile;
                            else :
                                echo '<p class="text-xs text-slate-400 text-center py-4">محصولی موجود نیست.</p>';
                            endif;
                            wp_reset_postdata();
                            ?>
                        </div>
                        <div class="p-3 bg-slate-50 text-center border-t border-slate-100">
                            <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="text-xs font-bold text-orange-500 hover:text-orange-600 transition-colors flex items-center justify-center gap-1">
                                مشاهده فروشگاه <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Social Share -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                        <span class="block text-xs font-bold text-slate-400 mb-4 text-center">اشتراک‌گذاری این مطلب</span>
                        <div class="flex gap-3 justify-center">
                            <a href="https://t.me/share/url?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank" class="w-10 h-10 bg-sky-500 text-white rounded-xl flex items-center justify-center hover:bg-sky-600 hover:-translate-y-1 transition-all shadow-sm shadow-sky-200" title="تلگرام">
                                <i class="fab fa-telegram-plane"></i>
                            </a>
                            <a href="https://wa.me/?text=<?php the_permalink(); ?>" target="_blank" class="w-10 h-10 bg-green-500 text-white rounded-xl flex items-center justify-center hover:bg-green-600 hover:-translate-y-1 transition-all shadow-sm shadow-green-200" title="واتساپ">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank" class="w-10 h-10 bg-slate-800 text-white rounded-xl flex items-center justify-center hover:bg-black hover:-translate-y-1 transition-all shadow-sm shadow-slate-400" title="توییتر/ایکس">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                            <button onclick="navigator.clipboard.writeText('<?php the_permalink(); ?>');alert('لینک کپی شد!');" class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center hover:bg-orange-500 hover:text-white hover:-translate-y-1 transition-all" title="کپی لینک">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </aside>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Scroll Progress Bar
        window.onscroll = function() {
            let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            let scrolled = (winScroll / height) * 100;
            let bar = document.getElementById("scrollProgress");
            if(bar) bar.style.width = scrolled + "%";
        };

        // 2. Auto Generate Table of Contents (TOC)
        const content = document.getElementById('post-content');
        const tocList = document.getElementById('toc-list');
        const tocContainer = document.getElementById('toc-container');
        
        if (content && tocList) {
            const headers = content.querySelectorAll('h2, h3'); // شامل h2 و h3
            
            if (headers.length > 0) {
                headers.forEach((header, index) => {
                    // Assign ID if missing
                    if (!header.id) {
                        header.id = 'section-' + (index + 1);
                    }
                    
                    // Create Link
                    const link = document.createElement('a');
                    link.href = '#' + header.id;
                    // استایل متفاوت برای h2 و h3
                    const isH3 = header.tagName.toLowerCase() === 'h3';
                    const indentClass = isH3 ? 'mr-4 text-xs' : 'text-sm font-bold';
                    
                    link.className = `toc-link block text-slate-600 hover:text-orange-500 py-2 border-r-2 border-transparent hover:border-orange-500 pr-3 transition-all ${indentClass}`;
                    link.innerText = header.innerText;
                    
                    tocList.appendChild(link);
                });
            } else {
                // Hide TOC if no headers found
                if(tocContainer) tocContainer.style.display = 'none';
            }
        }

        // 3. Scroll Spy / Active State Highlighter for TOC
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    document.querySelectorAll('.toc-link').forEach(link => {
                        link.classList.remove('active');
                    });
                    
                    const id = entry.target.getAttribute('id');
                    const link = document.querySelector(`.toc-link[href="#${id}"]`);
                    if (link) {
                        link.classList.add('active');
                    }
                }
            });
        }, { rootMargin: '-100px 0px -60% 0px' });

        if(content) {
            content.querySelectorAll('h2, h3').forEach((section) => {
                observer.observe(section);
            });
        }
    });
</script>

<?php 
endwhile;
get_footer(); 
?>