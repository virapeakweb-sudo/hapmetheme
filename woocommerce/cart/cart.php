<?php
/**
 * Cart Page
 *
 * Path: wp-content/themes/hapomeo/woocommerce/cart/cart.php
 * Version: 4.0
 * توضیحات: این فایل جایگزین قالب پیش‌فرض سبد خرید ووکامرس می‌شود و از دیزاین cart.html شما استفاده می‌کند.
 */

defined( 'ABSPATH' ) || exit;

// نمایش پیام‌های ووکامرس (مثل "محصول با موفقیت حذف شد")
do_action( 'woocommerce_before_cart' ); ?>

<div class="container mx-auto p-4 md:p-6 pb-24 md:pb-6">
    
    <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
        
        <?php if ( WC()->cart->is_empty() ) : ?>
            <!-- سبد خرید خالی -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-4xl">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mb-2">سبد خرید شما خالی است!</h2>
                <p class="text-slate-500 mb-6">به نظر می‌رسد هنوز محصولی انتخاب نکرده‌اید.</p>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="inline-block bg-orange-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-200 hover:bg-orange-600 transition-all">
                    بازگشت به فروشگاه
                </a>
            </div>
        <?php else : ?>

            <!-- نوار وضعیت ارسال رایگان (هاردکد شده فعلاً - بعداً می‌توان با PHP محاسبه کرد) -->
            <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 mb-6 max-w-5xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-center mb-3 gap-2">
                    <span class="text-slate-700 font-bold text-sm md:text-base flex items-center gap-2">
                        <i class="fas fa-gift text-orange-500 text-lg"></i>
                        هورا! <span class="text-green-600 font-extrabold mx-1">ارسال رایگان</span> برای شما فعال شد.
                    </span>
                    <span class="text-xs text-slate-400">مبلغ خرید شما: <?php wc_cart_totals_subtotal_html(); ?></span>
                </div>
                <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden relative">
                    <div class="absolute inset-0 w-full h-full opacity-30" style="background-image: linear-gradient(45deg,rgba(0,0,0,.1) 25%,transparent 25%,transparent 50%,rgba(0,0,0,.1) 50%,rgba(0,0,0,.1) 75%,transparent 75%,transparent); background-size: 1rem 1rem;"></div>
                    <div class="h-full bg-gradient-to-r from-green-400 to-green-500 rounded-full transition-all duration-1000" style="width: 100%;"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-7xl mx-auto">
                
                <!-- لیست محصولات -->
                <div class="lg:col-span-8 space-y-4">
                    
                    <div class="hidden md:flex justify-between items-center mb-2 px-2">
                        <h1 class="font-bold text-lg text-slate-800">سبد خرید شما (<?php echo WC()->cart->get_cart_contents_count(); ?> کالا)</h1>
                        <!-- دکمه خالی کردن سبد معمولاً نیاز به پلاگین یا کد سفارشی دارد، اینجا لینک ساده می‌گذاریم -->
                    </div>

                    <?php
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            ?>
                            
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row gap-4 relative group hover:border-orange-200 transition-colors woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                                
                                <!-- تصویر محصول -->
                                <div class="w-full md:w-32 h-32 bg-slate-50 rounded-xl p-2 flex items-center justify-center shrink-0">
                                    <?php
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                                    if ( ! $product_permalink ) {
                                        echo $thumbnail; // PHPCS: XSS ok.
                                    } else {
                                        printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                    }
                                    ?>
                                </div>
                                
                                <div class="flex-grow flex flex-col justify-between">
                                    <div>
                                        <h3 class="font-bold text-slate-800 text-sm md:text-base mb-2 leading-relaxed">
                                            <?php
                                            if ( ! $product_permalink ) {
                                                echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                            } else {
                                                echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s" class="hover:text-orange-500">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                            }
                                            ?>
                                        </h3>
                                        <!-- متادیتا (ویژگی‌ها) -->
                                        <div class="space-y-1 text-xs text-slate-500">
                                            <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                            
                                            <?php if ( $_product->is_in_stock() ) : ?>
                                                <div class="flex items-center gap-2 text-green-600 mt-1">
                                                    <i class="fas fa-check-circle w-4"></i>
                                                    <span>موجود در انبار هاپومیو</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- کنترل تعداد و قیمت (دسکتاپ) -->
                                    <div class="hidden md:flex justify-between items-end mt-4">
                                        <div class="flex items-center border border-slate-200 rounded-lg h-10">
                                            <?php
                                            if ( $_product->is_sold_individually() ) {
                                                $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                            } else {
                                                $product_quantity = woocommerce_quantity_input(
                                                    array(
                                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                                        'input_value'  => $cart_item['quantity'],
                                                        'max_value'    => $_product->get_max_purchase_quantity(),
                                                        'min_value'    => '0',
                                                        'product_name' => $_product->get_name(),
                                                        'classes'      => 'w-10 h-full text-center text-sm font-bold text-slate-700 outline-none border-x border-slate-100 bg-transparent appearance-none m-0', // کلاس‌های شما
                                                    ),
                                                    $_product,
                                                    false
                                                );
                                            }
                                            // نمایش اینپوت (نیاز به کمی CSS سفارشی برای استایل دقیق دکمه‌های +/- دارد که ووکامرس خودش اضافه می‌کند)
                                            echo $product_quantity;
                                            ?>
                                        </div>
                                        
                                        <div class="text-left">
                                            <div class="text-xl font-black text-slate-800">
                                                <?php
                                                    echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- کنترل تعداد و قیمت (موبایل) -->
                                <div class="flex md:hidden justify-between items-center mt-2 border-t border-slate-50 pt-3 w-full">
                                     <!-- ساده‌سازی برای موبایل: فقط نمایش تعداد -->
                                     <div class="flex items-center border border-slate-200 rounded-lg h-9 bg-white px-2">
                                        <span class="text-xs text-slate-500 ml-2">تعداد:</span>
                                        <span class="font-bold text-slate-800"><?php echo $cart_item['quantity']; ?></span>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="text-base font-black text-slate-800">
                                            <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- دکمه حذف -->
                                <?php
                                    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        'woocommerce_cart_item_remove_link',
                                        sprintf(
                                            '<a href="%s" class="hidden md:block absolute top-4 left-4 text-slate-300 hover:text-red-500 transition-colors remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fas fa-times text-xl"></i></a>',
                                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                            esc_html__( 'Remove this item', 'woocommerce' ),
                                            esc_attr( $product_id ),
                                            esc_attr( $_product->get_sku() )
                                        ),
                                        $cart_item_key
                                    );
                                ?>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <div class="text-left pt-4 hidden md:block">
                         <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-lg hover:bg-slate-900 transition-colors" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
                             <?php esc_html_e( 'بروزرسانی سبد خرید', 'woocommerce' ); ?>
                         </button> 
                        <a href="<?php echo wc_get_page_permalink( 'shop' ); ?>" class="text-orange-500 font-bold text-sm hover:underline mr-4">
                            <i class="fas fa-chevron-right text-xs ml-1"></i> خرید بیشتر
                        </a>
                    </div>

                </div>

                <!-- سایدبار خلاصه سفارش (Cart Totals) -->
                <div class="lg:col-span-4">
                    <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 sticky top-24 cart-collaterals">
                        
                        <?php
                        /**
                         * Cart collaterals hook.
                         *
                         * @hooked woocommerce_cross_sell_display
                         * @hooked woocommerce_cart_totals - 10
                         */
                        // ما اینجا فقط cart_totals را می‌خواهیم اما با استایل خودمان.
                        // پس دستی کد می‌زنیم یا هوک را صدا می‌زنیم. برای کنترل کامل HTML، دستی کد می‌زنیم:
                        ?>

                        <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-4">
                            <h3 class="font-bold text-slate-800 text-lg">خلاصه سفارش</h3>
                            <span class="text-xs text-slate-400"><?php echo WC()->cart->get_cart_contents_count(); ?> کالا</span>
                        </div>

                        <div class="space-y-3 text-sm mb-6">
                            <div class="flex justify-between text-slate-500">
                                <span>قیمت کالاها</span>
                                <span class="font-medium"><?php wc_cart_totals_subtotal_html(); ?></span>
                            </div>
                            <!-- مالیات و حمل و نقل معمولاً در این مرحله محاسبه نمی‌شود مگر آدرس وارد شود -->
                            <div class="flex justify-between text-slate-500">
                                <span>هزینه ارسال</span>
                                <span class="font-medium text-xs">در مرحله بعد محاسبه می‌شود</span>
                            </div>
                        </div>
                        
                        <div class="bg-orange-50 p-4 rounded-xl flex justify-between items-center mb-6 border border-orange-100">
                            <span class="text-slate-700 font-bold text-sm">مبلغ قابل پرداخت</span>
                            <div class="text-xl font-black text-orange-600"><?php wc_cart_totals_order_total_html(); ?></div>
                        </div>

                        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" id="main-checkout-btn" class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-200 transition-all text-center transform hover:-translate-y-1 checkout-button">
                            ادامه جهت تسویه حساب
                        </a>
                        
                        <p class="text-xs text-center text-slate-400 mt-4 leading-relaxed bg-slate-50 text-slate-500 p-3 rounded-lg border border-slate-100">
                            <i class="fas fa-shield-alt ml-1"></i>
                            کالاهای موجود در سبد شما ثبت و رزرو نشده‌اند، برای تکمیل مراحل خرید، سفارش خود را نهایی کنید.
                        </p>
                    </div>
                </div>
            </div>
            
            <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
        <?php endif; ?>
    </form>
