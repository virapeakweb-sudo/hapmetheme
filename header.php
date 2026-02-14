<?php
/* Path: wp-content/themes/hapomeo/header.php
توضیحات: هدر هوشمند و کامل. اگر در سبد خرید/تسویه باشید، هدر ساده را نشان می‌دهد.
در غیر این صورت، هدر کامل (شامل مگامنوها و جستجو) را نمایش می‌دهد.
اصلاحات: رفع خطای get_term_link با بررسی وجود ترم قبل از دریافت لینک.
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?></title>
    
    <!-- استایل‌های پایه و انیمیشن‌ها -->
    <style>
        body { font-family: 'IRANSansXV', sans-serif; background-color: #f8fafc; }
        
        /* استایل اسکرول‌بار */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* استایل‌های مگامنو */
        .nav-link-active { color: #fb923c; font-weight: 700; }
        .mega-menu-content { opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease; transform: translateY(10px); }
        .group:hover .mega-menu-content { opacity: 1; visibility: visible; transform: translateY(0); }
        
        /* سایدبار موبایل */
        .mobile-sidebar { transition: transform 0.3s ease-in-out; transform: translateX(-100%); }
        .mobile-sidebar.open { transform: translateX(0); }
        .sidebar-backdrop { opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .sidebar-backdrop.open { opacity: 1; visibility: visible; }
        
        /* آکاردئون موبایل */
        .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
    </style>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-slate-50 text-slate-800 flex flex-col min-h-screen'); ?>>

<?php 
// شرط: اگر در صفحه سبد خرید یا تسویه حساب هستیم
if ( is_cart() || is_checkout() ) : 
?>
    <!-- هدر مخصوص سبد خرید و تسویه (ساده) -->
    <header class="bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-40 border-b border-slate-100">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            
            <a href="<?php echo home_url('/'); ?>" class="text-xl md:text-2xl font-extrabold text-orange-500 flex items-center gap-2">
                <img src="https://hapoomeo.com/wp-content/uploads/2026/01/logo2.png" class="logoh"  alt="لوگو هاپومیو">
            </a>

            <div class="hidden md:flex items-center gap-2 text-sm font-semibold text-slate-500">
                <a href="<?php echo wc_get_cart_url(); ?>" class="<?php echo is_cart() ? 'text-orange-500' : 'hover:text-orange-500 transition-colors'; ?>">سبد خرید</a>
                <i class="fas fa-chevron-left text-xs mx-1"></i>
                <a href="<?php echo wc_get_checkout_url(); ?>" class="<?php echo is_checkout() && !is_wc_endpoint_url('order-received') ? 'text-orange-500' : 'hover:text-orange-500 transition-colors'; ?>">تسویه حساب</a>
                <i class="fas fa-chevron-left text-xs mx-1"></i>
                <span class="<?php echo is_wc_endpoint_url('order-received') ? 'text-orange-500' : 'opacity-50'; ?>">اتمام خرید</span>
            </div>

            <div class="md:hidden text-sm font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-lg">
                <?php if(is_cart()): ?>
                    سبد خرید <span class="text-orange-500 text-xs mr-1">(<?php echo WC()->cart->get_cart_contents_count(); ?> کالا)</span>
                <?php elseif(is_checkout()): ?>
                    تسویه حساب
                <?php endif; ?>
            </div>
            
        </div>
    </header>

<?php else : ?>

    <!-- هدر اصلی سایت (برای صفحه اصلی و سایر صفحات) -->
    <header class="bg-white/95 backdrop-blur-lg shadow-sm sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- لوگو -->
                <a href="<?php echo home_url('/'); ?>" class="text-2xl font-extrabold text-orange-500 flex items-center gap-2">
    
                   <img src="https://hapoomeo.com/wp-content/uploads/2026/01/logo2.png" class="logoh"  alt="لوگو هاپومیو">
                </a>

                <!-- منوی دسکتاپ (کامل) -->
                <nav class="hidden lg:flex items-center space-x-reverse space-x-8 text-slate-700 font-semibold">
                    <a href="<?php echo home_url('/'); ?>" class="nav-link hover:text-orange-500 transition-colors <?php if(is_front_page()) echo 'nav-link-active'; ?>">صفحه اصلی</a>

                    <!-- مگامنو سگ -->
                    <div class="group relative">
                        <?php 
                        // دریافت ایمن لینک دسته‌بندی سگ
                        $dog_term = get_term_by('slug', 'dog', 'product_cat');
                        $dog_link = $dog_term ? get_term_link($dog_term) : '#';
                        ?>
                        <a href="<?php echo esc_url($dog_link); ?>" class="hover:text-blue-500 transition-colors py-4 flex items-center">
                            <i class="fas fa-dog text-blue-500 ml-1 text-lg"></i>سگ
                            <i class="fas fa-chevron-down text-xs mr-1 opacity-70"></i>
                        </a>
                        <div class="mega-menu-content absolute right-1/2 translate-x-1/2 mt-4 bg-white rounded-2xl shadow-xl z-50 p-6 border w-[600px]">
                            <div class="grid grid-cols-2 gap-x-8">
                                <div class="space-y-4">
                                    <div class="space-y-3">
                                        <p class="font-bold text-slate-800 border-b pb-2 border-blue-100">غذا</p>
                                        <ul class="space-y-2 text-sm text-slate-500 pr-2">
                                            <li><a href="https://hapoomeo.com/product-category/dog/%d8%ba%d8%b0%d8%a7%db%8c-%d8%b3%da%af/%d8%ba%d8%b0%d8%a7%db%8c-%d8%ae%d8%b4%da%a9-%d8%b3%da%af/" class="hover:text-blue-500 transition-colors">غذای خشک</a></li>
                                            <li><a href="https://hapoomeo.com/product-category/dog/%d8%ba%d8%b0%d8%a7%db%8c-%d8%b3%da%af/%da%a9%d9%86%d8%b3%d8%b1%d9%88-%d9%88-%d9%be%d9%88%da%86-%d8%b3%da%af/" class="hover:text-blue-500 transition-colors">کنسرو و پوچ</a></li>
                                            <li><a href="https://hapoomeo.com/product-category/dog/%d8%ba%d8%b0%d8%a7%db%8c-%d8%b3%da%af/%d8%aa%d8%b4%d9%88%db%8c%d9%82%db%8c-%d8%b3%da%af/" class="hover:text-blue-500 transition-colors">تشویقی</a></li>
                                        </ul>
                                    </div>
                                    <div class="space-y-3 mt-4">
                                        <p class="font-bold text-slate-800 border-b pb-2 border-blue-100">لوازم</p>
                                        <ul class="space-y-2 text-sm text-slate-500 pr-2">
                                            <li><a href="https://hapoomeo.com/product-category/dog/%d9%84%d9%88%d8%a7%d8%b2%d9%85-%d9%86%da%af%d9%87%d8%af%d8%a7%d8%b1%db%8c-%d8%b3%da%af/%d9%82%d9%84%d8%a7%d8%af%d9%87-%d9%88-%d8%a8%d9%86%d8%af-%d9%82%d9%84%d8%a7%d8%af%d9%87-%d8%b3%da%af/" class="hover:text-blue-500 transition-colors">قلاده و بند</a></li>
                                            <li><a href="https://hapoomeo.com/product-category/dog/%d9%84%d9%88%d8%a7%d8%b2%d9%85-%d9%86%da%af%d9%87%d8%af%d8%a7%d8%b1%db%8c-%d8%b3%da%af/%d8%ac%d8%a7%db%8c-%d8%ae%d9%88%d8%a7%d8%a8-%d9%88-%d8%aa%d8%b4%da%a9-%d8%b3%da%af/" class="hover:text-blue-500 transition-colors">جای خواب</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bg-blue-50 rounded-xl p-5 flex flex-col justify-center items-center text-center">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <i class="fas fa-bone text-3xl text-blue-500"></i>
                                    </div>
                                    <h4 class="font-bold text-slate-800">جشنواره سگ‌ها</h4>
                                    <p class="text-sm text-slate-600 my-3">تا ۴۰٪ تخفیف روی تمامی محصولات مرتبط با سگ</p>
                                    <a href="https://hapoomeo.com/product-category/dog/" class="text-sm bg-blue-500 text-white font-bold py-2.5 px-6 rounded-full hover:bg-blue-600 shadow-md transition-all">مشاهده</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- مگامنو گربه -->
                    <div class="group relative">
                        <?php 
                        // دریافت ایمن لینک دسته‌بندی گربه
                        $cat_term = get_term_by('slug', 'cat', 'product_cat');
                        $cat_link = $cat_term ? get_term_link($cat_term) : '#';
                        ?>
                        <a href="<?php echo esc_url($cat_link); ?>" class="hover:text-orange-500 transition-colors py-4 flex items-center">
                            <i class="fas fa-cat text-orange-500 ml-1 text-lg"></i>گربه
                            <i class="fas fa-chevron-down text-xs mr-1 opacity-70"></i>
                        </a>
                        <div class="mega-menu-content absolute right-1/2 translate-x-1/2 mt-4 bg-white rounded-2xl shadow-xl z-50 p-6 border w-[600px]">
                            <div class="grid grid-cols-2 gap-x-8">
                                <div class="space-y-4">
                                    <div class="space-y-3">
                                        <p class="font-bold text-slate-800 border-b pb-2 border-orange-100">غذا</p>
                                        <ul class="space-y-2 text-sm text-slate-500 pr-2">
                                            <li><a href="https://hapoomeo.com/product-category/cat/%d8%ba%d8%b0%d8%a7%db%8c-%da%af%d8%b1%d8%a8%d9%87/%d8%ba%d8%b0%d8%a7%db%8c-%d8%ae%d8%b4%da%a9-%da%af%d8%b1%d8%a8%d9%87/" class="hover:text-orange-500 transition-colors">غذای خشک</a></li>
                                            <li><a href="https://hapoomeo.com/product-category/cat/%d8%ba%d8%b0%d8%a7%db%8c-%da%af%d8%b1%d8%a8%d9%87/%da%a9%d9%86%d8%b3%d8%b1%d9%88-%d9%88-%d9%be%d9%88%da%86-%da%af%d8%b1%d8%a8%d9%87/" class="hover:text-orange-500 transition-colors">کنسرو و پوچ</a></li>
                                        </ul>
                                    </div>
                                    <div class="space-y-3 mt-4">
                                        <p class="font-bold text-slate-800 border-b pb-2 border-orange-100">بهداشتی</p>
                                        <ul class="space-y-2 text-sm text-slate-500 pr-2">
                                            <li><a href="https://hapoomeo.com/product-category/cat/%d9%84%d9%88%d8%a7%d8%b2%d9%85-%d9%86%da%af%d9%87%d8%af%d8%a7%d8%b1%db%8c-%da%af%d8%b1%d8%a8%d9%87/%d8%ae%d8%a7%da%a9-%da%af%d8%b1%d8%a8%d9%87/" class="hover:text-orange-500 transition-colors">خاک گربه</a></li>
                                            <li><a href="https://hapoomeo.com/product-category/cat/%d8%a2%d8%b1%d8%a7%db%8c%d8%b4%db%8c-%d9%88-%d8%a8%d9%87%d8%af%d8%a7%d8%b4%d8%aa%db%8c-%da%af%d8%b1%d8%a8%d9%87/%d8%b4%d8%a7%d9%85%d9%be%d9%88-%d9%88-%d9%84%d9%88%d8%a7%d8%b2%d9%85-%d8%ad%d9%85%d8%a7%d9%85-%da%af%d8%b1%d8%a8%d9%87/" class="hover:text-orange-500 transition-colors">شامپو</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bg-orange-50 rounded-xl p-5 flex flex-col justify-center items-center text-center">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <i class="fas fa-fish text-3xl text-orange-500"></i>
                                    </div>
                                    <h4 class="font-bold text-slate-800">دنیای گربه‌ها</h4>
                                    <p class="text-sm text-slate-600 my-3">بهترین برندهای غذای گربه با قیمت استثنایی</p>
                                    <a href="https://hapoomeo.com/product-category/cat/" class="text-sm bg-orange-500 text-white font-bold py-2.5 px-6 rounded-full hover:bg-orange-600 shadow-md transition-all">خرید کنید</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="<?php echo wc_get_page_permalink('shop'); ?>" class="hover:text-orange-500 transition-colors">فروشگاه</a>
                    <a href="https://hapoomeo.com/contact-us/" class="hover:text-orange-500 transition-colors">تماس با ما</a>
                </nav>

                <!-- آیکون‌ها و دکمه موبایل -->
                <div class="flex items-center space-x-reverse space-x-4">
                    <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="hidden md:flex text-xl text-slate-600 hover:text-orange-500 transition-colors"><i class="fas fa-user-circle"></i></a>
                    <a href="<?php echo wc_get_cart_url(); ?>" class="text-xl text-slate-600 hover:text-orange-500 transition-colors relative">
                        <i class="fas fa-shopping-bag"></i>
                        <?php if ( WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) : ?>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white">
                                <?php echo WC()->cart->get_cart_contents_count(); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <button id="mobile-menu-btn" class="lg:hidden text-2xl text-slate-700 p-2 hover:bg-slate-100 rounded-lg transition-colors"><i class="fas fa-bars"></i></button>
                </div>
            </div>
        </div>
    </header>

    <!-- سایدبار موبایل (فقط برای هدر اصلی) -->
    <div id="sidebar-backdrop" class="sidebar-backdrop fixed inset-0 bg-black/40 z-50 backdrop-blur-sm lg:hidden"></div>
    <div id="mobile-sidebar" class="mobile-sidebar fixed inset-y-0 left-0 w-72 bg-white shadow-2xl z-[60] overflow-y-auto lg:hidden">
        <div class="p-4 border-b flex justify-between items-center bg-orange-50">
            <span class="font-extrabold text-orange-500 text-lg flex items-center gap-2"><i class="fas fa-paw"></i> هاپومیو</span>
            <button id="close-sidebar" class="text-slate-500 hover:text-red-500 w-8 h-8 flex items-center justify-center rounded-full hover:bg-white transition-all"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-4 space-y-1">
            <a href="<?php echo home_url('/'); ?>" class="block py-3 px-4 rounded-xl bg-orange-50 text-orange-600 font-bold">صفحه اصلی</a>

            <div class="border-b border-slate-100 py-2">
                <button class="accordion-toggle flex justify-between items-center w-full py-3 px-4 text-slate-700 font-semibold hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="flex items-center gap-2"><i class="fas fa-dog text-blue-500"></i> سگ</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>
                <div class="accordion-content pl-10 text-sm text-slate-600">
                    <ul class="space-y-2 py-2 border-r-2 border-slate-100 pr-3 my-1">
                        <li><a href="https://hapoomeo.com/product-category/dog/%d8%ba%d8%b0%d8%a7%db%8c-%d8%b3%da%af/%d8%ba%d8%b0%d8%a7%db%8c-%d8%ae%d8%b4%da%a9-%d8%b3%da%af/" class="block py-1 hover:text-blue-500">غذای خشک</a></li>
                        <li><a href="https://hapoomeo.com/product-category/dog/%d8%ba%d8%b0%d8%a7%db%8c-%d8%b3%da%af/%da%a9%d9%86%d8%b3%d8%b1%d9%88-%d9%88-%d9%be%d9%88%da%86-%d8%b3%da%af/" class="block py-1 hover:text-blue-500">کنسرو و پوچ</a></li>
                        <li><a href="https://hapoomeo.com/product-category/dog/%d8%ba%d8%b0%d8%a7%db%8c-%d8%b3%da%af/%d8%aa%d8%b4%d9%88%db%8c%d9%82%db%8c-%d8%b3%da%af/" class="block py-1 hover:text-blue-500">لوازم جانبی</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-b border-slate-100 py-2">
                <button class="accordion-toggle flex justify-between items-center w-full py-3 px-4 text-slate-700 font-semibold hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="flex items-center gap-2"><i class="fas fa-cat text-orange-500"></i> گربه</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>
                <div class="accordion-content pl-10 text-sm text-slate-600">
                    <ul class="space-y-2 py-2 border-r-2 border-slate-100 pr-3 my-1">
                        <li><a href="https://hapoomeo.com/product-category/cat/%d8%ba%d8%b0%d8%a7%db%8c-%da%af%d8%b1%d8%a8%d9%87/%d8%ba%d8%b0%d8%a7%db%8c-%d8%ae%d8%b4%da%a9-%da%af%d8%b1%d8%a8%d9%87/" class="block py-1 hover:text-orange-500">غذای خشک</a></li>
                        <li><a href="https://hapoomeo.com/product-category/cat/%d8%ba%d8%b0%d8%a7%db%8c-%da%af%d8%b1%d8%a8%d9%87/%da%a9%d9%86%d8%b3%d8%b1%d9%88-%d9%88-%d9%be%d9%88%da%86-%da%af%d8%b1%d8%a8%d9%87/" class="block py-1 hover:text-orange-500">کنسرو و سوپ</a></li>
                        <li><a href="https://hapoomeo.com/product-category/cat/%d9%84%d9%88%d8%a7%d8%b2%d9%85-%d9%86%da%af%d9%87%d8%af%d8%a7%d8%b1%db%8c-%da%af%d8%b1%d8%a8%d9%87/%d8%ae%d8%a7%da%a9-%da%af%d8%b1%d8%a8%d9%87/" class="block py-1 hover:text-orange-500">خاک گربه</a></li>
                    </ul>
                </div>
            </div>

            <a href="https://hapoomeo.com/blog/" class="block py-3 px-4 text-slate-700 font-semibold hover:bg-slate-50 rounded-xl transition-colors">مقالات آموزشی</a>
            <a href="https://hapoomeo.com/contact-us/" class="block py-3 px-4 text-slate-700 font-semibold hover:bg-slate-50 rounded-xl transition-colors">تماس با ما</a>
        </div>
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-slate-50">
            <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="flex justify-center items-center gap-2 w-full py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold hover:shadow-md transition-all">
                <i class="fas fa-user"></i> ورود / ثبت نام
            </a>
        </div>
    </div>

<?php endif; ?>