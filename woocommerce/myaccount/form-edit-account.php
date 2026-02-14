<?php
/**
 * Edit account form
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 * * Edited for: Hapoomio Custom Design
 * Path: /woocommerce/my-account/form-edit-account.php
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
    <span class="w-2 h-8 bg-orange-500 rounded-full block"></span>
    جزئیات حساب کاربری
</h2>

<div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-slate-100">
    <form class="woocommerce-EditAccountForm edit-account space-y-6" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

        <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="account_first_name" class="text-sm font-bold text-slate-700"><?php esc_html_e( 'First name', 'woocommerce' ); ?> <span class="text-red-500">*</span></label>
                <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition-all text-sm woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
            </div>
            <div class="space-y-2">
                <label for="account_last_name" class="text-sm font-bold text-slate-700"><?php esc_html_e( 'Last name', 'woocommerce' ); ?> <span class="text-red-500">*</span></label>
                <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition-all text-sm woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
            </div>
        </div>

        <div class="space-y-2">
            <label for="account_display_name" class="text-sm font-bold text-slate-700"><?php esc_html_e( 'Display name', 'woocommerce' ); ?> <span class="text-red-500">*</span> <span class="text-xs font-normal text-slate-400">(<?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?>)</span></label>
            <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition-all text-sm woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" />
        </div>

        <div class="space-y-2">
            <label for="account_email" class="text-sm font-bold text-slate-700"><?php esc_html_e( 'Email address', 'woocommerce' ); ?> <span class="text-red-500">*</span></label>
            <input type="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition-all text-sm text-left woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" dir="ltr" />
        </div>

        <div class="border-t border-slate-100 my-6 pt-6">
            <h3 class="font-bold text-slate-800 mb-4"><?php esc_html_e( 'Password change', 'woocommerce' ); ?></h3>
            
            <div class="space-y-4">
                <input type="password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition-all text-sm woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" placeholder="<?php esc_attr_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?>" />
                
                <input type="password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition-all text-sm woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" placeholder="<?php esc_attr_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?>" />
                
                <input type="password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition-all text-sm woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" placeholder="<?php esc_attr_e( 'Confirm new password', 'woocommerce' ); ?>" />
            </div>
        </div>

        <?php do_action( 'woocommerce_edit_account_form' ); ?>

        <p class="flex items-center gap-4">
            <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
            <button type="submit" class="woocommerce-Button button bg-slate-900 text-white font-bold py-3 px-8 rounded-xl hover:bg-orange-500 transition-colors shadow-lg shadow-slate-200 hover:shadow-orange-200" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
            <input type="hidden" name="action" value="save_account_details" />
        </p>

        <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
    </form>
</div>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>