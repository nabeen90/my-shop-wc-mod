<?php

namespace Nabin\Mswm;

final class Bootstrap {
	const MINIMUM_PHP_VERSION = '7.4';

	public static ?Bootstrap $_instance = null;

	/**
	 * @return Bootstrap|null
	 */
	public static function getInstance(): ?Bootstrap {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		$this->autoload();
		add_action( 'plugin_loaded', [ $this, 'initPlugin' ] );
	}

	public function pluginActivated() {
		//other plugins can get this option and check if plugin is activated
		update_option( 'MY_SHOP_WC_MOD_plugin_activate', 'activated' );
	}

	public function pluginDeactivated() {
		delete_option( 'MY_SHOP_WC_MOD_plugin_activate' );
	}

	/**
	 * Autoload - PSR 4 Compliance
	 */
	private function autoload() {
		require_once MSWM_ROOT_DIR_PATH . '/vendor/autoload.php';
	}

	/**
	 * @param $plugin
	 *
	 * @return bool
	 */
	private function is_plugin_active( $plugin ): bool {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
	}

	/**
	 * @return bool
	 */
	private function checkDependencies(): bool {
		$passed = true;
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			$this->message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
				esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'my-shop-wc-mod' ),
				'<strong>' . esc_html__( 'My Shop Wc Mod', 'my-shop-wc-mod' ) . '</strong>',
				'<strong>' . esc_html__( 'PHP', 'my-shop-wc-mod' ) . '</strong>',
				self::MINIMUM_PHP_VERSION
			);
			add_action( 'admin_notices', [ $this, 'add_admin_notice' ] );

			return false;
		}

		return $passed;
	}

	public function add_admin_notice() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		printf( '<div class="notice notice-error is-dismissible"><p>%1$s</p></div>', $this->message );
	}

	public function initPlugin() {
		$dependenciesPassed = $this->checkDependencies();
		if ( ! $dependenciesPassed ) {
			return;
		}

		Modification::getInstance();
	}
}

Bootstrap::getInstance();
