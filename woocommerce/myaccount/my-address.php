<?php
/**
 * My Addresses
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 * * Edited for: Hapoomio Custom Design
 * Path: /woocommerce/my-account/my-address.php
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}

$oldcol = 1;
$col    = 1;
?>

<h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
    <span class="w-2 h-8 bg-orange-500 rounded-full block"></span>
    آدرس‌های من
</h2>

<p class="text-slate-600 mb-6 text-sm">
    <?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</p>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 u-columns woocommerce-Addresses col2-set addresses">

<?php foreach ( $get_addresses as $name => $address_title ) : ?>
    <?php
        $address = wc_get_account_formatted_address( $name );
        $col     = $col * -1;
    ?>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative group hover:border-orange-200 transition-colors u-column<?php echo $col < 0 ? 1 : 2; ?> col-<?php echo $col < 0 ? 1 : 2; ?> woocommerce-Address">
        <header class="woocommerce-Address-title title flex justify-between items-start mb-4">
            <h3 class="font-bold text-lg text-slate-800"><?php echo esc_html( $address_title ); ?></h3>
            <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:bg-orange-500 hover:text-white flex items-center justify-center transition-all edit">
                <i class="fas fa-pen text-xs"></i>
            </a>
        </header>
        
        <div class="text-sm text-slate-500 space-y-2 leading-relaxed woocommerce-Address-address">
            <?php if( $address ) : ?>
                <?php echo wp_kses_post( $address ); ?>
            <?php else: ?>
                <p class="text-slate-400 italic"><?php esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ); ?></p>
            <?php endif; ?>
        </div>
    </div>

<?php endforeach; ?>

</div>