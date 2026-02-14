<?php
/**
 * The Template for displaying all single products
 * Path: wp-content/themes/your-theme/woocommerce/single-product.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

global $product;

// شروع حلقه محصول
while ( have_posts() ) :
    the_post();
    
    $product_id = get_the_ID();
    $average_rating = $product->get_average_rating();
    $review_count = $product->get_review_count();
    $gallery_image_ids = $product->get_gallery_image_ids();
    $main_image_url = wp_get_attachment_image_url( $product->get_image_id(), 'full' );
    
    // دریافت دسته‌بندی اصلی برای نمایش در بردکرامب و تایتل
    $terms = get_the_terms( $product_id, 'product_cat' );
    $main_cat = !empty($terms) ? $terms[0] : null;

    // محاسبات قیمت و تخفیف
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    $discount_percentage = 0;
    if ( $product->is_on_sale() && !empty($regular_price) && !empty($sale_price) && is_numeric($regular_price) && is_numeric($sale_price) ) {
        $discount_percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
    } elseif($product->is_type('variable') && $product->is_on_sale()) {
        // محاسبه تخفیف برای محصولات متغیر (حداکثر تخفیف)
        $prices = $product->get_variation_prices();
        if(!empty($prices['regular_price']) && !empty($prices['sale_price'])) {
             $max_percentage = 0;
             foreach($prices['regular_price'] as $key => $regular) {
                 $sale = $prices['sale_price'][$key];
                 if($regular > $sale) {
                     $perc = round( ( ( $regular - $sale ) / $regular ) * 100 );
                     if($perc > $max_percentage) $max_percentage = $perc;
                 }
             }
             $discount_percentage = $max_percentage;
        }
    }
?>

<!-- فراخوانی Tailwind و استایل‌های اختصاصی -->
<!-- نکته: اگر در هدر قالب خود تیلویند را دارید، خط زیر را حذف کنید -->


<style>
    /* استایل‌های اختصاصی منتقل شده از فایل HTML */
    .product-page-wrapper {
        font-family: 'IRANSansXV', sans-serif;
        background-color: #f8fafc; /* gray-50 */
    }
    
    /* فاصله پایین بادی برای نوار موبایل در قالب اصلی باید هندل شود، اما اینجا یک رپر داخلی می‌سازیم */
    .product-content-wrapper {
        padding-bottom: 80px; 
    }
    @media (min-width: 768px) {
        .product-content-wrapper {
            padding-bottom: 0;
        }
    }

    .nav-link-active {
        color: #fb923c;
        font-weight: 700;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Active Tab Style */
    .tab-button.active {
        border-color: #fb923c;
        color: #fb923c;
        font-weight: 700;
    }
    
    /* FAQ Accordion Styles */
    .faq-item input:checked ~ .faq-content {
        max-height: 500px;
        opacity: 1;
        padding-top: 0.75rem;
    }
    .faq-item input:checked ~ label i { transform: rotate(180deg); }
    .faq-item input:checked ~ label { color: #fb923c; }
    
    /* Sticky Mobile Buy Bar Animation */
    #sticky-buy-bar {
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        transform: translateY(100%);
        opacity: 0;
    }
    #sticky-buy-bar.visible { transform: translateY(0); opacity: 1; }
    #sticky-buy-bar.hidden-footer { transform: translateY(100%); opacity: 0; }

    /* Trust Badge Animation */
    .trust-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* WooCommerce Overrides */
    .woocommerce-notices-wrapper { margin-bottom: 1rem; }
    
    /* استایل دکمه‌های متغیر سفارشی */
    .custom-variation-btn {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .custom-variation-btn.selected {
        background-color: #fff7ed; /* orange-50 */
        border-color: #f97316; /* orange-500 */
        color: #c2410c; /* orange-700 */
        font-weight: 700;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .custom-variation-btn.selected::after {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 12px; height: 100%;
        background: rgba(255,255,255,0.2);
        transform: skewX(-12deg);
    }
    .custom-variation-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f1f5f9;
        border-color: #e2e8f0;
    }
    
    /* مخفی کردن فرم اصلی ووکامرس */
    .hidden-wc-form {
        display: none !important;
    }

    /* ========================================================================
       استایل‌های داینامیک قیمت (وقتی با JS تغییر می‌کند)
       ========================================================================
    */
    #custom-price-container .current-price del,
    .related-products-price del,
    .sticky-price-container del {
        color: #9ca3af; /* gray-400 */
        text-decoration: line-through;
        text-decoration-color: #ef4444; /* red-500 - خط قرمز */
        font-size: 0.75rem; /* text-xs - کوچک‌تر */
        display: block;
        margin-bottom: 0.1rem;
        font-weight: 500;
        opacity: 1 !important;
    }
    
    #custom-price-container .current-price ins,
    .related-products-price ins,
    .sticky-price-container ins {
        text-decoration: none !important; /* حذف زیرخط */
        font-weight: 900; /* font-black */
        color: #1e293b; /* slate-800 */
        font-size: 1.125rem; /* text-lg */
        display: inline-block;
        background: transparent;
    }
    
    /* سایز فونت بزرگتر فقط برای صفحه محصول اصلی */
    #custom-price-container .current-price ins {
        font-size: 1.875rem; /* text-3xl */
    }

    /* استایل مخصوص موبایل برای محصولات مرتبط */
    @media (max-width: 768px) {
        .related-products-price ins {
            font-size: 0.875rem; /* text-sm: کوچک‌تر در موبایل */
        }
        .related-products-price del {
            font-size: 0.65rem; /* خیلی کوچک برای موبایل */
        }
    }

    /* استایل نوتیفیکیشن افزودن به سبد خرید */
    #cart-notification {
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.4s ease;
        transform: translateY(120%);
        opacity: 0;
        z-index: 100;
    }
    #cart-notification.active {
        transform: translateY(0);
        opacity: 1;
    }