</div>

<!-- فوتر چسبان موبایل -->
<?php if ( ! WC()->cart->is_empty() ) : ?>
<div id="mobile-sticky-footer" class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-50 lg:hidden shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] transition-all duration-300 transform translate-y-0 opacity-100">
    <div class="flex justify-between items-center gap-4">
            <div class="flex flex-col">
            <span class="text-[10px] text-slate-400">مبلغ قابل پرداخت:</span>
            <span class="font-black text-slate-800 text-lg"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>
        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="bg-orange-500 text-white font-bold py-3 px-8 rounded-xl hover:bg-orange-600 transition-colors shadow-md text-sm">
            تسویه حساب
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileFooter = document.getElementById('mobile-sticky-footer');
        const mainBtn = document.getElementById('main-checkout-btn');

        function toggleStickyFooter() {
            if (!mobileFooter || !mainBtn || window.innerWidth >= 1024) return;
            const mainBtnRect = mainBtn.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            if (mainBtnRect.top < windowHeight + 50) {
                mobileFooter.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
            } else {
                mobileFooter.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
            }
        }
        window.addEventListener('scroll', toggleStickyFooter);
        window.addEventListener('resize', toggleStickyFooter);
        toggleStickyFooter();
    });
</script>
<?php endif; ?>

<?php do_action( 'woocommerce_after_cart' ); ?>