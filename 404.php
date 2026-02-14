<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * Path: wp-content/themes/hapomeo/404.php
 * Version: 5.0
 * توضیحات: این صفحه زمانی نمایش داده می‌شود که آدرس وارد شده در سایت وجود نداشته باشد.
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main class="flex-grow container mx-auto px-4 py-12 flex flex-col items-center justify-center text-center relative overflow-hidden min-h-[60vh]">
    
    <!-- Background Decor (تزئینات پس‌زمینه) -->
    <div class="absolute top-10 left-10 w-32 h-32 bg-orange-100 rounded-full blur-3xl opacity-50 -z-10"></div>
    <div class="absolute bottom-10 right-10 w-40 h-40 bg-blue-100 rounded-full blur-3xl opacity-50 -z-10"></div>

    <!-- 404 Illustration -->
    <div class="relative mb-6">
        <h1 class="text-[150px] md:text-[200px] font-black text-slate-100 leading-none select-none">404</h1>
        <div class="absolute inset-0 flex items-center justify-center animate-float">
            <!-- تصویر پت گم شده (از پلیس‌هولدر استفاده شده، می‌توانید با تصویر واقعی خود جایگزین کنید) -->
            <img src="https://placehold.co/250x250/transparent/orange?text=🐶" alt="پت گم شده" class="w-48 h-48 md:w-64 md:h-64 object-contain drop-shadow-2xl">
        </div>
    </div>

    <!-- Text Content -->
    <div class="relative z-10 max-w-lg mx-auto">
        <h2 class="text-2xl md:text-4xl font-extrabold text-slate-800 mb-4">اوه! راه رو گم کردید؟</h2>
        <p class="text-slate-500 mb-8 text-lg leading-relaxed">
            متاسفانه صفحه‌ای که دنبالش می‌گردید وجود ندارد. شاید آدرس را اشتباه وارد کرده‌اید یا صفحه حذف شده است.
        </p>

        <!-- Search Bar (داینامیک شده برای جستجوی محصولات) -->
        <div class="relative max-w-xs mx-auto mb-8">
            <form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" 
                       id="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" 
                       class="search-field w-full bg-white border border-slate-200 rounded-xl px-4 py-3 pr-10 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all shadow-sm" 
                       placeholder="جستجو در محصولات..." 
                       value="<?php echo get_search_query(); ?>" 
                       name="s" />
                <input type="hidden" name="post_type" value="product" />
                <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-500 transition-colors bg-transparent border-none cursor-pointer">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo home_url('/'); ?>" class="bg-orange-500 text-white font-bold py-3.5 px-8 rounded-xl hover:bg-orange-600 transition-colors shadow-lg shadow-orange-200 flex items-center justify-center gap-2 transform hover:-translate-y-1">
                <i class="fas fa-home"></i> صفحه اصلی
            </a>
            <a href="<?php echo wc_get_page_permalink( 'shop' ); ?>" class="bg-white text-slate-700 border border-slate-200 font-bold py-3.5 px-8 rounded-xl hover:bg-slate-50 hover:text-orange-500 transition-colors shadow-sm flex items-center justify-center gap-2 transform hover:-translate-y-1">
                <i class="fas fa-store"></i> فروشگاه
            </a>
        </div>
    </div>

</main>

<?php get_footer(); ?>