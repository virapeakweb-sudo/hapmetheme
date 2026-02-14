<?php
/**
 * Billing Details
 * Path: themes/your-theme/woocommerce/checkout/form-billing.php
 */
defined( 'ABSPATH' ) || exit;
?>

<!-- باکس ۱: اطلاعات فردی -->
<div class="bg-white p-5 md:p-8 rounded-2xl shadow-sm border border-slate-100 mb-6">
    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
        <span class="bg-orange-100 text-orange-600 w-8 h-8 flex items-center justify-center rounded-lg text-sm">1</span>
        اطلاعات گیرنده
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php 
        // فیلدهای نام، نام خانوادگی و موبایل و ایمیل را اینجا نمایش می‌دهیم
        $fields = $checkout->get_checkout_fields( 'billing' );
        
        // لیست فیلدهایی که در باکس اول می‌خواهیم
        $box1_keys = array('billing_first_name', 'billing_last_name', 'billing_phone', 'billing_email');

        foreach ( $box1_keys as $key ) {
            if ( isset( $fields[$key] ) ) {
                woocommerce_form_field( $key, $fields[$key], $checkout->get_value( $key ) );
                // برای جلوگیری از چاپ مجدد در پایین، آن را از آرایه حذف می‌کنیم (اختیاری ولی تمیزتر است)
                unset($fields[$key]);
            }
        }
        ?>
    </div>
</div>

<!-- باکس ۲: آدرس تحویل -->
<div class="bg-white p-5 md:p-8 rounded-2xl shadow-sm border border-slate-100 mb-6">
    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
        <span class="bg-blue-100 text-blue-600 w-8 h-8 flex items-center justify-center rounded-lg text-sm">2</span>
        آدرس تحویل سفارش
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php
        // بقیه فیلدها (استان، شهر، آدرس و...) اینجا نمایش داده می‌شوند
        foreach ( $fields as $key => $field ) {
            // فیلتر کردن فیلدهای باقیمانده (اگر فیلدی نمانده باشد چاپ نمی‌شود)
            if ( ! in_array($key, $box1_keys) ) {
                woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
            }
        }
        ?>
    </div>
</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
    <!-- بخش ثبت نام (اگر کاربر لاگین نباشد) -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 mt-6">
        <div class="woocommerce-account-fields">
            <?php if ( ! $checkout->is_registration_required() ) : ?>
                <p class="form-row form-row-wide create-account">
                    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox flex items-center gap-2">
                        <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span>ایجاد حساب کاربری؟</span>
                    </label>
                </p>
            <?php endif; ?>

            <?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

            <?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>
                <div class="create-account grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
                        <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
        </div>
    </div>
<?php endif; ?>