<?php
/**
 * My Account Page
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 * * Edited for: Hapoomio Custom Design
 * Path: /woocommerce/my-account/my-account.php
 */

defined( 'ABSPATH' ) || exit;

// استایل‌های ضروری (اگر در هدر سایت نیستند)
?>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Vazirmatn', sans-serif; }
    /* استایل‌های اختصاصی لینک‌های منو در فایل navigation اعمال می‌شود */
    .woocommerce-MyAccount-navigation { grid-column: span 1 / span 1; }
    .woocommerce-MyAccount-content { grid-column: span 1 / span 1; }
    @media (min-width: 1024px) {
        .woocommerce-MyAccount-content { grid-column: span 3 / span 3; }
    }
    
    /* مخفی کردن بولت‌های لیست پیش‌فرض ووکامرس */
    .woocommerce-MyAccount-navigation ul { list-style: none; padding: 0; margin: 0; }
</style>

<div class="bg-slate-50 text-slate-800 min-h-screen pb-12" dir="rtl">

    <!-- Main Container -->
    <main class="container mx-auto px-4 py-8 md:py-12">
        
        <!-- Breadcrumb (استاتیک برای زیبایی، می‌توانید با تابع ووکامرس جایگزین کنید) -->
        <div class="text-sm text-slate-500 mb-8 flex items-center gap-2">
            <a href="<?php echo esc_url( home_url() ); ?>" class="hover:text-orange-500 transition-colors"><i class="fas fa-home"></i></a>
            <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
            <span class="text-slate-800 font-bold">حساب کاربری من</span>
        </div>

        <!-- Grid Layout: Sidebar (1 Col) + Content (3 Cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar Navigation Hook -->
            <?php
            /**
             * My Account navigation.
             * @since 2.6.0
             */
            do_action( 'woocommerce_account_navigation' );
            ?>

            <!-- Content Area Hook -->
            <div class="woocommerce-MyAccount-content lg:col-span-3">
                <?php
                    /**
                     * My Account content.
                     * @since 2.6.0
                     */
                    do_action( 'woocommerce_account_content' );
                ?>
            </div>

        </div>
    </main>
</div>