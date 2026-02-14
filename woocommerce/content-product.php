<?php
/**
 * The template for displaying product content within loops
 *
 * Path: wp-content/themes/hapomeo/woocommerce/content-product.php
 * Version: 3.3
 * توضیحات: اضافه کردن بج تاریخ انقضا و درصد تخفیف
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>

<div <?php wc_product_class( 'bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 flex flex-col h-full relative', $product ); ?>>
    
    <a href="<?php the_permalink(); ?>" class="block flex-grow flex flex-col h-full">
        
        <!-- تصویر محصول -->
        <div class="relative overflow-hidden bg-slate-50 aspect-square">
            
            <!-- بج تاریخ انقضا (در صورت وجود متا دیتا) -->
            <?php 
            // فرض بر این است که تاریخ انقضا در متای '_expiry_date' ذخیره شده است.
            // اگر از افزونه خاصی استفاده می‌کنید، باید نام فیلد را تغییر دهید.
            $expiry_date = get_post_meta( $product->get_id(), '_expiry_date', true );
            if ( $expiry_date ) : ?>
                <div class="absolute top-3 left-3 z-10 bg-red-50 text-red-600 text-[10px] md:text-xs font-bold px-2.5 py-1 rounded-full border border-red-100 flex items-center gap-1 shadow-sm">
                    <i class="far fa-clock"></i> انقضا: <?php echo esc_html( $expiry_date ); ?>
                </div>
            <?php endif; ?>

            <?php 
            if ( has_post_thumbnail() ) {
                $image_props = array(
                    'class' => 'w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-500 mix-blend-multiply',
                );
                echo get_the_post_thumbnail( $product->get_id(), 'woocommerce_thumbnail', $image_props );
            } else {
                echo sprintf( '<img src="%s" alt="%s" class="w-full h-full object-cover opacity-50" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) );
            }
            ?>
            
            <!-- بج درصد تخفیف -->
            <?php if ( $product->is_on_sale() && $product->get_type() !== 'variable' ) : 
                // محاسبه درصد تخفیف برای محصولات ساده
                $percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
            ?>
                <span class="absolute bottom-0 right-0 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-tl-xl"><?php echo $percentage; ?>٪ تخفیف</span>
            <?php elseif ( $product->is_on_sale() && $product->get_type() === 'variable' ) : ?>
                 <!-- برای محصولات متغیر فقط کلمه حراج را نشان می‌دهیم -->
                 <span class="absolute bottom-0 right-0 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-tl-xl">حراج</span>
            <?php endif; ?>

        </div>

        <div class="p-4 flex flex-col flex-grow">
            <!-- دسته‌بندی -->
            <?php
            $terms = get_the_terms( $product->get_id(), 'product_cat' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                $cat = array_shift( $terms );
                echo '<span class="text-xs text-slate-400 font-semibold mb-1 block">' . esc_html( $cat->name ) . '</span>';
            }
            ?>

            <!-- عنوان محصول -->
            <h3 class="font-bold text-slate-700 truncate text-sm md:text-base mb-auto leading-snug min-h-[1.5em]">
                <?php the_title(); ?>
            </h3>
            
            <!-- قیمت اصلاح شده -->
            <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between">
                <div>
                    <?php if ( $product->is_on_sale() ) : ?>
                         <!-- نمایش قیمت خط خورده به صورت دستی و ساده (بدون تکرار) -->
                         <span class="text-xs text-slate-400 line-through block mb-0.5">
                             <?php echo wc_price( $product->get_regular_price() ); ?>
                         </span>
                    <?php else: ?>
                        <!-- فضای خالی برای تراز ماندن -->
                        <span class="text-xs text-transparent block mb-0.5">-</span>
                    <?php endif; ?>
                    
                    <!-- نمایش قیمت نهایی بدون استایل‌های اضافه ووکامرس -->
                    <div class="text-lg font-extrabold text-orange-500">
                        <?php echo wc_price( $product->get_price() ); ?>
                    </div>
                </div>
                
                <!-- آیکون افزودن -->
                <div class="bg-slate-900 text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-md group-hover:bg-orange-500 transition-colors">
                    <i class="fas fa-arrow-left text-xs"></i>
                </div>
            </div>
        </div>
    </a>
</div>