</style>

<div class="product-page-wrapper" dir="rtl">
    <div class="product-content-wrapper container mx-auto p-4 md:p-6">
        
        <!-- Breadcrumbs -->
        <div class="text-xs text-slate-500 mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap pb-2">
            <a href="<?php echo home_url(); ?>" class="hover:text-orange-500"><i class="fas fa-home"></i></a>
            <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
            <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="hover:text-orange-500">فروشگاه</a>
            <?php if($main_cat): ?>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <a href="<?php echo get_term_link($main_cat); ?>" class="hover:text-orange-500"><?php echo $main_cat->name; ?></a>
            <?php endif; ?>
            <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
            <span class="text-slate-800 font-medium"><?php the_title(); ?></span>
        </div>

        <!-- WooCommerce Notices (مخفی می‌کنیم تا با نوتیفیکیشن اختصاصی جایگزین کنیم، یا برای خطاها نگه می‌داریم) -->
        <div class="woocommerce-notices-wrapper hidden"></div>

        <!-- Product Card -->
        <div class="bg-white p-4 md:p-8 rounded-3xl shadow-sm border border-slate-100">
            
            <!-- Mobile Title & Meta -->
            <div class="md:hidden mb-4">
                <?php if($main_cat): ?>
                    <a href="<?php echo get_term_link($main_cat); ?>" class="text-xs text-orange-500 font-semibold bg-orange-50 px-2 py-1 rounded-md mb-2 inline-block"><?php echo $main_cat->name; ?></a>
                <?php endif; ?>
                <h1 class="text-xl font-extrabold my-2 text-slate-800 leading-snug"><?php the_title(); ?></h1>
                <div class="flex items-center space-x-reverse space-x-2 text-sm text-slate-500">
                    <div class="flex items-center text-yellow-400 text-xs gap-0.5">
                        <?php 
                        $rating = floatval($average_rating);
                        for($i=1; $i<=5; $i++) {
                            if($i <= $rating) echo '<i class="fas fa-star"></i>';
                            elseif($i - 0.5 <= $rating) echo '<i class="fas fa-star-half-alt"></i>';
                            else echo '<i class="far fa-star text-slate-300"></i>';
                        }
                        ?>
                    </div>
                    <span class="text-xs text-slate-400">(<?php echo $review_count; ?> نظر)</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16">
                <!-- Product Gallery -->
                <div>
                    <div class="relative group">
                        
                        <!-- بج‌های سمت راست (تاریخ انقضا و تخفیف) -->
                        <div class="absolute top-4 right-4 z-10 flex flex-col items-end gap-2">
                            <!-- بج تاریخ انقضا -->
                            <div class="bg-white/95 backdrop-blur-sm text-[11px] font-bold px-3 py-1.5 rounded-lg shadow-sm text-slate-700 border border-slate-100 flex items-center gap-1.5">
                                <i class="far fa-clock text-orange-500"></i>
                                <span>انقضا: > ۶ ماه</span>
                            </div>

                            <!-- بج درصد تخفیف -->
                            <?php if ( $product->is_on_sale() && $discount_percentage > 0 ) : ?>
                                <div class="bg-red-500 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1.5 animate-pulse">
                                    <i class="fas fa-fire"></i>
                                    <span><?php echo $discount_percentage; ?>٪ تخفیف</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ( $main_image_url ) : ?>
                            <img src="<?php echo esc_url( $main_image_url ); ?>" alt="<?php the_title(); ?>" class="w-full rounded-2xl mb-4 shadow-sm border border-slate-100 main-product-img object-cover aspect-square">
                        <?php else: ?>
                             <img src="https://placehold.co/600x600/f1f5f9/94a3b8?text=No+Image" class="w-full rounded-2xl mb-4 shadow-sm border border-slate-100 main-product-img">
                        <?php endif; ?>
                        
                        <!-- Wishlist Placeholder -->
                        <button class="absolute top-4 left-4 bg-white p-2 rounded-full shadow-md text-slate-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100 z-10">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>

                    <!-- Thumbnails Grid -->
                    <?php if ( $gallery_image_ids ) : ?>
                    <div class="grid grid-cols-4 gap-3">
                        <img src="<?php echo esc_url( $main_image_url ); ?>" class="rounded-xl cursor-pointer border-2 border-orange-500 p-0.5 hover:shadow-md transition-all object-cover aspect-square gallery-thumb active" onclick="changeMainImage(this.src)">
                        <?php foreach ( array_slice($gallery_image_ids, 0, 3) as $attachment_id ) : 
                            $img_url = wp_get_attachment_image_url( $attachment_id, 'full' );
                        ?>
                            <img src="<?php echo esc_url( $img_url ); ?>" class="rounded-xl cursor-pointer opacity-70 hover:opacity-100 border-2 border-transparent hover:border-slate-200 transition-all object-cover aspect-square gallery-thumb" onclick="changeMainImage(this.src)">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Product Details -->
                <div class="flex flex-col">
                    <!-- Desktop Header -->
                    <div class="hidden md:block">
                        <div class="flex justify-between items-start">
                            <?php if($main_cat): ?>
                                <a href="<?php echo get_term_link($main_cat); ?>" class="text-sm text-orange-600 font-bold bg-orange-50 px-3 py-1 rounded-lg"><?php echo $main_cat->name; ?></a>
                            <?php endif; ?>
                            <span class="text-xs text-slate-400 bg-slate-50 px-2 py-1 rounded border border-slate-100">کد: <?php echo $product->get_sku() ? $product->get_sku() : 'N/A'; ?></span>
                        </div>
                        <h1 class="text-2xl lg:text-3xl font-black my-4 text-slate-800 leading-tight"><?php the_title(); ?></h1>
                        <div class="flex items-center space-x-reverse space-x-4 text-sm text-slate-500 mb-6">
                            <div class="flex items-center space-x-reverse space-x-1 text-yellow-400">
                                <?php 
                                for($i=1; $i<=5; $i++) {
                                    if($i <= $rating) echo '<i class="fas fa-star"></i>';
                                    elseif($i - 0.5 <= $rating) echo '<i class="fas fa-star-half-alt"></i>';
                                    else echo '<i class="far fa-star text-slate-300"></i>';
                                }
                                ?>
                                <span class="text-sm text-slate-500 mr-2">(<?php echo $review_count; ?> دیدگاه کاربران)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div class="text-slate-600 leading-relaxed mb-6 bg-slate-50 p-4 rounded-xl border-r-4 border-slate-200 text-sm">
                        <?php echo apply_filters( 'woocommerce_short_description', $product->get_short_description() ); ?>
                    </div>

                    <!-- Availability -->
                    <div class="flex items-center gap-2 mb-6 text-sm">
                       <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full <?php echo $product->is_in_stock() ? 'bg-green-400' : 'bg-red-400'; ?> opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 <?php echo $product->is_in_stock() ? 'bg-green-500' : 'bg-red-500'; ?>"></span>
                       </span>
                       <span class="<?php echo $product->is_in_stock() ? 'text-green-700' : 'text-red-700'; ?> font-bold">
                           <?php echo $product->is_in_stock() ? 'موجود در انبار' : 'ناموجود'; ?>
                       </span>
                    </div>

                    <!-- ============================================ -->
                    <!-- START: Custom Buy UI (جایگزین فرم ووکامرس) -->
                    <!-- ============================================ -->
                    
                    <div class="custom-buy-interface">
                        
                        <!-- 1. Custom Variations Buttons -->
                        <?php if ( $product->is_type( 'variable' ) ) : ?>
                            <div class="mb-8" id="custom-variations-wrapper">
                                <?php 
                                $attributes = $product->get_variation_attributes();
                                foreach ( $attributes as $attribute_name => $options ) : 
                                    $nice_name = wc_attribute_label( $attribute_name );
                                    
                                    // مرتب‌سازی عددی
                                    usort($options, function($a, $b) {
                                        $num_a = floatval(preg_replace('/[^0-9.]/', '', $a));
                                        $num_b = floatval(preg_replace('/[^0-9.]/', '', $b));
                                        if ($num_a == $num_b) return 0;
                                        return ($num_a < $num_b) ? -1 : 1;
                                    });
                                    
                                    // انتخاب پیش‌فرض کمترین مقدار
                                    $default_value = isset($options[0]) ? $options[0] : '';
                                ?>
                                    <div class="mb-4">
                                        <label class="font-bold mb-3 block text-sm text-slate-800">انتخاب <?php echo $nice_name; ?>:</label>
                                        <div class="flex flex-wrap gap-3" data-attribute="<?php echo esc_attr( 'attribute_' . sanitize_title( $attribute_name ) ); ?>">
                                            <?php foreach ( $options as $option ) : 
                                                $display_value = $option;
                                                if ( taxonomy_exists( $attribute_name ) ) {
                                                    $term = get_term_by( 'slug', $option, $attribute_name );
                                                    if ( $term && ! is_wp_error( $term ) ) $display_value = $term->name;
                                                } else {
                                                    $display_value = urldecode($option);
                                                }
                                                $is_selected = ($option === $default_value) ? 'selected' : '';
                                            ?>
                                                <button type="button" 
                                                        class="custom-variation-btn px-5 py-2.5 border border-slate-200 rounded-xl hover:border-orange-300 hover:text-orange-600 hover:bg-white text-slate-600 text-sm font-medium <?php echo $is_selected; ?>" 
                                                        data-value="<?php echo esc_attr( $option ); ?>">
                                                    <?php echo esc_html( $display_value ); ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- 2. Custom Buy Box (Desktop Only) -->
                        <div id="main-buy-box" class="bg-white border border-slate-200 p-5 rounded-2xl shadow-lg shadow-slate-200/50 mb-6 relative overflow-hidden hidden md:block">
                             <div class="absolute -top-10 -left-10 w-24 h-24 bg-orange-100 rounded-full opacity-50 blur-xl pointer-events-none"></div>

                            <!-- 
                                =================================================
                                تغییر ۲: جابجایی قیمت به بالای دکمه خرید و ضمانت
                                =================================================
                            -->
                            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 relative z-10">
                                 <div id="custom-price-container">
                                     <?php if ( $product->is_on_sale() ) : ?>
                                         <span class="text-xs text-gray-400 line-through decoration-red-500 decoration-1 mb-1 block original-price font-medium">
                                            <?php echo wc_price($regular_price); ?>
                                         </span>
                                         <div class="flex items-center gap-2">
                                             <div class="text-3xl font-black text-slate-800 tracking-tight current-price no-underline">
                                                <?php echo wc_price($sale_price); ?>
                                             </div>
                                             <?php if($discount_percentage > 0): ?>
                                                <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold mr-2 discount-badge"><?php echo $discount_percentage; ?>٪ تخفیف</span>
                                             <?php endif; ?>
                                         </div>
                                     <?php else : ?>
                                         <div class="text-3xl font-black text-slate-800 tracking-tight current-price no-underline">
                                            <?php echo $product->get_price_html(); ?>
                                         </div>
                                     <?php endif; ?>
                                     <div class="text-xs text-red-500 mt-1 font-bold variation-alert hidden"></div>
                                 </div>

                                 <!-- Custom Quantity Input (Desktop) -->
                                 <div class="flex items-center h-11 border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                     <button type="button" class="qty-btn minus w-10 h-full text-slate-600 hover:bg-orange-100 hover:text-orange-600 transition-colors text-lg font-bold flex items-center justify-center cursor-pointer">-</button>
                                     <input type="text" id="custom-qty" value="1" class="w-12 h-full text-center bg-transparent border-x border-slate-200 text-slate-800 font-bold focus:outline-none" readonly>
                                     <button type="button" class="qty-btn plus w-10 h-full text-slate-600 hover:bg-orange-100 hover:text-orange-600 transition-colors text-lg font-bold flex items-center justify-center cursor-pointer">+</button>
                                 </div>
                            </div>

                            <div class="flex gap-3 relative z-10">
                                <button id="custom-add-to-cart-btn" class="flex-grow bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/40 hover:-translate-y-0.5 transition-all transform active:scale-[0.98] flex justify-center items-center gap-2 <?php echo !$product->is_in_stock() ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                                    <i class="fas fa-shopping-cart text-lg"></i> 
                                    <span class="btn-text"><?php echo $product->is_in_stock() ? 'افزودن به سبد خرید' : 'ناموجود'; ?></span>
                                </button>
                            </div>
                        </div>

                        <!-- Trust Badges (زیر باکس خرید) -->
                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <div class="trust-badge flex flex-col items-center text-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-slate-700 transition-all duration-300">
                                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-sm text-green-500 border border-green-50 shrink-0">
                                    <i class="fas fa-shield-alt text-2xl"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-800">ضمانت اصالت کالا</span>
                                    <span class="text-[10px] text-slate-500 mt-1 leading-snug">تضمین اصالت و سلامت فیزیکی تمام محصولات</span>
                                </div>
                            </div>
                            
                            <div class="trust-badge flex flex-col items-center text-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-slate-700 transition-all duration-300">
                                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-sm text-blue-500 border border-blue-50 shrink-0">
                                    <i class="fas fa-shipping-fast text-2xl"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-800">ارسال سریع و مطمئن</span>
                                    <span class="text-[10px] text-slate-500 mt-1 leading-snug">ارسال فوری به سراسر کشور با بسته‌بندی ایمن</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <!-- 3. Hidden Official WooCommerce Form (Engine) -->
                    <div class="hidden-wc-form">
                        <?php woocommerce_template_single_add_to_cart(); ?>
                    </div>
                    
                    <!-- ============================================ -->
                    <!-- END: Custom Buy UI -->
                    <!-- ============================================ -->

                </div>
            </div>
        </div>

        <!-- Sticky Mobile Buy Bar -->
        <div id="sticky-buy-bar" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 shadow-[0_-4px_15px_rgba(0,0,0,0.05)] z-50 md:hidden flex justify-between items-center pb-safe gap-3">
            
            <div class="flex flex-col flex-1">
                <div class="text-sm font-black text-slate-800 sticky-price-container">
                    <?php echo $product->get_price_html(); ?>
                </div>
            </div>

            <!-- دکمه‌های کنترل تعداد و افزودن به سبد کنار هم -->
            <div class="flex items-center gap-2">
                <!-- کنترل تعداد موبایل -->
                <div class="flex items-center border border-slate-200 rounded-xl h-10 bg-slate-50 overflow-hidden w-24">
                    <button type="button" class="qty-btn plus w-8 h-full text-slate-600 hover:text-orange-600 flex items-center justify-center">+</button>
                    <input type="text" id="mobile-qty" value="1" class="w-8 h-full text-center bg-transparent border-x border-slate-200 text-slate-800 font-bold text-sm focus:outline-none" readonly>
                    <button type="button" class="qty-btn minus w-8 h-full text-slate-600 hover:text-orange-600 flex items-center justify-center">-</button>
                </div>

                <!-- دکمه افزودن -->
                <button id="sticky-add-to-cart-trigger" class="bg-orange-500 text-white text-sm font-bold h-10 px-4 rounded-xl shadow-lg shadow-orange-500/30 hover:bg-orange-600 transition-colors flex items-center gap-2 whitespace-nowrap">
                    <span>خرید</span> <i class="fas fa-shopping-bag"></i>
                </button>
            </div>
        </div>

        <!-- Main Content + Sidebar -->
        <div class="mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Main Column: Tabs -->
            <div class="lg:col-span-8">
                <!-- Tab Buttons -->
                <div class="border-b border-slate-200 mb-6 overflow-x-auto">
                    <nav class="flex space-x-reverse space-x-8 -mb-px min-w-max">
                        <button class="tab-button active whitespace-nowrap py-4 px-2 border-b-2 font-bold text-sm border-transparent text-slate-500 hover:text-slate-700 transition-colors" data-tab="description">توضیحات تکمیلی</button>
                        <button class="tab-button whitespace-nowrap py-4 px-2 border-b-2 font-bold text-sm border-transparent text-slate-500 hover:text-slate-700 transition-colors" data-tab="specs">مشخصات</button>
                        <button class="tab-button whitespace-nowrap py-4 px-2 border-b-2 font-bold text-sm border-transparent text-slate-500 hover:text-slate-700 transition-colors" data-tab="reviews">نظرات کاربران <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs mr-1"><?php echo $review_count; ?></span></button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                    
                    <!-- Description Tab -->
                    <!-- 
                        =================================================
                        تغییر ۳: اضافه کردن آیکون به عناوین تب‌ها
                        =================================================
                    -->
                    <div id="tab-description" class="tab-content leading-loose text-slate-600 text-justify">
                        <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-align-right text-orange-500 bg-orange-50 p-2 rounded-lg"></i>
                            بررسی تخصصی محصول
                        </h3>
                        <div class="prose prose-slate max-w-none">
                            <?php the_content(); ?>
                        </div>
                    </div>
                    
                    <!-- Specs Tab -->
                    <div id="tab-specs" class="tab-content hidden">
                        <h3 class="font-bold text-lg text-slate-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-list-ul text-orange-500 bg-orange-50 p-2 rounded-lg"></i>
                            مشخصات فنی
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                            <?php 
                            $attributes = $product->get_attributes();
                            if($attributes):
                                foreach ( $attributes as $attribute ) : 
                                    $values = array();
                                    if ( $attribute->is_taxonomy() ) {
                                        $values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
                                    } else {
                                        $values = $attribute->get_options();
                                    }
                                    if(empty($values)) continue;
                                    ?>
                                    <div class="flex justify-between py-3 border-b border-slate-50">
                                        <span class="text-slate-500"><?php echo wc_attribute_label( $attribute->get_name() ); ?></span>
                                        <span class="font-semibold text-slate-800"><?php echo wptexturize( implode( ', ', $values ) ); ?></span>
                                    </div>
                                <?php endforeach; 
                            else: echo '<p>مشخصات فنی برای این محصول ثبت نشده است.</p>';
                            endif; ?>
                        </div>
                    </div>
               
                   <!-- Reviews Tab -->
                    <div id="tab-reviews" class="tab-content hidden">
                        <h3 class="font-bold text-lg text-slate-800 mb-6 flex items-center gap-2">
                            <i class="far fa-comments text-orange-500 bg-orange-50 p-2 rounded-lg"></i>
                            نظرات کاربران
                        </h3>
                        <?php 
                        // --- FIX START: Force WooCommerce to use our custom comments.php ---
                        // غیرفعال کردن موقت لودر تمپلیت نظرات ووکامرس
                        // این کار باعث می‌شود به جای single-product-reviews.php، فایل comments.php قالب شما لود شود
                        if ( class_exists( 'WC_Template_Loader' ) ) {
                            remove_filter( 'comments_template', array( 'WC_Template_Loader', 'comments_template_loader' ) );
                        }
                        
                        // فراخوانی تمپلیت استاندارد نظرات وردپرس
                        comments_template();
                        
                        // بازگرداندن فیلتر برای اطمینان از عملکرد صحیح در سایر صفحات (اختیاری)
                        if ( class_exists( 'WC_Template_Loader' ) ) {
                            add_filter( 'comments_template', array( 'WC_Template_Loader', 'comments_template_loader' ) );
                        }
                        // --- FIX END ---
                        ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar Column: Sticky FAQ -->

            <aside class="lg:col-span-4">
                <div class="sticky top-28 space-y-6">
                    <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2 pb-3 border-b border-slate-100">
                            <i class="far fa-question-circle text-orange-500 text-xl"></i>
                            سوالات متداول
                        </h3>
                        <div class="space-y-3">
                            <div class="faq-item">
                                <input type="checkbox" id="faq1" class="hidden">
                                <label for="faq1" class="flex justify-between items-center cursor-pointer font-bold text-sm text-slate-700 hover:text-orange-500 transition-colors py-1">
                                    <span>تاریخ انقضا تا کی هست؟</span>
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-300 text-slate-400"></i>
                                </label>
                                <div class="faq-content max-h-0 overflow-hidden opacity-0 transition-all duration-300 text-slate-500 text-xs leading-relaxed bg-slate-50 rounded-lg px-2">
                                    تمامی محصولات ارسالی دارای حداقل ۶ ماه تاریخ انقضا هستند.
                                </div>
                            </div>
                            <div class="border-t border-slate-50"></div>
                            <div class="faq-item">
                                <input type="checkbox" id="faq2" class="hidden">
                                <label for="faq2" class="flex justify-between items-center cursor-pointer font-bold text-sm text-slate-700 hover:text-orange-500 transition-colors py-1">
                                    <span>ضمانت بازگشت کالا دارید؟</span>
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-300 text-slate-400"></i>
                                </label>
                                <div class="faq-content max-h-0 overflow-hidden opacity-0 transition-all duration-300 text-slate-500 text-xs leading-relaxed bg-slate-50 rounded-lg px-2">
                                    بله، در صورت وجود مشکل فیزیکی تا ۷ روز مهلت بازگشت دارید.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Support Banner -->
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-100 rounded-3xl p-6 text-center relative overflow-hidden">
                         <i class="fas fa-headset text-6xl text-orange-200 absolute -bottom-4 -left-4 opacity-50 rotate-12"></i>
                        <p class="font-bold text-slate-800 mb-1 relative z-10">نیاز به مشاوره دارید؟</p>
                        <p class="text-xs text-slate-500 mb-4 relative z-10">تیم پشتیبانی هاپومیو آماده پاسخ‌گویی است.</p>
                        <a href="tel:+989336261041" class="block bg-white text-orange-500 border border-orange-200 font-bold py-2.5 rounded-xl text-sm hover:bg-orange-500 hover:text-white transition-all shadow-sm relative z-10">
                            تماس با پشتیبانی
                        </a>
                    </div>
                </div>
            </aside>
        </div>

         <!-- Related Products -->
        <div class="mt-16 mb-8">
             <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black text-slate-800 relative pl-4">
                    محصولات مشابه
                    <span class="absolute -bottom-2 right-0 w-1/2 h-1 bg-orange-500 rounded-full"></span>
                </h2>
             </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                <?php
                $related_ids = wc_get_related_products( $product->get_id(), 4 );
                foreach( $related_ids as $related_id ) :
                    $related_product = wc_get_product( $related_id );
                    ?>
                     <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden group hover:shadow-lg transition-all duration-300">
                        <a href="<?php echo get_permalink($related_id); ?>" class="block">
                           <div class="relative overflow-hidden">
                               <?php echo $related_product->get_image('woocommerce_thumbnail', array('class' => 'w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500')); ?>
                           </div>
                           <div class="p-4">
                                <h3 class="font-bold text-slate-700 truncate text-sm mb-3"><?php echo $related_product->get_name(); ?></h3>
                                <div class="flex justify-between items-end">
                                    <div class="related-products-price">
                                        <!-- قیمت با استایل اصلاح شده -->
                                        <?php echo $related_product->get_price_html(); ?>
                                    </div>
                                    <!-- دکمه خرید اصلاح شده با آیکون -->
                                    <button class="bg-orange-50 text-orange-500 rounded-xl px-3 py-1.5 h-9 flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all shadow-sm text-sm font-bold gap-2">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                           </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- 
    =================================================
    تغییر ۱: نوتیفیکیشن اختصاصی افزودن به سبد خرید
    =================================================
