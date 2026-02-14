<?php
/**
 * Template Name: سبد خرید اختصاصی (Custom Cart)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// اطمینان از نصب بودن ووکامرس
if ( ! class_exists( 'WooCommerce' ) ) {
    wp_die('لطفا افزونه ووکامرس را نصب کنید.');
}

// محاسبه متغیرها
$free_shipping_threshold = 3000000;
$cart_total = WC()->cart->get_subtotal();
$remaining_for_free = $free_shipping_threshold - $cart_total;
$progress_percent = ($cart_total / $free_shipping_threshold) * 100;
if ($progress_percent > 100) $progress_percent = 100;

?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    
    <!-- کتابخانه‌های دیزاین -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Vazirmatn', sans-serif; background-color: #f8fafc; }
        
        .processing { opacity: 0.5; pointer-events: none; transition: opacity 0.3s; }
        button[name="update_cart"] { display: none; } 

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* --- استایل‌های اختصاصی و مدرن پیام‌های ووکامرس --- */
        .woocommerce-notices-wrapper { 
            max-width: 1280px; 
            margin: 0 auto 1.5rem auto; 
            padding: 0 1rem; 
        }
        
        .woocommerce-message, .woocommerce-info, .woocommerce-error {
            background-color: #ffffff;
            border: 1px solid #e2e8f0; /* بوردر ملایم دور تا دور */
            border-right: 4px solid #10b981; /* نوار رنگی سمت راست */
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            
            /* چیدمان فلکس برای قرارگیری آیکون و متن کنار هم */
            display: flex;
            align-items: center; /* تراز عمودی */
            flex-wrap: wrap;
            gap: 0.75rem;
            
            color: #334155;
            font-size: 0.9rem;
            font-weight: 500;
            line-height: 1.6;
        }

        /* آیکون چک مارک (با استفاده از FontAwesome که لود شده) */
        .woocommerce-message::before {
            content: '\f00c'; /* کد آیکون تیک */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #10b981; /* رنگ سبز */
            font-size: 1.2rem;
            background-color: #ecfdf5; /* پس‌زمینه محو سبز */
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%; /* دایره‌ای */
            flex-shrink: 0; /* جلوگیری از جمع شدن */
        }

        /* استایل دکمه‌های داخل پیام (مثل بازگشت) */
        .woocommerce-message .button, .woocommerce-info .button, .woocommerce-error .button {
            float: left !important; /* انتقال دکمه به سمت چپ */
            background-color: transparent !important;
            color: #10b981 !important;
            padding: 0.4rem 1rem !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
            border: 1px solid #10b981 !important;
            transition: all 0.2s !important;
            margin-right: auto; /* هل دادن به انتهای سمت چپ */
            display: inline-flex;
            align-items: center;
            order: 99; /* مطمئن شویم دکمه آخر است (در حالت wrap) اما با margin-right: auto سمت چپ می‌رود */
        }
        
        .woocommerce-message .button:hover {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }

        /* استایل خطا (قرمز) */
        .woocommerce-error {
            border-right-color: #ef4444;
        }
        .woocommerce-error::before {
            content: '\f071'; /* علامت تعجب */
            color: #ef4444;
            background-color: #fef2f2;
        }
        
        /* استایل اطلاع‌رسانی (آبی) */
        .woocommerce-info {
            border-right-color: #3b82f6;
        }
        .woocommerce-info::before {
            content: '\f05a'; /* علامت i */
            color: #3b82f6;
            background-color: #eff6ff;
        }

        /* مخفی کردن دکمه "مشاهده سبد خرید" در خود صفحه سبد خرید */
        .woocommerce-message a[href*="cart"] {
            display: none !important;
        }

        /* حالت موبایل */
        @media (max-width: 640px) {
            .woocommerce-message {
                padding: 0.75rem 1rem;
                font-size: 0.8rem;
            }
            .woocommerce-message::before {
                width: 1.5rem;
                height: 1.5rem;
                font-size: 0.9rem;
            }
            /* دکمه در موبایل تمام عرض نشود، فقط کمی جمع‌وجورتر */
            .woocommerce-message .button {
                width: auto;
            }
        }
    </style>
    
    <?php wp_head(); ?>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- هدر اختصاصی سبد خرید -->
    <header class="bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-40 border-b border-slate-100">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            
            <a href="<?php echo home_url(); ?>" class="text-xl md:text-2xl font-extrabold text-orange-500 flex items-center gap-2 no-underline">
               <img src="https://hapoomeo.com/wp-content/uploads/2026/01/logo2.png" class="logoh"  alt="لوگو هاپومیو">
               
            </a>

            <!-- مراحل خرید (دسکتاپ) -->
            <div class="hidden md:flex items-center gap-2 text-sm font-semibold text-slate-500">
                <span class="text-orange-500 flex items-center gap-1"><i class="fas fa-shopping-cart"></i> سبد خرید</span>
                <i class="fas fa-chevron-left text-xs mx-1 opacity-50"></i>
                <a href="<?php echo wc_get_checkout_url(); ?>" class="hover:text-orange-500 transition-colors">تسویه حساب</a>
                <i class="fas fa-chevron-left text-xs mx-1 opacity-50"></i>
                <span class="opacity-50">اتمام خرید</span>
            </div>

            <!-- شمارنده (موبایل) -->
            <div class="md:hidden text-sm font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-lg">
                <span class="text-orange-500 text-xs mr-1">(<?php echo WC()->cart->get_cart_contents_count(); ?> کالا)</span>
            </div>
            
        </div>
    </header>

    <main class="container mx-auto p-4 md:p-6 pb-24 md:pb-6 min-h-[60vh]">
        
        <!-- محل نمایش پیام‌های ووکامرس -->
        <div class="woocommerce-notices-wrapper">
            <?php wc_print_notices(); ?>
        </div>

        <?php if ( WC()->cart->is_empty() ) : ?>
            
            <!-- سبد خالی -->
            <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-slate-100 max-w-2xl mx-auto mt-10">
                <div class="bg-orange-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shopping-basket text-4xl text-orange-200"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 mb-4">سبد خرید شما خالی است!</h2>
                <p class="text-slate-500 mb-8 leading-relaxed">هنوز هیچ محصولی به سبد خرید خود اضافه نکرده‌اید.<br>پیشنهاد می‌کنیم نگاهی به محصولات جدید ما بیندازید.</p>
                <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="bg-orange-500 text-white font-bold py-3.5 px-10 rounded-xl hover:bg-orange-600 transition-all shadow-lg shadow-orange-200 inline-flex items-center gap-2">
                    <i class="fas fa-store"></i> بازگشت به فروشگاه
                </a>
            </div>

        <?php else : ?>

            <!-- نوار پیشرفت ارسال رایگان -->
            <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 mb-6 max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-center mb-3 gap-2">
                    <span class="text-slate-700 font-bold text-sm md:text-base flex items-center gap-2">
                        <?php if ( $remaining_for_free > 0 ): ?>
                            <i class="fas fa-shipping-fast text-orange-500 text-lg"></i>
                            فقط <span class="text-orange-600 font-extrabold mx-1"><?php echo wc_price($remaining_for_free); ?></span> تا ارسال رایگان!
                        <?php else: ?>
                            <i class="fas fa-check-circle text-green-500 text-lg"></i>
                            <span class="text-green-600 font-extrabold mx-1">ارسال رایگان</span> برای سفارش شما فعال شد.
                        <?php endif; ?>
                    </span>
                    <span class="text-xs text-slate-400 bg-slate-50 px-2 py-1 rounded">جمع سبد: <?php echo wc_cart_totals_subtotal_html(); ?></span>
                </div>
                <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden relative">
                    <!-- پترن پس‌زمینه -->
                    <div class="absolute inset-0 w-full h-full opacity-30" style="background-image: linear-gradient(45deg,rgba(0,0,0,.05) 25%,transparent 25%,transparent 50%,rgba(0,0,0,.05) 50%,rgba(0,0,0,.05) 75%,transparent 75%,transparent); background-size: 1rem 1rem;"></div>
                    <!-- نوار پر شونده -->
                    <div class="h-full bg-gradient-to-r <?php echo ($remaining_for_free > 0) ? 'from-orange-400 to-orange-500' : 'from-green-400 to-green-500'; ?> rounded-full transition-all duration-1000 relative shadow-sm" style="width: <?php echo $progress_percent; ?>%;">
                         <?php if ( $remaining_for_free > 0 ): ?>
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-2 h-2 bg-white rounded-full ml-1 opacity-50 animate-pulse"></div>
                         <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- فرم اصلی سبد خرید -->
            <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-7xl mx-auto">
                    
                    <!-- لیست محصولات -->
                    <div class="lg:col-span-8 space-y-4">
                         <div class="hidden md:flex justify-between items-center mb-2 px-2">
                            <h1 class="font-bold text-lg text-slate-800 flex items-center gap-2"><i class="fas fa-list-ul text-orange-500"></i> لیست سفارش</h1>
                            <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="text-xs text-slate-400 hover:text-orange-500 font-medium flex items-center gap-1 transition-colors">
                                <i class="fas fa-plus-circle"></i> افزودن کالای بیشتر
                            </a>
                        </div>

                        <?php
                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                                ?>
                                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row gap-4 relative group hover:border-orange-200 transition-colors cart-item-row" data-key="<?php echo $cart_item_key; ?>">
                                    
                                    <!-- تصویر محصول -->
                                    <div class="w-full md:w-32 h-32 bg-slate-50 rounded-xl p-2 flex items-center justify-center shrink-0 border border-slate-50">
                                        <?php
                                        $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_gallery_thumbnail', array('class' => 'max-h-full object-contain mix-blend-multiply')), $cart_item, $cart_item_key );
                                        if ( ! $product_permalink ) {
                                            echo $thumbnail;
                                        } else {
                                            printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                                        }
                                        ?>
                                    </div>
                                    
                                    <!-- محتوا -->
                                    <div class="flex-grow flex flex-col justify-between">
                                        <div>
                                            <h3 class="font-bold text-slate-800 text-sm md:text-base mb-2 leading-relaxed">
                                                <?php
                                                if ( ! $product_permalink ) {
                                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                                } else {
                                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s" class="hover:text-orange-500 transition-colors">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                                }
                                                ?>
                                            </h3>
                                            
                                            <div class="space-y-1.5 text-xs text-slate-500">
                                                <!-- نمایش متغیرها (رنگ، سایز و...) -->
                                                <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                                
                                                <!-- وضعیت موجودی -->
                                                <?php if ( $_product->is_in_stock() ) : ?>
                                                    <div class="flex items-center gap-1.5 text-green-600 bg-green-50 w-fit px-2 py-0.5 rounded-md">
                                                        <i class="fas fa-check-circle"></i> موجود در انبار
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- اکشن‌های دسکتاپ -->
                                        <div class="hidden md:flex justify-between items-end mt-4">
                                            <!-- کنترل تعداد -->
                                            <div class="flex items-center border border-slate-200 rounded-xl h-10 bg-slate-50 overflow-hidden">
                                                <button type="button" class="w-10 h-full hover:bg-orange-100 text-orange-500 transition-colors qty-btn plus"><i class="fas fa-plus text-xs"></i></button>
                                                <?php
                                                    if ( $_product->is_sold_individually() ) {
                                                        echo '<input type="text" value="1" readonly class="w-10 h-full text-center text-sm font-bold text-slate-700 outline-none bg-transparent border-x border-slate-200">';
                                                        echo '<input type="hidden" name="cart[' . $cart_item_key . '][qty]" value="1" />';
                                                    } else {
                                                        echo woocommerce_quantity_input( array(
                                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                                            'input_value'  => $cart_item['quantity'],
                                                            'max_value'    => $_product->get_max_purchase_quantity(),
                                                            'min_value'    => '0',
                                                            'product_name' => $_product->get_name(),
                                                            'classes'      => array( 'w-10', 'h-full', 'text-center', 'text-sm', 'font-bold', 'text-slate-700', 'outline-none', 'border-x', 'border-slate-200', 'bg-transparent', 'qty-input' ),
                                                        ), $_product, false );
                                                    }
                                                ?>
                                                <button type="button" class="w-10 h-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors qty-btn minus"><i class="fas fa-minus text-xs"></i></button>
                                            </div>
                                            
                                            <!-- قیمت کل آیتم -->
                                            <div class="text-lg font-black text-slate-800">
                                                <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- اکشن‌های موبایل (کنترل تعداد + قیمت) -->
                                    <div class="flex md:hidden justify-between items-center mt-3 border-t border-dashed border-slate-200 pt-3 w-full">
                                        <div class="flex items-center border border-slate-200 rounded-lg h-9 bg-slate-50">
                                            <button type="button" class="w-9 h-full text-orange-500 qty-btn plus"><i class="fas fa-plus text-[10px]"></i></button>
                                            <span class="w-8 text-center text-sm font-bold text-slate-700 mobile-qty-display"><?php echo $cart_item['quantity']; ?></span>
                                            <button type="button" class="w-9 h-full text-red-500 qty-btn minus"><i class="fas fa-minus text-[10px]"></i></button>
                                        </div>
                                        <span class="text-base font-black text-slate-800">
                                            <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                                        </span>
                                    </div>

                                    <!-- دکمه حذف -->
                                    <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="absolute top-4 left-4 w-8 h-8 flex items-center justify-center rounded-full text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all" aria-label="حذف">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </a>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        
                        <!-- دکمه مخفی بروزرسانی (توسط JS کلیک می‌شود) -->
                        <button type="submit" class="button" name="update_cart" value="Update cart" disabled>Update cart</button>
                        <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                    </div>

                    <!-- سایدبار خلاصه وضعیت -->
                    <div class="lg:col-span-4">
                        <div class="bg-white p-6 rounded-3xl shadow-lg shadow-slate-200/50 border border-slate-100 sticky top-24">
                            <h3 class="font-bold text-slate-800 text-lg mb-4 border-b border-slate-100 pb-4 flex items-center gap-2">
                                <i class="fas fa-file-invoice-dollar text-orange-500"></i> خلاصه سفارش
                            </h3>
                            
                            <div class="space-y-4 text-sm mb-6">
                                <div class="flex justify-between text-slate-500">
                                    <span>جمع کل کالاها</span>
                                    <span class="font-bold text-slate-700"><?php wc_cart_totals_subtotal_html(); ?></span>
                                </div>
                                
                                <!-- نمایش کوپن‌ها -->
                                <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                                    <div class="flex justify-between text-red-500 bg-red-50 px-2 py-1 rounded">
                                        <span>کد تخفیف: <?php echo esc_html( $code ); ?></span>
                                        <span class="font-bold"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
                                    </div>
                                <?php endforeach; ?>

                                <div class="flex justify-between text-slate-500 items-center">
                                    <span>هزینه ارسال</span>
                                    <span class="font-bold text-green-600 bg-green-50 px-2 py-1 rounded text-xs">
                                        <?php echo ($remaining_for_free <= 0) ? 'رایگان' : 'محاسبه در مرحله بعد'; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-5 rounded-2xl flex justify-between items-center mb-6 border border-orange-200/50">
                                <span class="text-slate-700 font-bold text-sm">مبلغ قابل پرداخت</span>
                                <div class="text-xl font-black text-orange-600"><?php wc_cart_totals_order_total_html(); ?></div>
                            </div>

                            <a href="<?php echo wc_get_checkout_url(); ?>" id="main-checkout-btn" class="group block w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-500/30 transition-all text-center transform active:scale-[0.98]">
                                ادامه جهت تسویه حساب <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                            </a>
                            
                            <p class="text-[10px] text-center text-slate-400 mt-4 bg-slate-50 p-3 rounded-lg border border-slate-100 leading-relaxed">
                                <i class="fas fa-info-circle ml-1"></i>
                                افزودن کالا به سبد خرید به معنی رزرو آن نیست. با توجه به محدودیت موجودی، لطفا خرید خود را سریع‌تر نهایی کنید.
                            </p>
                        </div>
                    </div>
                    
                </div>
            </form>
        <?php endif; ?>
    </main>

    <!-- فوتر چسبان موبایل -->
    <?php if ( ! WC()->cart->is_empty() ) : ?>
    <div id="mobile-sticky-footer" class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-50 lg:hidden shadow-[0_-5px_15px_rgba(0,0,0,0.05)] transition-transform duration-300 translate-y-full">
        <div class="flex justify-between items-center gap-4">
             <div class="flex flex-col">
                <span class="text-[10px] text-slate-400 mb-0.5">مبلغ نهایی سفارش:</span>
                <span class="font-black text-slate-800 text-lg"><?php wc_cart_totals_order_total_html(); ?></span>
            </div>
            <a href="<?php echo wc_get_checkout_url(); ?>" class="bg-orange-500 text-white font-bold py-3 px-6 rounded-xl hover:bg-orange-600 transition-colors shadow-lg shadow-orange-500/30 text-sm flex items-center gap-2">
                تسویه حساب <i class="fas fa-arrow-left text-xs"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- فوتر سایت -->
    <?php wp_footer(); ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Mobile Footer Visibility
        const mobileFooter = document.getElementById('mobile-sticky-footer');
        const mainBtn = document.getElementById('main-checkout-btn');
        
        function toggleFooter() {
            if(!mobileFooter || !mainBtn) return;
            const rect = mainBtn.getBoundingClientRect();
            // اگر دکمه اصلی در ویوپورت نیست
            if(rect.top < window.innerHeight && rect.bottom > 0) {
                mobileFooter.style.transform = 'translateY(100%)';
            } else {
                mobileFooter.style.transform = 'translateY(0)';
            }
        }
        
        if(mobileFooter) {
            window.addEventListener('scroll', toggleFooter);
            window.addEventListener('resize', toggleFooter);
            setTimeout(toggleFooter, 100);
        }

        // 2. Quantity Logic Fix
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('.qty-btn');
            if (!btn) return;
            
            e.preventDefault();
            const wrapper = btn.closest('.cart-item-row');
            const input = wrapper.querySelector('input.qty-input');
            const mobileDisplay = wrapper.querySelector('.mobile-qty-display');
            
            if(!input) return;
            
            let val = parseFloat(input.value) || 0;
            const step = parseFloat(input.getAttribute('step')) || 1;
            const min = parseFloat(input.getAttribute('min')) || 0;
            const max = parseFloat(input.getAttribute('max')); 
            
            if(btn.classList.contains('plus')) {
                if(isNaN(max) || val + step <= max) val += step;
            } else if(btn.classList.contains('minus')) {
                if(val - step >= min) val -= step;
            }
            
            // آپدیت مقدار ظاهری
            input.value = val;
            if(mobileDisplay) mobileDisplay.textContent = val;
            
            // تریگر کردن رویداد change برای اینکه ووکامرس بفهمد تغییر کرده
            // این خیلی مهم است
            input.dispatchEvent(new Event('change', { bubbles: true }));
            
            // پیدا کردن دکمه آپدیت
            const updateBtn = document.querySelector('button[name="update_cart"]');
            
            // فعال کردن و کلیک کردن دکمه آپدیت با تاخیر (Debounce)
            clearTimeout(window.updateTimer);
            
            // افکت بصری فوری
            document.querySelector('.woocommerce-cart-form').classList.add('processing');
            
            window.updateTimer = setTimeout(() => {
                if(updateBtn) {
                    updateBtn.removeAttribute('disabled');
                    updateBtn.click();
                }
            }, 600); 
        });
    });
    </script>

</body>
</html>