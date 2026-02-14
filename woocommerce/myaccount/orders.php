<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 * * Edited for: Hapoomio Custom Design
 * Path: /woocommerce/my-account/orders.php
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
        <span class="w-2 h-8 bg-orange-500 rounded-full block"></span>
        سفارش‌های من
    </h2>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm text-right responsive-table woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
            <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                <tr>
                    <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
                        <th class="p-4 woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?>">
                            <span class="nobr"><?php echo esc_html( $column_name ); ?></span>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                <?php
                foreach ( $customer_orders->orders as $customer_order ) {
                    $order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> order">
                        <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
                            <td class="p-4 woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>" data-label="<?php echo esc_attr( $column_name ); ?>">
                                <?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
                                    <?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

                                <?php elseif ( 'order-number' === $column_id ) : ?>
                                    <span class="font-bold text-slate-800">
                                        <?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
                                    </span>

                                <?php elseif ( 'order-date' === $column_id ) : ?>
                                    <span class="text-slate-600">
                                        <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
                                    </span>

                                <?php elseif ( 'order-status' === $column_id ) : ?>
                                    <?php 
                                    // Custom styling for status based on your HTML
                                    $status = $order->get_status();
                                    $status_class = 'bg-slate-100 text-slate-600';
                                    if($status == 'completed') $status_class = 'bg-green-50 text-green-600';
                                    if($status == 'processing') $status_class = 'bg-blue-50 text-blue-600';
                                    if($status == 'cancelled' || $status == 'failed') $status_class = 'bg-red-50 text-red-600';
                                    if($status == 'on-hold') $status_class = 'bg-orange-50 text-orange-600';
                                    ?>
                                    <span class="<?php echo $status_class; ?> px-3 py-1 rounded-full text-xs font-bold">
                                        <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                                    </span>

                                <?php elseif ( 'order-total' === $column_id ) : ?>
                                    <span class="font-bold text-slate-700">
                                        <?php echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) ); ?>
                                    </span>

                                <?php elseif ( 'order-actions' === $column_id ) : ?>
                                    <?php
                                    $actions = wc_get_account_orders_actions( $order );
                                    if ( ! empty( $actions ) ) {
                                        foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                            echo '<a href="' . esc_url( $action['url'] ) . '" class="inline-block bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white px-4 py-2 rounded-lg transition-colors font-bold text-xs ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
                                        }
                                    }
                                    ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

    <?php if ( 1 < $customer_orders->max_num_pages ) : ?>
        <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination mt-6 flex justify-center">
            <?php if ( 1 !== $customer_orders->page_no ) : ?>
                <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button bg-slate-200 text-slate-600 px-4 py-2 rounded-lg ml-2 hover:bg-orange-500 hover:text-white transition-colors" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $customer_orders->page_no - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
            <?php endif; ?>

            <?php if ( intval( $customer_orders->max_num_pages ) !== $customer_orders->page_no ) : ?>
                <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button bg-slate-200 text-slate-600 px-4 py-2 rounded-lg hover:bg-orange-500 hover:text-white transition-colors" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $customer_orders->page_no + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php else : ?>
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-2xl">
            <i class="fas fa-box-open"></i>
        </div>
        <p class="text-slate-600 mb-4"><?php esc_html_e( 'No order has been made yet.', 'woocommerce' ); ?></p>
        <a class="inline-block bg-orange-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-orange-600 transition-colors" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
            <?php esc_html_e( 'Browse products', 'woocommerce' ); ?>
        </a>
    </div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>