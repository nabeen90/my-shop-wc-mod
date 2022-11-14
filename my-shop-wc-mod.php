<?php

/**
 * Plugin Name
 *
 * @author            Nabin Adhikari
 * @copyright         2022 Nabin Adhikari
 * @license           GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       My Shop Wc Mod
 * Plugin URI:        https://example.com/plugin-name
 * Description:       Description of the plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Nabin Adhikari
 * Author URI:        https://example.com
 * Text Domain:       my-shop-wc-mod
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! defined( 'MSWM_VERSION' ) ) {
	define( 'MSWM_VERSION', '1.0.0' );
}
if ( ! defined( 'MSWM_FILE_PATH' ) ) {
	define( 'MSWM_FILE_PATH', __FILE__ );
}

if ( ! defined( 'MSWM_ROOT_DIR_PATH' ) ) {
	define( 'MSWM_ROOT_DIR_PATH', DIRNAME( __FILE__ ) );
}

if ( ! defined( 'MSWM_ROOT_URI_PATH' ) ) {
	define( 'MSWM_ROOT_URI_PATH', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'MSWM_BASE_FILE' ) ) {
	define( 'MSWM_BASE_FILE', plugin_basename( __FILE__ ) );
}

require_once MSWM_ROOT_DIR_PATH . '/includes/Bootstrap.php';