-->
<div id="cart-notification" class="fixed bottom-4 left-4 right-4 md:right-auto md:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 z-[100] transform transition-transform duration-300 translate-y-[150%]">
    <div class="flex items-center gap-4">
        <!-- تصویر محصول -->
        <div class="w-16 h-16 bg-slate-50 rounded-xl overflow-hidden shrink-0 border border-slate-100">
            <?php echo $product->get_image('thumbnail', array('class' => 'w-full h-full object-cover')); ?>
        </div>
        
        <!-- متن -->
        <div class="flex-grow">
            <h4 class="font-bold text-slate-800 text-sm mb-1">به سبد خرید اضافه شد!</h4>
            <p class="text-xs text-slate-500 truncate w-48"><?php the_title(); ?></p>
        </div>
        
        <!-- دکمه بستن -->
        <button id="close-notification" class="text-slate-400 hover:text-red-500 transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="mt-4 flex gap-3">
        <a href="<?php echo wc_get_cart_url(); ?>" class="flex-1 bg-orange-500 text-white text-xs font-bold py-2.5 rounded-xl text-center hover:bg-orange-600 transition-colors shadow-sm">
            مشاهده سبد خرید
        </a>
        <button id="continue-shopping" class="flex-1 bg-slate-50 text-slate-600 text-xs font-bold py-2.5 rounded-xl text-center hover:bg-slate-100 transition-colors border border-slate-200">
            ادامه خرید
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Gallery Image Switcher ---
        window.changeMainImage = function(src) {
            document.querySelector('.main-product-img').src = src;
            document.querySelectorAll('.gallery-thumb').forEach(img => {
                if(img.src === src) {
                    img.classList.add('border-orange-500');
                    img.classList.remove('border-transparent');
                } else {
                    img.classList.remove('border-orange-500');
                    img.classList.add('border-transparent');
                }
            });
        };

        // --- Tabs Logic ---
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabName = button.dataset.tab;
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.add('hidden'));
                button.classList.add('active');
                const targetTab = document.getElementById(`tab-${tabName}`);
                if (targetTab) targetTab.classList.remove('hidden');
            });
        });

        // --- Sticky Mobile Buy Bar Logic ---
        const stickyBuyBar = document.getElementById('sticky-buy-bar');
        const mainBuyBox = document.getElementById('main-buy-box'); // باکس خرید دسکتاپ به عنوان مرجع
        const footer = document.querySelector('footer');

        function handleScroll() {
            if (!stickyBuyBar) return;
            if (window.innerWidth >= 768) {
                stickyBuyBar.classList.remove('visible');
                return;
            }
            
            // استفاده از باکس توضیحات (اولین باکس) به عنوان مرجع نمایش اگر باکس خرید دسکتاپ مخفی باشد
            const triggerEl = mainBuyBox && mainBuyBox.offsetParent !== null ? mainBuyBox : document.querySelector('.product-card');
            
            if(!triggerEl) {
                 // اگر هیچ مرجعی پیدا نشد، همیشه نمایش بده (Fallback)
                 stickyBuyBar.classList.add('visible');
                 return;
            }

            const boxRect = triggerEl.getBoundingClientRect();
            // وقتی از پایین باکس خرید رد شدیم نمایش بده
            const scrolledPastBox = boxRect.bottom < 0; 

            if (scrolledPastBox) {
                stickyBuyBar.classList.add('visible');
            } else {
                stickyBuyBar.classList.remove('visible');
            }
            
            if(footer) {
                const footerRect = footer.getBoundingClientRect();
                if (footerRect.top < window.innerHeight) {
                    stickyBuyBar.classList.add('hidden-footer');
                } else {
                    stickyBuyBar.classList.remove('hidden-footer');
                }
            }
        }

        window.addEventListener('scroll', handleScroll);
        window.addEventListener('resize', handleScroll);

        // ============================================
        // INTERACTIVE LOGIC FOR CUSTOM BUY BOX
        // ============================================

        // 1. Quantity Buttons Logic (Desktop & Mobile Sync)
        const desktopQtyInput = document.getElementById('custom-qty');
        const mobileQtyInput = document.getElementById('mobile-qty');
        const hiddenQty = document.querySelector('form.cart input[name="quantity"]'); // Target WC hidden input
        
        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // تعیین اینکه کدام اینپوت باید تغییر کند (یا هر دو)
                let currentVal = parseInt(desktopQtyInput ? desktopQtyInput.value : mobileQtyInput.value) || 1;
                
                if (this.classList.contains('plus')) {
                    currentVal++;
                } else {
                    if (currentVal > 1) currentVal--;
                }
                
                // بروزرسانی تمام اینپوت‌های تعداد
                if(desktopQtyInput) desktopQtyInput.value = currentVal;
                if(mobileQtyInput) mobileQtyInput.value = currentVal;
                
                // Sync with hidden WC input
                if(hiddenQty) {
                    hiddenQty.value = currentVal;
                    // Trigger change in case WC has listeners
                    hiddenQty.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });

        // 2. Custom "Add to Cart" triggers Hidden "Add to Cart"
        const customAddBtn = document.getElementById('custom-add-to-cart-btn');
        const stickyAddBtn = document.getElementById('sticky-add-to-cart-trigger');
        const notification = document.getElementById('cart-notification');
        const closeNotification = document.getElementById('close-notification');
        const continueShopping = document.getElementById('continue-shopping');

        // تابع نمایش نوتیفیکیشن
        function showNotification() {
            notification.classList.remove('translate-y-[150%]');
            notification.classList.add('translate-y-0');
            
            // مخفی کردن خودکار بعد از ۵ ثانیه
            setTimeout(() => {
                hideNotification();
            }, 5000);
        }

        function hideNotification() {
            notification.classList.remove('translate-y-0');
            notification.classList.add('translate-y-[150%]');
        }

        if(closeNotification) closeNotification.addEventListener('click', hideNotification);
        if(continueShopping) continueShopping.addEventListener('click', hideNotification);
        
        function triggerAddToCart(e) {
            e.preventDefault(); // جلوگیری از رفرش
            
            const wcButton = document.querySelector('.single_add_to_cart_button');
            if(wcButton) {
                if(!wcButton.disabled) {
                    wcButton.click(); // کلیک واقعی روی دکمه مخفی ووکامرس
                } else {
                    alert('لطفا گزینه‌های محصول را انتخاب کنید.');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }

        if(customAddBtn) customAddBtn.addEventListener('click', triggerAddToCart);
        if(stickyAddBtn) stickyAddBtn.addEventListener('click', triggerAddToCart);

        // گوش دادن به رویداد "added_to_cart" ووکامرس (AJAX)
        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).on('added_to_cart', function() {
                showNotification();
            });
        }

        // 3. jQuery Bridge for Variations (Requires jQuery because WC uses it)
        if (typeof jQuery !== 'undefined') {
            const $ = jQuery;
            
            // On Custom Attribute Button Click
            $('.custom-variation-btn').on('click', function() {
                const $btn = $(this);
                const value = $btn.data('value');
                const $parent = $btn.closest('[data-attribute]');
                const attributeName = $parent.data('attribute');
                
                // Visual Selection
                $parent.find('.custom-variation-btn').removeClass('selected');
                $btn.addClass('selected');

                // Sync with Hidden Select
                const $hiddenSelect = $(`select[name="${attributeName}"]`);
                if($hiddenSelect.length) {
                    $hiddenSelect.val(value).trigger('change');
                }
            });

            // Trigger Default Selection (اگر ووکامرس پیش‌فرض ست نکرده باشد، ما با جاوااسکریپت انتخاب می‌کنیم)
            // بررسی می‌کنیم که کدام دکمه کلاس selected دارد و کلیکش را تریگر می‌کنیم
            $('.custom-variation-btn.selected').each(function() {
                const $btn = $(this);
                const value = $btn.data('value');
                const $parent = $btn.closest('[data-attribute]');
                const attributeName = $parent.data('attribute');
                const $hiddenSelect = $(`select[name="${attributeName}"]`);
                if($hiddenSelect.length) {
                    // یک تاخیر کوچک برای اطمینان از لود کامل ووکامرس
                    setTimeout(() => {
                        $hiddenSelect.val(value).trigger('change');
                    }, 500);
                }
            });

            // Listen to WooCommerce Variation Events to Update Price and Image
            $('form.variations_form').on('show_variation', function(event, variation) {
                // Update Price
                if (variation.price_html) {
                    // آپدیت قیمت در باکس اصلی
                    $('#custom-price-container .current-price').html(variation.price_html);
                    
                    // آپدیت قیمت در نوار چسبان موبایل (با حفظ ساختار HTML)
                    $('.sticky-price-container').html(variation.price_html);
                    
                    // Hide original elements if sale/regular structure changes
                    $('#custom-price-container .original-price, #custom-price-container .discount-badge').hide();
                }

                // Update Image (Optional - WC handles main image usually, but if needed:)
                if (variation.image && variation.image.src) {
                    window.changeMainImage(variation.image.src);
                }

                // Update Add to Cart Button Status
                const $customBtn = $('#custom-add-to-cart-btn');
                if (variation.is_in_stock) {
                    $customBtn.removeClass('opacity-50 cursor-not-allowed').find('.btn-text').text('افزودن به سبد خرید');
                } else {
                    $customBtn.addClass('opacity-50 cursor-not-allowed').find('.btn-text').text('ناموجود');
                }
                
                $('.variation-alert').addClass('hidden').text('');
            });
            
            // When no variation is found/reset
            $('form.variations_form').on('reset_data', function() {
                // Reset Price (Needs logic to get original range, simplier to keep last or placeholder)
                // $('.custom-variation-btn').removeClass('selected'); // این خط را حذف کردیم تا سلکشن بصری باقی بماند
                $('.variation-alert').removeClass('hidden').text('');
            });
        }
    });
</script>

<?php
endwhile; // End of the loop.
get_footer();
?>