<?php
/**
 * QR Code Creator
 *
 * A WordPress plugin which will help you to create QR Codes.
 *
 * @package           QRCodeCreator
 * @author            Dhanendran Rajagopal
 * @copyright         2024 Dhanendran Rajagopal
 * @license           GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       QR Code Creator
 * Plugin URI:        https://github.com/dhanendran/qr-code-creator
 * Description:       A WordPress plugin which will help you to create QR Codes.
 * Short Description: Create customizable QR codes with size, color, and error correction options.
 * Version:           0.2.0
 * Author:            Dhanendran Rajagopal
 * Author URI:        https://dhanendranrajagopal.me/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       qr-code-creator
 * Domain Path:       /languages
 * Requires at least: 4.4
 * Tested up to:      7.0
 * Requires PHP:      7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Plugin version constant.
 *
 * @since 0.2.0
 */
define( 'QR_CODE_CREATOR_VERSION', '0.2.0' );

/**
 * Plugin directory path.
 *
 * @since 0.2.0
 */
define( 'QR_CODE_CREATOR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 *
 * @since 0.2.0
 */
define( 'QR_CODE_CREATOR_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class.
 *
 * @since 0.1.0
 */
class QRCodeCreator {

	/**
	 * Plugin version.
	 *
	 * @since 0.2.0
	 * @var string
	 */
	private $version;

	/**
	 * Admin instance.
	 *
	 * @since 0.2.0
	 * @var QRCodeCreator_Admin
	 */
	private $admin;

	/**
	 * Initialize the plugin.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->version = QR_CODE_CREATOR_VERSION;
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load required dependencies.
	 *
	 * @since 0.2.0
	 */
	private function load_dependencies() {
		require_once QR_CODE_CREATOR_PATH . 'includes/class-qrcode-creator-admin.php';
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 0.2.0
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		if ( is_admin() ) {
			$this->admin = new QRCodeCreator_Admin( $this->version );
		}
	}

	/**
	 * Activation hook.
	 *
	 * @since 0.2.0
	 */
	public function activate() {
		// Add activation logic here if needed.
	}

	/**
	 * Deactivation hook.
	 *
	 * @since 0.2.0
	 */
	public function deactivate() {
		// Add deactivation logic here if needed.
	}
}

/**
 * Initialize the plugin.
 *
 * @since 0.1.0
 */
function qr_code_creator_init() {
	new QRCodeCreator();
}
qr_code_creator_init();


