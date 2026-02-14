<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.5.0
 * * Edited for: Hapoomio Custom Design
 * Path: /woocommerce/checkout/form-checkout.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// اضافه کردن استایل‌های ضروری برای هماهنگی با طرح شما
// اگر Tailwind را در هدر قالب لود کرده‌اید، نیازی به اسکریپت زیر نیست
// اما برای اطمینان اینجا قرار می‌دهیم (بهتر است به header.php منتقل شود)
?>
<!-- Load Tailwind & Fonts (Temporary: Move to header.php recommended) -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Vazirmatn', sans-serif; }
    /* مخفی کردن المان‌های پیش‌فرض ووکامرس که با طرح ما تداخل دارند */
    .woocommerce-billing-fields__field-wrapper { display: grid; grid-template-columns: repeat(1, 1fr); gap: 1rem; }
    @media (min-width: 768px) {
        .woocommerce-billing-fields__field-wrapper { grid-template-columns: repeat(2, 1fr); }
        /* برخی فیلدها مثل آدرس باید تمام عرض باشند */
        #billing_address_1_field, #billing_address_2_field, #billing_email_field { grid-column: span 2; }
    }
    .woocommerce-input-wrapper { width: 100%; }
    input.input-text, select, textarea {
        width: 100%;
        background-color: #f8fafc; /* bg-slate-50 */
        border: 1px solid #e2e8f0; /* border-slate-200 */
        border-radius: 0.75rem; /* rounded-xl */
        padding: 0.75rem 1rem; /* px-4 py-3 */
        outline: none;
        transition: all 0.2s;
    }
    input.input-text:focus, select:focus, textarea:focus {
        border-color: #f97316; /* orange-500 */
        box-shadow: 0 0 0 1px #f97316;
    }
    /* مخفی کردن عناوین پیش‌فرض ووکامرس چون ما عنوان‌های خودمان را داریم */
    .woocommerce-billing-fields > h3, .woocommerce-additional-fields > h3, #ship-to-different-address {
        display: none !important;
    }
    /* استایل دکمه پرداخت ووکامرس */
    #place_order {
        width: 100%;
        background-color: #f97316;
        color: white;
        font-weight: bold;
        padding: 1rem;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    #place_order:hover { background-color: #ea580c; }
    
    /* استایل جدول بازبینی سفارش */
    .woocommerce-checkout-review-order-table th, .woocommerce-checkout-review-order-table td {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .woocommerce-checkout-review-order-table th { text-align: right; color: #64748b; font-weight: normal; }
    .woocommerce-checkout-review-order-table td { text-align: left; font-weight: bold; color: #1e293b; }
</style>

<div class="bg-slate-50 text-slate-800 min-h-screen" dir="rtl">
    
    <!-- Hooks before checkout (Coupon form, login form usually appear here) -->
    <div class="container mx-auto px-4 py-4">
        <?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
    </div>

    <!-- Main Container -->
    <main class="container mx-auto p-4 md:p-6 pb-24 md:pb-6">

        <!-- If checkout is empty -->
        <?php
        if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
            echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
            return;
        }
        ?>

        <!-- Progress Bar / Free Shipping Banner (Hardcoded from your HTML) -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 mb-8 max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center mb-3 gap-2">
                <span class="text-slate-700 font-bold text-sm md:text-base flex items-center gap-2">
                    <i class="fas fa-truck-fast text-orange-500 text-lg"></i>
                    وضعیت <span class="text-orange-600 font-extrabold mx-1">سفارش</span> شما
                </span>
                <!-- این قسمت در ووکامرس نیاز به کد PHP پیچیده دارد، فعلاً استاتیک نمایش می‌دهیم -->
                <span class="text-xs text-slate-400">تکمیل اطلاعات جهت ارسال</span>
            </div>
            <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden relative">
                <div class="absolute inset-0 w-full h-full opacity-30" style="background-image: linear-gradient(45deg,rgba(0,0,0,.1) 25%,transparent 25%,transparent 50%,rgba(0,0,0,.1) 50%,rgba(0,0,0,.1) 75%,transparent 75%,transparent); background-size: 1rem 1rem;"></div>
                <div class="h-full bg-gradient-to-l from-orange-400 to-orange-500 rounded-full transition-all duration-1000" style="width: 50%;"></div>
            </div>
        </div>

        <!-- Checkout Form -->
        <form name="checkout" method="post" class="checkout woocommerce-checkout grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-7xl mx-auto" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

            <!-- LEFT COLUMN (Billing & Shipping Fields) -->
            <div class="lg:col-span-8 space-y-6">

                <?php if ( $checkout->get_checkout_fields() ) : ?>

                    <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                    <!-- Step 1: Billing & Address Details -->
                    <div class="bg-white p-5 md:p-8 rounded-2xl shadow-sm border border-slate-100" id="customer_details">
                        <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                            <span class="bg-orange-100 text-orange-600 w-8 h-8 flex items-center justify-center rounded-lg text-sm">1</span>
                            اطلاعات صورتحساب و گیرنده
                        </h2>
                        
                        <!-- Billing Fields Hook -->
                        <div class="space-y-4">
                            <?php do_action( 'woocommerce_checkout_billing' ); ?>
                        </div>
                    </div>

                    <!-- Step 2: Shipping Fields (If 'Ship to different address' is checked or forced) -->
                    <div class="bg-white p-5 md:p-8 rounded-2xl shadow-sm border border-slate-100 mt-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                            <span class="bg-blue-100 text-blue-600 w-8 h-8 flex items-center justify-center rounded-lg text-sm">2</span>
                            توضیحات و ارسال به آدرس دیگر
                        </h2>
                        
                        <!-- Shipping Fields Hook -->
                         <div class="space-y-4">
                            <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                        </div>
                    </div>

                    <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                <?php endif; ?>

                <!-- Step 3: Shipping Method (Note: In standard WC, Shipping Methods are usually inside the Order Review table on the right. 
                     Moving them here requires complex AJAX handling. For this template, we will rely on the standard "Order Review" section 
                     to handle shipping selection to ensure functionality isn't broken.) -->
                <div class="bg-white p-5 md:p-8 rounded-2xl shadow-sm border border-slate-100">
                    <div class="text-sm text-slate-500">
                        <i class="fas fa-info-circle ml-1"></i>
                        انتخاب شیوه ارسال و پرداخت در کادر روبرو (فاکتور نهایی) انجام می‌شود.
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (Order Review & Payment) -->
            <div class="lg:col-span-4">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 sticky top-24">
                    <h3 class="font-bold text-slate-800 mb-6 text-lg">فاکتور نهایی</h3>
                    
                    <!-- Order Review Hook (Outputs Products Table, Shipping Methods, Total, Payment Gateways) -->
                    <div id="order_review" class="woocommerce-checkout-review-order space-y-4 mb-6 custom-scroll">
                        <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                    </div>

                    <p class="text-[10px] text-center text-slate-400 mt-4 leading-relaxed">
                        با ثبت سفارش، <a href="#" class="underline hover:text-orange-500">قوانین و مقررات</a> سایت را می‌پذیرم.
                    </p>
                </div>
            </div>

        </form>

        <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
    </main>

    <!-- Mobile Sticky Button (Optional JS functionality) -->
    <div id="mobile-sticky-btn" class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-50 lg:hidden shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] transition-all duration-300 transform translate-y-0 opacity-100">
        <button onclick="document.getElementById('place_order').click();" class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-colors">
            تکمیل خرید (مشاهده فاکتور)
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileBtn = document.getElementById('mobile-sticky-btn');
            const mainBtn = document.getElementById('place_order'); // Changed to WC default ID

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
        });
    </script>
</div>