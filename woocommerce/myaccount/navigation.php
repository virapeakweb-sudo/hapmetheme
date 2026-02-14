<?php
/**
 * My Account Navigation
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 * * Edited for: Hapoomio Custom Design
 * Path: /woocommerce/my-account/navigation.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
?>

<aside class="woocommerce-MyAccount-navigation lg:col-span-1">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden sticky top-28">
        
        <!-- User Info Mini Header -->
        <div class="p-6 text-center border-b border-slate-50 bg-slate-50/50">
            <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center text-orange-500 mx-auto mb-3 border-4 border-white shadow-sm overflow-hidden">
                <?php echo get_avatar( $current_user->ID, 80 ); ?>
            </div>
            <h3 class="font-bold text-slate-800"><?php echo esc_html( $current_user->display_name ); ?></h3>
            <p class="text-xs text-slate-500 mt-1">خوش آمدید</p>
        </div>

        <!-- Navigation Links -->
        <nav class="p-2 space-y-1">
            <ul>
                <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                    <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?> mb-1">
                        <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-slate-600 hover:bg-orange-50 hover:text-orange-500 transition-all 
                           <?php echo ( is_wc_endpoint_url( $endpoint ) ) ? 'bg-orange-50 text-orange-500 border-r-4 border-orange-500' : 'border-r-4 border-transparent'; ?>">
                            
                            <!-- Icons based on endpoint -->
                            <?php 
                            $icon_class = 'fa-circle-dot'; // Default
                            switch($endpoint) {
                                case 'dashboard': $icon_class = 'fa-tachometer-alt'; break;
                                case 'orders': $icon_class = 'fa-shopping-bag'; break;
                                case 'downloads': $icon_class = 'fa-download'; break;
                                case 'edit-address': $icon_class = 'fa-map-marker-alt'; break;
                                case 'edit-account': $icon_class = 'fa-user-cog'; break;
                                case 'customer-logout': $icon_class = 'fa-sign-out-alt'; break;
                            }
                            ?>
                            <i class="fas <?php echo $icon_class; ?> w-6 text-center"></i>
                            <?php echo esc_html( $label ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>