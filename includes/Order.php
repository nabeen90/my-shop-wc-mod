<?php

namespace Nabin\Mswm;

class Order {

	public static ?Order $instance = null;

	public static function getInstance(): ?Order {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		add_filter( 'woocommerce_checkout_fields', [ $this, 'dropout_location_callback' ], 10, 1 );
		add_action( 'woocommerce_checkout_create_order', [ $this, 'save_dropout_location' ], 10, 2 );

		add_action( 'woocommerce_admin_order_data_after_order_details', [ $this, 'display_dropout_data_in_admin' ] );
		add_action( 'woocommerce_process_shop_order_meta', [ $this, 'save_dropout_location_data' ], 45, 2 );

		add_filter( 'woocommerce_email_customer_details', [ $this, 'email_dropout_location_data' ], 30, 3 );

	}

	function dropout_location_callback( $fields ) {

		$fields['billing']['_dropout_location'] = array(
			'label'       => __( 'Dropout Location', 'woocommerce' ),
			'type'        => 'text',
			'placeholder' => _x( 'Drop Out Location', 'placeholder', 'woocommerce' ),
			'required'    => false,
			'class'       => array( 'dropout-loc' ),
			'clear'       => true,
			'priority'    => 120,
		);

		return $fields;
	}

	function save_dropout_location( $order, $data ) {
		if ( isset( $data['_dropout_location'] ) ) {
			$order->update_meta_data( '_dropout_location', sanitize_text_field( $data['_dropout_location'] ) );
		}
	}

	function display_dropout_data_in_admin( $order ) { ?>
        <div class="order_data_column">

            <h4><?php _e( 'Dropout Location', 'woocommerce' ); ?><a href="#"
                                                                    class="edit_address"><?php _e( 'Edit', 'woocommerce' ); ?></a>
            </h4>
            <div class="address">
				<?php
				echo '<p><strong>' . __( 'Dropout Location' ) . ':</strong>' . $order->get_meta( '_dropout_location' ) . '</p>'; ?>
            </div>
            <div class="edit_address">
				<?php woocommerce_wp_text_input( array(
					'id'            => '_dropout_location',
					'label'         => __( 'Dropout Location' ),
					'wrapper_class' => '_billing_company_field'
				) ); ?>
            </div>
        </div>
	<?php }

	function save_dropout_location_data( $order_id, $post ) {
		$order = wc_get_order( $order_id );
		$order->update_meta_data( '_dropout_location', wc_clean( $_POST['_dropout_location'] ) );
		$order->save_meta_data();
	}

	function email_dropout_location_data( $order, $sent_to_admin, $plain_text ) {
		$dropout_loc = $order->get_meta( '_dropout_location' );
		?>
        <div class="dropout">
            <p>Dropout Location is <span style="font-size: 14px; font-weight: bold;"><?php echo $dropout_loc; ?></span></p>
        </div>
		<?php
	}
}