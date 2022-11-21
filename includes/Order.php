<?php

namespace Nabin\Mswm;
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Order {

	public static ?Order $instance = null;

	public static function getInstance(): ?Order {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		add_filter( 'woocommerce_checkout_fields', [ $this, 'dropout_location_callback' ], 10, 1 );
//		add_action( 'woocommerce_checkout_process', [ $this, 'custom_dropout_loc_validation' ] );
		add_action( 'woocommerce_checkout_create_order', [ $this, 'save_dropout_location' ], 10, 2 );

		add_action( 'woocommerce_admin_order_data_after_order_details', [ $this, 'display_dropout_data_in_admin' ] );
		add_action( 'woocommerce_process_shop_order_meta', [ $this, 'save_dropout_location_data' ], 45, 2 );

		add_filter( 'woocommerce_email_customer_details', [ $this, 'email_dropout_location_data' ], 30, 3 );

		add_action( 'woocommerce_admin_order_data_after_billing_address', [
			$this,
			'my_custom_checkout_field_display_admin_order_meta'
		], 10, 1 );
	}

	public function dropout_location_callback( $fields ) {
		$i = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product       = $cart_item['data'];
			$product_id    = $product->get_id();
			$product_name  = $product->get_name();
			$show_location = get_field( 'show_dropout_location', $product_id );
			$i ++;

			if ( $i >= 1 && $show_location == 1 ) {
				$fields['billing'][ '_dropout_location_' . $product_id ] = array(
					'type'        => 'text',
					'id'          => '_dropout_location_' . $product_id,
					'class'       => array( 'form-row-wide' ),
					'label'       => __( 'Dropout Location for ' . $product_name, 'woocommerce' ),
					'placeholder' => _x( 'Drop Out Location', 'placeholder', 'woocommerce' ),
					'clear'       => true,
					'required'    => true,
					'priority'    => 120
				);
			}
		}

		return $fields;
	}

	/**
	 * @param $order \WC_Order
	 * @param $data
	 *
	 * @return void
	 */
	public function save_dropout_location( $order, $data ) {
		$items             = $order->get_items();
		$dropout_locations = [];
		foreach ( $items as $item_id => $item ) {
			$product_id = $item->get_product_id();
			if ( get_field( 'show_dropout_location', $product_id ) ) {
				$dropout_locations[ $product_id ] = sanitize_text_field( $data[ '_dropout_location_' . $product_id ] );
			}
		}
		$order->update_meta_data( 'dropout_locations', $dropout_locations );
	}

	/**
	 * Display field value on the order edit page
	 */

	function my_custom_checkout_field_display_admin_order_meta( $order ) {
		$dropout_locations = $order->get_meta( 'dropout_locations' );
		if ( ! empty( $dropout_locations ) ) {
			foreach ( $dropout_locations as $product_id => $dropout_location ) {
				$product = wc_get_product( $product_id );
				echo 'Drop out location for ' . $product->get_name() . ' is ' . $dropout_location . '<br />';
			}
		}

	}

	function display_dropout_data_in_admin( $order ) {
		$dropout_locations = $order->get_meta( 'dropout_locations' );
		foreach ( $dropout_locations as $product_id => $dropout_location ) {
			$product = wc_get_product( $product_id );
			?>
            <div class="order_data_column">
                <h4><?php _e( 'Dropout Location For ' . $product->get_name(), 'woocommerce' ); ?><a href="#"
                                                                                                    class="edit_address"><?php _e( 'Edit', 'woocommerce' ); ?></a>
                </h4>
                <div class="address">
					<?php
					echo '<p><strong>' . __( 'Dropout Location For ' . $product->get_name() ) . ':</strong>' . $dropout_location . '</p>'; ?>
                </div>
                <div class="edit_address">
					<?php woocommerce_wp_text_input( array(
						'id'            => $dropout_location,
						'label'         => __( 'Dropout Location For ' . $product->get_name() ),
						'wrapper_class' => '_billing_company_field'
					) ); ?>
                </div>
            </div>
		<?php }
	}

	function save_dropout_location_data( $order_id, $post ) {
		$order = wc_get_order( $order_id );
//		$dropout_locations = $order->get_meta( 'dropout_locations' );
		$order->update_meta_data( '_dropout_location', wc_clean( $_POST['_dropout_location'] ) );
		$order->save_meta_data();
	}

	function email_dropout_location_data( $order, $sent_to_admin, $plain_text ) {
		$dropout_loc = $order->get_meta( '_dropout_location' );
		?>
        <div class="dropout">
            <p>Dropout Location is <span style="font-size: 14px; font-weight: bold;"><?php echo $dropout_loc; ?></span>
            </p>
        </div>
		<?php
	}

}