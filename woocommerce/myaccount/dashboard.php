<?php
/**
 * My Account Dashboard
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 * * Edited for: Hapoomio Custom Design
 * Path: /woocommerce/my-account/dashboard.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="account-content animate-fade-in">
    <!-- Welcome Message -->
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-slate-100 mb-6">
        <h2 class="text-2xl font-bold text-slate-800 mb-4">
            سلام، <?php global $current_user; echo esc_html( $current_user->display_name ); ?> عزیز! 👋
        </h2>
        <p class="text-slate-600 leading-relaxed">
            به پیشخوان حساب کاربری خود خوش آمدید. از اینجا می‌توانید 
            <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="text-orange-500 hover:underline">سفارش‌های اخیر</a> 
            خود را مشاهده کنید، 
            <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="text-orange-500 hover:underline">آدرس‌های حمل و نقل</a> 
            را مدیریت کنید و 
            <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="text-orange-500 hover:underline">جزئیات حساب کاربری</a> 
            خود را ویرایش کنید.
        </p>
    </div>

    <!-- Quick Stats Boxes (Static Layout - Dynamic Data needs custom coding) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        
        <!-- Box 1: Orders -->
        <div class="bg-blue-50 p-6 rounded-2xl flex items-center justify-between group hover:bg-blue-100 transition-colors cursor-pointer" onclick="window.location='<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>'">
            <div>
                <p class="text-blue-500 text-sm font-bold mb-1">سفارش‌ها</p>
                <p class="text-2xl font-black text-slate-800">
                    <?php 
                    // دریافت تعداد سفارش‌های مشتری
                    $customer_orders = get_posts( array(
                        'numberposts' => -1,
                        'meta_key'    => '_customer_user',
                        'meta_value'  => get_current_user_id(),
                        'post_type'   => wc_get_order_types(),
                        'post_status' => array_keys( wc_get_order_statuses() ),
                    ) );
                    echo count( $customer_orders );
                    ?>
                    <span class="text-xs font-normal">عدد</span>
                </p>
            </div>
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-500 text-xl shadow-sm group-hover:scale-110 transition-transform">
                <i class="fas fa-box-open"></i>
            </div>
        </div>

        <!-- Box 2: Total Spent (Optional - requires custom helper usually) -->
        <div class="bg-green-50 p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-green-500 text-sm font-bold mb-1">وضعیت حساب</p>
                <p class="text-lg font-black text-slate-800">فعال</p>
            </div>
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-green-500 text-xl shadow-sm">
                <i class="fas fa-wallet"></i>
            </div>
        </div>

        <!-- Box 3: Loyalty (Static Placeholder) -->
        <div class="bg-orange-50 p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-orange-500 text-sm font-bold mb-1">باشگاه مشتریان</p>
                <p class="text-lg font-black text-slate-800">عضو معمولی</p>
            </div>
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-orange-500 text-xl shadow-sm">
                <i class="fas fa-star"></i>
            </div>
        </div>

    </div>
</div>