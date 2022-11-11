<?php

namespace nabin\mswm;

final class Bootstrap {
	const MINIMUM_PHP_VERSION = '7.4';

	/**
	 * Autoload - PSR 4 Compliance
	 */
	public function autoloader() {
		require_once MSWM_ROOT_DIR_PATH . 'vendor/autoload.php';
	}
}
