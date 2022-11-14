<?php

namespace Nabin\Mswm;

class Modification {
	public static ?Modification $instance = null;

	public static function getInstance(): ?Modification {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		add_action( 'woocommerce_before_single_product_summary', [ $this, 'dummy_text_callback' ], 5 );
		remove_action( 'woocommerce_single_product_summary', [ $this, 'woocommerce_template_single_meta' ], 40 );
		add_filter( 'woocommerce_product_tabs', [ $this, 'woo_new_product_tab' ] );
	}

	function dummy_text_callback() {
		echo "This is a dummy store";
	}

	function woo_new_product_tab( $tabs ) {

		$tabs['location'] = array(
			'title'    => __( 'Location', 'woocommerce' ),
			'priority' => 10,
			'callback' => [$this, 'location_tab_callback_function']
		);

		return $tabs;
	}

	function location_tab_callback_function() {
		echo get_field( 'store_location' );
	}

}