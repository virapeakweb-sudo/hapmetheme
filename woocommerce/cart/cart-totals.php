<?php
/**
 * Cart totals
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

    <?php do_action( 'woocommerce_before_cart_totals' ); ?>

    <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 sticky top-24">
        
        <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-4">
            <h3 class="font-bold text-slate-800 text-lg">خلاصه سفارش</h3>
            <span class="text-xs text-slate-400"><?php echo WC()->cart->get_cart_contents_count(); ?> کالا</span>
        </div>

        <div class="space-y-3 text-sm mb-6">
            <div class="flex justify-between text-slate-500">
                <span>قیمت کالاها (Subtotal)</span>
                <span class="font-medium"><?php wc_cart_totals_subtotal_html(); ?></span>
            </div>
            
            <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                <div class="flex justify-between text-red-500 cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                    <span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
                    <span class="font-medium"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
                </div>
            <?php endforeach; ?>

            <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                <div class="flex justify-between text-slate-500">
                    <span>هزینه ارسال</span>
                    <span class="font-medium text-green-600"><?php wc_cart_totals_shipping_html(); ?></span>
                </div>
            <?php endif; ?>
            
            <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                <div class="flex justify-between text-slate-500 fee">
                    <span><?php echo esc_html( $fee->name ); ?></span>
                    <span class="font-medium"><?php wc_cart_totals_fee_html( $fee ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="bg-orange-50 p-4 rounded-xl flex justify-between items-center mb-6 border border-orange-100">
            <span class="text-slate-700 font-bold text-sm">جمع سبد خرید</span>
            <div class="text-xl font-black text-orange-600">
                <?php wc_cart_totals_order_total_html(); ?> 
            </div>
        </div>

        <div class="wc-proceed-to-checkout">
            <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
        </div>
        
        <div class="mt-4 flex items-center justify-center gap-4 text-slate-400 text-2xl">
            <i class="fab fa-cc-visa opacity-50"></i>
            <i class="fab fa-cc-mastercard opacity-50"></i>
            <i class="fas fa-credit-card opacity-50"></i>
        </div>

        <p class="text-xs text-center text-slate-400 mt-4 leading-relaxed bg-slate-50 text-slate-500 p-3 rounded-lg border border-slate-100">
            <i class="fas fa-shield-alt ml-1"></i>
            کالاهای موجود در سبد شما ثبت و رزرو نشده‌اند.
        </p>
    </div>

    <?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>