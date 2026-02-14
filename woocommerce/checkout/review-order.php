<?php
/**
 * Review Order (Sidebar)
 * Path: themes/your-theme/woocommerce/checkout/review-order.php
 * * FIX 2: Removed #payment section from this file because WooCommerce 
 * automatically hooks 'woocommerce_checkout_payment' after this template.
 * Keeping it here causes duplication and AJAX loops.
 */
defined( 'ABSPATH' ) || exit;
?>

<!-- 
    کانتینر اصلی برای آپدیت‌های AJAX
    ووکامرس محتویات این کلاس را با اطلاعات جدید جایگزین می‌کند.
-->
<div class="woocommerce-checkout-review-order-table">

    <!-- لیست محصولات -->
    <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2 custom-scroll">
        <?php
        do_action( 'woocommerce_review_order_before_cart_contents' );

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                ?>
                <div class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?> flex gap-3 items-center border-b border-slate-50 pb-2">
                    
                    <!-- تصویر محصول -->
                    <div class="w-16 h-16 bg-slate-50 rounded-lg p-1 border border-slate-100 shrink-0">
                        <?php 
                        $thumbnail = $_product->get_image('thumbnail', ['class' => 'w-full h-full object-cover rounded']);
                        echo $thumbnail;
                        ?>
                    </div>

                    <!-- عنوان و قیمت -->
                    <div class="flex-grow">
                        <h4 class="text-xs font-bold text-slate-700 line-clamp-2">
                            <?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ); ?>
                        </h4>
                        <div class="flex justify-between items-end mt-1">
                            <span class="text-xs text-slate-400">
                                <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', sprintf( '%s عدد', $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                            </span>
                            <span class="text-sm font-bold text-slate-800">
                                <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        do_action( 'woocommerce_review_order_after_cart_contents' );
        ?>
    </div>

    <hr class="border-slate-100 mb-4">

    <!-- بخش جمع کل و هزینه‌ها -->
    <div class="space-y-3 text-sm mb-6">
        
        <div class="flex justify-between text-slate-500">
            <span><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
            <span class="font-medium"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>

        <!-- شیوه ارسال -->
        <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <div class="shipping-methods-wrapper py-2">
                <span class="block text-slate-700 font-bold mb-2">شیوه ارسال:</span>
                <?php wc_cart_totals_shipping_html(); ?>
            </div>
        <?php endif; ?>

        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <div class="fee flex justify-between text-slate-500">
                <span><?php echo esc_html( $fee->name ); ?></span>
                <span class="font-medium"><?php wc_cart_totals_fee_html( $fee ); ?></span>
            </div>
        <?php endforeach; ?>

        <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
            <div class="tax-total flex justify-between text-slate-500">
                <span><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
                <span class="font-medium"><?php wc_cart_totals_taxes_total_html(); ?></span>
            </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

        <div class="flex justify-between text-slate-700 font-bold bg-orange-50 p-3 rounded-lg">
            <span><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
            <span class="text-orange-600 text-lg"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>

        <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
    </div>
    
    <!-- 
        نکته: بخش دکمه پرداخت (Payment) از اینجا حذف شد.
        ووکامرس به صورت خودکار فایل checkout/payment.php را بعد از این فایل فراخوانی می‌کند.
        این کار باعث می‌شود دکمه دقیقاً زیر همین باکس قرار بگیرد بدون اینکه صفحه خراب شود.
    -->

</div>