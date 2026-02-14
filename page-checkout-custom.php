<?php
/**
 * Template Name: تسویه حساب اختصاصی (Custom Checkout)
 * Version: 1.6.0
 * Path: wp-content/themes/your-theme/page-checkout-custom.php
 * Author: Gemini AI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// اطمینان از اینکه ووکامرس نصب است
if ( ! class_exists( 'WooCommerce' ) ) {
    wp_die( 'لطفا افزونه ووکامرس را نصب کنید.' );
}

// اگر سبد خرید خالی است، رایرکت شود
if ( WC()->cart->is_empty() && ! is_wc_endpoint_url( 'order-received' ) && ! is_wc_endpoint_url( 'order-pay' ) ) {
    wp_safe_redirect( wc_get_cart_url() );
    exit;
}

// تنظیمات سقف ارسال رایگان (به تومان)
$free_shipping_threshold = 3000000;
$cart_total = WC()->cart->get_subtotal();
$remaining_for_free = $free_shipping_threshold - $cart_total;
$progress_percent = ($cart_total / $free_shipping_threshold) * 100;
if ($progress_percent > 100) $progress_percent = 100;

// دریافت اطلاعات کاربر لاگین شده و فیلدها
$current_user = wp_get_current_user();
$checkout = WC()->checkout();
$billing_fields = $checkout->get_checkout_fields('billing');

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    
    <!-- استایل‌های اختصاصی -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Vazirmatn', sans-serif; background-color: #f8fafc; }
        
        /* اسکرول بار زیبا */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* استایل‌های رادیو باتن */
        .radio-selected { border-color: #f97316; background-color: #fff7ed; }
        .radio-circle-inner { transform: scale(0); transition: transform 0.2s ease-in-out; }
        input:checked + div .radio-circle-inner { transform: scale(1); }
        
        /* باکس خطای ووکامرس */
        .woocommerce-notices-wrapper { width: 100%; margin-bottom: 1.5rem; }
        .woocommerce-error {
            background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b;
            padding: 1rem 1.5rem; border-radius: 1rem; list-style: none; margin: 0; width: 100%;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1); display: flex; flex-direction: column; gap: 0.5rem;
        }
        .woocommerce-error li { display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.9rem; }
        .woocommerce-error li::before { content: '\f06a'; font-family: "Font Awesome 6 Free"; font-weight: 900; }
        
        /* -----------------------------------------------------------
           استایل‌های سفارشی و مدرن برای Dropdown (Select2)
        ----------------------------------------------------------- */
        /* فیلد انتخاب بسته */
        .select2-container .select2-selection--single {
            height: 52px !important;
            border-radius: 0.75rem !important; /* rounded-xl */
            border: 1px solid #e2e8f0 !important;
            background-color: #f8fafc !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s;
        }
        .select2-container--default .select2-selection--single:hover {
            border-color: #cbd5e1 !important;
        }
        .select2-container--open .select2-selection--single {
            border-color: #f97316 !important; /* orange-500 */
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1) !important;
        }
        
        /* متن داخل فیلد */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 52px !important;
            padding-right: 16px !important;
            color: #334155 !important;
            font-size: 0.875rem !important;
            font-weight: 500;
            text-align: right !important;
        }
        
        /* فلش کناری */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            left: 12px !important;
            right: auto !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #94a3b8 transparent transparent transparent !important;
            border-width: 6px 5px 0 5px !important;
        }
        .select2-container--open .select2-selection__arrow b {
            border-color: transparent transparent #f97316 transparent !important;
            border-width: 0 5px 6px 5px !important;
        }

        /* منوی باز شونده (Dropdown) */
        .select2-dropdown {
            border: 0 !important;
            border-radius: 1rem !important;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            z-index: 9999 !important;
            margin-top: 8px !important;
            background: #ffffff !important;
            animation: fadeInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; /* انیمیشن نرم */
            padding: 8px !important;
        }

        /* آیتم‌های لیست */
        .select2-results__option {
            padding: 10px 16px !important;
            font-size: 0.875rem !important;
            color: #475569 !important;
            border-radius: 0.5rem !important;
            margin-bottom: 2px !important;
            transition: all 0.1s;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #fff7ed !important; /* orange-50 */
            color: #f97316 !important; /* orange-500 */
            font-weight: bold;
        }
        .select2-results__option[aria-selected="true"] {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        /* باکس جستجو داخل دراپ‌داون */
        .select2-search--dropdown {
            padding: 0 0 8px 0 !important;
        }
        .select2-search__field {
            border-radius: 0.5rem !important;
            border: 1px solid #e2e8f0 !important;
            padding: 10px !important;
            outline: none !important;
            font-family: 'Vazirmatn', sans-serif !important;
            font-size: 0.85rem !important;
        }
        .select2-search__field:focus {
            border-color: #f97316 !important;
        }

        /* انیمیشن باز شدن */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .woocommerce-input-wrapper { width: 100%; }
        /* مخفی کردن لودینگ دیفالت */
        .blockUI.blockOverlay { z-index: 1000 !important; opacity: 0.6 !important; background: #fff !important; }
    </style>

    <?php wp_head(); ?>
</head>
<body class="bg-slate-50 text-slate-800 <?php body_class(); ?>">

    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-40 border-b border-slate-100">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="<?php echo home_url(); ?>" class="text-xl md:text-2xl font-extrabold text-orange-500 flex items-center gap-2">
                 <img src="https://hapoomeo.com/wp-content/uploads/2026/01/logo2.png" class="logoh"  alt="لوگو هاپومیو">
            </a>
            <div class="hidden md:flex items-center gap-2 text-sm font-semibold text-slate-500">
                <a href="<?php echo wc_get_cart_url(); ?>" class="hover:text-orange-500 transition-colors">سبد خرید</a>
                <i class="fas fa-chevron-left text-xs mx-1"></i>
                <span class="text-orange-500">تسویه حساب</span>
                <i class="fas fa-chevron-left text-xs mx-1"></i>
                <span class="opacity-50">اتمام خرید</span>
            </div>
            <div class="md:hidden text-sm font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-lg">تسویه حساب</div>
        </div>
    </header>

    <main class="container mx-auto p-4 md:p-6 pb-24 md:pb-6">
        
        <!-- نمایش پیام‌های خطا و اطلاعیه‌ها (بالای صفحه) -->
        <div class="max-w-7xl mx-auto woocommerce-notices-wrapper">
            <?php wc_print_notices(); ?>
        </div>

        <!-- نوار پیشرفت ارسال رایگان -->
        <?php if ( $remaining_for_free > 0 ): ?>
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 mb-8 max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center mb-3 gap-2">
                <span class="text-slate-700 font-bold text-sm md:text-base flex items-center gap-2">
                    <i class="fas fa-truck-fast text-orange-500 text-lg"></i>
                    فقط <span class="text-orange-600 font-extrabold mx-1"><?php echo wc_price($remaining_for_free); ?></span> دیگر تا ارسال رایگان!
                </span>
                <span class="text-xs text-slate-400">سقف ارسال رایگان: <?php echo wc_price($free_shipping_threshold); ?></span>
            </div>
            <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden relative">
                <div class="absolute inset-0 w-full h-full opacity-30" style="background-image: linear-gradient(45deg,rgba(0,0,0,.1) 25%,transparent 25%,transparent 50%,rgba(0,0,0,.1) 50%,rgba(0,0,0,.1) 75%,transparent 75%,transparent); background-size: 1rem 1rem;"></div>
                <div class="h-full bg-gradient-to-l from-orange-400 to-orange-500 rounded-full transition-all duration-1000" style="width: <?php echo $progress_percent; ?>%;"></div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-green-50 rounded-2xl p-4 mb-8 max-w-5xl mx-auto text-green-700 font-bold text-center border border-green-100">
            <i class="fas fa-check-circle ml-2"></i> هزینه ارسال برای شما رایگان شد!
        </div>
        <?php endif; ?>

        <!-- شروع فرم تسویه حساب -->
        <form name="checkout" method="post" class="checkout woocommerce-checkout grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-7xl mx-auto" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
            
            <!-- ستون سمت راست (فرم‌ها) -->
            <div class="lg:col-span-8 space-y-6 order-1">
                
                <!-- STEP 1: اطلاعات فردی -->
                <div class="bg-white p-5 md:p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                        <span class="bg-orange-100 text-orange-600 w-8 h-8 flex items-center justify-center rounded-lg text-sm">1</span>
                        اطلاعات گیرنده
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <?php 
                            woocommerce_form_field( 'billing_first_name', array(
                                'type' => 'text',
                                'class' => array('form-row-wide'),
                                'label' => 'نام',
                                'required' => true,
                                'placeholder' => 'مثلا: علی',
                                'input_class' => array('w-full', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-xl', 'px-4', 'py-3', 'outline-none', 'focus:border-orange-500', 'transition-all', 'text-sm')
                            ), $checkout->get_value( 'billing_first_name' ) ); 
                            ?>
                        </div>
                        <div class="space-y-2">
                            <?php 
                            woocommerce_form_field( 'billing_last_name', array(
                                'type' => 'text',
                                'class' => array('form-row-wide'),
                                'label' => 'نام خانوادگی',
                                'required' => true, 
                                'placeholder' => 'مثلا: علوی',
                                'input_class' => array('w-full', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-xl', 'px-4', 'py-3', 'outline-none', 'focus:border-orange-500', 'transition-all', 'text-sm')
                            ), $checkout->get_value( 'billing_last_name' ) ); 
                            ?>
                        </div>
                        <div class="space-y-2">
                            <?php 
                            woocommerce_form_field( 'billing_phone', array(
                                'type' => 'tel',
                                'class' => array('form-row-wide'),
                                'label' => 'شماره موبایل',
                                'required' => true,
                                'placeholder' => '0912...',
                                'input_class' => array('w-full', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-xl', 'px-4', 'py-3', 'outline-none', 'focus:border-orange-500', 'transition-all', 'text-sm', 'text-left'),
                                'custom_attributes' => array('dir' => 'ltr')
                            ), $checkout->get_value( 'billing_phone' ) ); 
                            ?>
                        </div>
                         <div class="space-y-2">
                            <?php 
                            woocommerce_form_field( 'billing_email', array(
                                'type' => 'email',
                                'class' => array('form-row-wide'),
                                'label' => 'ایمیل (اختیاری)',
                                'required' => false,
                                'placeholder' => 'name@example.com',
                                'input_class' => array('w-full', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-xl', 'px-4', 'py-3', 'outline-none', 'focus:border-orange-500', 'transition-all', 'text-sm', 'text-left'),
                                'custom_attributes' => array('dir' => 'ltr')
                            ), $checkout->get_value( 'billing_email' ) ); 
                            ?>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: آدرس -->
                <div class="bg-white p-5 md:p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                        <span class="bg-blue-100 text-blue-600 w-8 h-8 flex items-center justify-center rounded-lg text-sm">2</span>
                        آدرس تحویل سفارش
                    </h2>
                    
                    <!-- فیلد مخفی کشور -->
                    <div style="display:none !important;">
                        <?php 
                        woocommerce_form_field( 'billing_country', array(
                            'type' => 'country',
                            'class' => array('form-row-wide', 'hidden'),
                            'label' => 'کشور',
                            'required' => true,
                        ), $checkout->get_value( 'billing_country' ) ? $checkout->get_value( 'billing_country' ) : 'IR' ); 
                        ?>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-2">
                            <!-- State (Dynamic) -->
                             <?php 
                                $state_args = isset($billing_fields['billing_state']) ? $billing_fields['billing_state'] : array();
                                $state_args['class'] = array('form-row-wide', 'address-field'); 
                                $state_args['input_class'] = array('w-full', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-xl', 'px-4', 'py-3', 'outline-none', 'focus:border-orange-500', 'transition-all', 'text-sm');
                                
                                woocommerce_form_field( 'billing_state', $state_args, $checkout->get_value( 'billing_state' ) ); 
                            ?>
                        </div>
                        <div class="space-y-2">
                            <!-- City (Dynamic) -->
                             <?php 
                                $city_args = isset($billing_fields['billing_city']) ? $billing_fields['billing_city'] : array();
                                $city_args['class'] = array('form-row-wide', 'address-field');
                                $city_args['input_class'] = array('w-full', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-xl', 'px-4', 'py-3', 'outline-none', 'focus:border-orange-500', 'transition-all', 'text-sm');
                                
                                woocommerce_form_field( 'billing_city', $city_args, $checkout->get_value( 'billing_city' ) ); 
                            ?>
                        </div>
                    </div>
                    
                    <div class="space-y-2 mb-6">
                         <?php 
                            woocommerce_form_field( 'billing_address_1', array(
                                'type' => 'textarea',
                                'class' => array('form-row-wide'),
                                'label' => 'آدرس پستی دقیق',
                                'required' => true,
                                'placeholder' => 'خیابان اصلی، کوچه، پلاک، واحد...',
                                'input_class' => array('w-full', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-xl', 'px-4', 'py-3', 'outline-none', 'focus:border-orange-500', 'transition-all', 'text-sm', 'resize-none'),
                                'custom_attributes' => array('rows' => 3)
                            ), $checkout->get_value( 'billing_address_1' ) ); 
                        ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div class="space-y-2">
                            <?php 
                            woocommerce_form_field( 'billing_postcode', array(
                                'type' => 'text',
                                'class' => array('form-row-wide'),
                                'label' => 'کد پستی',
                                'required' => false,
                                'input_class' => array('w-full', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-xl', 'px-4', 'py-3', 'outline-none', 'focus:border-orange-500', 'transition-all', 'text-sm', 'text-left'),
                                'custom_attributes' => array('dir' => 'ltr')
                            ), $checkout->get_value( 'billing_postcode' ) ); 
                            ?>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: روش ارسال -->
                <div class="bg-white p-5 md:p-8 rounded-2xl shadow-sm border border-slate-100" id="shipping_method_wrapper">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                        <span class="bg-green-100 text-green-600 w-8 h-8 flex items-center justify-center rounded-lg text-sm">3</span>
                        شیوه ارسال
                    </h2>
                    
                    <div class="space-y-4 woocommerce-shipping-methods">
                        <?php
                        if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) :
                            $packages = WC()->shipping->get_packages();
                            foreach ( $packages as $i => $package ) {
                                $available_methods = $package['rates'];
                                if( $available_methods ) {
                                    foreach( $available_methods as $method ) {
                                        ?>
                                        <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-slate-50 transition-all group">
                                            <input type="radio" 
                                                   name="shipping_method[<?php echo $i; ?>]" 
                                                   data-index="<?php echo $i; ?>" 
                                                   id="shipping_method_<?php echo $i; ?>_<?php echo sanitize_title( $method->id ); ?>" 
                                                   value="<?php echo esc_attr( $method->id ); ?>" 
                                                   class="peer sr-only shipping_method" 
                                                   <?php checked( $method->id, $package['chosen_method'], true ); ?> 
                                            />
                                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-orange-500 rounded-xl pointer-events-none"></div>
                                            
                                            <div class="flex items-center gap-4 w-full">
                                                <div class="w-5 h-5 border-2 border-slate-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                                                    <div class="w-2.5 h-2.5 bg-orange-500 rounded-full radio-circle-inner"></div>
                                                </div>
                                                <div class="flex-grow">
                                                    <div class="font-bold text-slate-700"><?php echo $method->label; ?></div>
                                                </div>
                                                <div class="text-sm font-bold text-slate-800">
                                                    <?php echo wc_price( $method->cost ); ?>
                                                </div>
                                            </div>
                                        </label>
                                        <?php
                                    }
                                } else {
                                    echo '<p class="text-sm text-red-500">هیچ روش ارسالی برای آدرس شما یافت نشد.</p>';
                                }
                            }
                        endif;
                        ?>
                    </div>
                </div>

            </div>

            <!-- ستون سمت چپ (سایدبار: لیست کالاها و جمع کل) -->
            <div class="lg:col-span-4 order-2">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 sticky top-24">
                    
                    <!-- عنوان بخش کالاها -->
                    <h3 class="font-bold text-slate-800 mb-4 text-lg border-b border-slate-100 pb-3 flex justify-between items-center">
                        سبد خرید
                        <span class="text-xs font-medium text-slate-400 bg-slate-50 px-2 py-1 rounded-md"><?php echo WC()->cart->get_cart_contents_count(); ?> کالا</span>
                    </h3>
                    
                    <!-- لیست کالاها (نمایش در سمت چپ) -->
                    <div class="space-y-4 mb-6 max-h-80 overflow-y-auto pr-2 custom-scroll">
                        <?php
                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

                            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                $thumbnail = $_product->get_image(array(100, 100), array('class' => 'w-full h-full object-cover rounded'));
                                ?>
                                <div class="flex gap-3 items-center group">
                                    <div class="w-16 h-16 bg-slate-50 rounded-lg p-1 border border-slate-100 shrink-0 group-hover:border-orange-200 transition-colors">
                                        <?php echo $thumbnail; ?>
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="text-xs font-bold text-slate-700 line-clamp-2 leading-relaxed"><?php echo $_product->get_name(); ?></h4>
                                        <div class="flex justify-between items-end mt-1">
                                            <span class="text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded"><?php echo $cart_item['quantity']; ?> عدد</span>
                                            <span class="text-sm font-bold text-slate-800"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <!-- بخش اعمال کد تخفیف -->
                    <div class="mb-6 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <label class="text-xs font-bold text-slate-500 mb-2 block">کد تخفیف دارید؟</label>
                        <div class="flex relative">
                            <input type="text" id="custom_coupon_code" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-orange-500 transition-all pr-12" placeholder="کد را وارد کنید">
                            <button type="button" id="apply_custom_coupon" class="absolute left-1 top-1 bottom-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-3 rounded-md transition-colors">اعمال</button>
                        </div>
                        <div id="coupon-message" class="text-xs mt-2"></div>
                    </div>

                    <hr class="border-slate-100 mb-4">

                    <!-- جمع کل -->
                    <div class="space-y-3 text-sm mb-6 shop_table">
                        <div class="flex justify-between text-slate-500">
                            <span>قیمت کالاها</span>
                            <span class="font-medium"><?php wc_cart_totals_subtotal_html(); ?></span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>هزینه ارسال</span>
                            <span class="font-medium"><?php wc_cart_totals_shipping_html(); ?></span>
                        </div>
                         <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                            <div class="flex justify-between text-green-600 cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                                <span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
                                <span class="font-medium"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="flex justify-between text-slate-800 font-bold order-total pt-3 border-t border-slate-100">
                            <span>مبلغ قابل پرداخت</span>
                            <span class="font-black text-lg text-orange-600"><?php wc_cart_totals_order_total_html(); ?></span>
                        </div>
                    </div>

                    <!-- درگاه پرداخت -->
                    <div class="mb-6 bg-slate-50 p-3 rounded-xl">
                        <h4 class="text-sm font-bold mb-3">روش پرداخت</h4>
                        <div id="payment" class="woocommerce-checkout-payment">
                            <?php woocommerce_checkout_payment(); ?>
                        </div>
                    </div>

                    <!-- دکمه پرداخت -->
                    <?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="w-full bg-orange-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-200 hover:bg-orange-600 hover:shadow-xl transition-all flex items-center justify-center gap-2 transform active:scale-95" name="woocommerce_checkout_place_order" id="place_order" value="پرداخت و ثبت نهایی" data-value="پرداخت و ثبت نهایی"><span>پرداخت و ثبت نهایی</span><i class="fas fa-arrow-left"></i></button>' ); ?>
                    
                    <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

                    <p class="text-[10px] text-center text-slate-400 mt-4 leading-relaxed">
                        با ثبت سفارش، <a href="#" class="underline hover:text-orange-500">قوانین و مقررات</a> سایت را می‌پذیرم.
                    </p>
                </div>
            </div>
        </form>
    </main>

    <!-- دکمه چسبان موبایل -->
    <div id="mobile-sticky-btn" class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-50 lg:hidden shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] transition-all duration-300">
        <div class="flex justify-between items-center mb-2">
            <span class="text-xs text-slate-500">مبلغ قابل پرداخت:</span>
            <span class="font-bold text-slate-800"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>
        <button onclick="document.getElementById('place_order').click();" class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-colors">
            پرداخت نهایی
        </button>
    </div>

    <!-- اسکریپت‌ها -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // منطق دکمه چسبان
            const mobileBtn = document.getElementById('mobile-sticky-btn');
            const mainBtn = document.getElementById('place_order');

            function toggleStickyBtn() {
                if (!mobileBtn || !mainBtn || window.innerWidth >= 1024) return;
                const mainBtnRect = mainBtn.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                
                if (mainBtnRect.top < windowHeight + 50) {
                    mobileBtn.classList.add('opacity-0', 'translate-y-full', 'pointer-events-none');
                } else {
                    mobileBtn.classList.remove('opacity-0', 'translate-y-full', 'pointer-events-none');
                }
            }
            window.addEventListener('scroll', toggleStickyBtn);
            window.addEventListener('resize', toggleStickyBtn);
            toggleStickyBtn();

            // AJAX کوپن
            jQuery(document.body).on('click', '#apply_custom_coupon', function(e) {
                e.preventDefault();
                var $btn = jQuery(this);
                var code = jQuery('#custom_coupon_code').val();
                
                if(!code) return;
                
                $btn.text('...');
                var data = {
                    security: wc_checkout_params.apply_coupon_nonce,
                    coupon_code: code
                };

                jQuery.ajax({
                    type: 'POST',
                    url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
                    data: data,
                    success: function(response) {
                        $btn.text('اعمال');
                        jQuery('.woocommerce-error, .woocommerce-message').remove();
                        jQuery(document.body).trigger('update_checkout');

                        if ( response && response.indexOf('woocommerce-error') !== -1 ) {
                            jQuery('#coupon-message').html('<span class="text-red-500">کد تخفیف نامعتبر است.</span>');
                        } else {
                            jQuery('#custom_coupon_code').val('');
                            jQuery('#coupon-message').html('<span class="text-green-500 font-bold">کد تخفیف با موفقیت اعمال شد!</span>');
                        }
                    },
                    error: function() {
                        $btn.text('اعمال');
                        jQuery('#coupon-message').html('<span class="text-red-500">خطا در برقراری ارتباط.</span>');
                    }
                });
            });
        });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>