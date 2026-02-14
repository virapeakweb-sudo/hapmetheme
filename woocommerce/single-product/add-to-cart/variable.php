<?php
/**
 * Variable product add to cart
 * Path: wp-content/themes/your-theme/woocommerce/single-product/add-to-cart/variable.php
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>

		<div class="variations-wrapper">
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
                <?php 
                    $sanitized_name = sanitize_title( $attribute_name );
                ?>
				<div class="mb-6 variations variation-row">
                    <label class="font-bold mb-3 block text-sm text-slate-800" for="<?php echo esc_attr( $sanitized_name ); ?>">
                        انتخاب <?php echo wc_attribute_label( $attribute_name ); ?>:
                    </label>
                    
                    <div class="flex flex-wrap gap-3 custom-radios mb-2">
                        <?php 
                            // دریافت مقدار پیش‌فرض
                            $selected_value = $product->get_variation_default_attribute( $attribute_name );
                            // اگر کاربر قبلا انتخاب کرده یا رفرش کرده
                            if ( isset( $_REQUEST[ 'attribute_' . $sanitized_name ] ) ) { 
                                $selected_value = $_REQUEST[ 'attribute_' . $sanitized_name ]; 
                            }

                            foreach ( $options as $option ) : 
                                // منطق نمایش نام فارسی
                                $display_name = $option;
                                if ( taxonomy_exists( $attribute_name ) ) {
                                    $term = get_term_by( 'slug', $option, $attribute_name );
                                    if ( $term && ! is_wp_error( $term ) ) {
                                        $display_name = $term->name;
                                    }
                                } else {
                                    $display_name = urldecode( $option );
                                }
                                
                                $is_active = $selected_value == $option ? 'selected' : '';
                        ?>
                            <div class="custom-variation-btn <?php echo $is_active; ?>" data-value="<?php echo esc_attr( $option ); ?>">
                                <?php echo esc_html( $display_name ); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php
						wc_dropdown_variation_attribute_options(
							array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
                                'class'     => 'hidden-variation-select', // این کلاس در CSS شما مخفی شده است
							)
						);
						// لینک پاک کردن (اختیاری)
                        // echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations text-xs text-red-500 block mt-2" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) : '';
					?>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 */
				// do_action( 'woocommerce_single_variation' ); 
                // ما به جای اکشن بالا، کد دکمه افزودن به سبد خرید را مستقیم اینجا می‌گذاریم تا استایل حفظ شود
			?>

            <div class="woocommerce-variation single_variation mb-4 min-h-[20px]"></div>

            <div class="woocommerce-variation-add-to-cart variations_button flex flex-col sm:flex-row gap-4 items-end sm:items-center">
                
                <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

                <div class="flex items-center h-12 border border-slate-200 rounded-xl overflow-hidden bg-slate-50 w-32">
                    <button type="button" class="w-10 h-full text-slate-600 hover:bg-orange-100 hover:text-orange-600 transition-colors text-lg font-bold flex items-center justify-center cursor-pointer qty-btn minus">-</button>
                    <?php 
                    woocommerce_quantity_input( array(
                        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
                        'classes'     => array('w-12', 'h-full', 'text-center', 'bg-transparent', 'border-x', 'border-slate-200', 'text-slate-800', 'font-bold', 'focus:outline-none', 'qty')
                    ) ); 
                    ?>
                    <button type="button" class="w-10 h-full text-slate-600 hover:bg-orange-100 hover:text-orange-600 transition-colors text-lg font-bold flex items-center justify-center cursor-pointer qty-btn plus">+</button>
                </div>

                <button type="submit" class="single_add_to_cart_button button alt flex-grow bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/40 transition-all flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-shopping-cart text-lg"></i> 
                    <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                </button>

                <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
                <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
                <input type="hidden" name="variation_id" class="variation_id" value="0" />

                <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
            </div>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
?>