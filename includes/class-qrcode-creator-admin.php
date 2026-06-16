<?php
/**
 * Admin functionality for QR Code Creator.
 *
 * @package    QRCodeCreator
 * @subpackage Admin
 * @since      0.2.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Admin class.
 *
 * @since 0.2.0
 */
class QRCodeCreator_Admin {

	/**
	 * Plugin version.
	 *
	 * @since 0.2.0
	 * @var string
	 */
	private $version;

	/**
	 * Plugin slug.
	 *
	 * @since 0.2.0
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Initialize the admin class.
	 *
	 * @since 0.2.0
	 * @param string $version Plugin version.
	 */
	public function __construct( $version ) {
		$this->version     = $version;
		$this->plugin_slug = 'qr-code-creator';

		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 0.2.0
	 */
	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add options page.
	 *
	 * @since 0.2.0
	 */
	public function add_plugin_page() {
		add_options_page(
			esc_html__( 'QR Code Creator', 'qr-code-creator' ),
			esc_html__( 'QR Code Creator', 'qr-code-creator' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Enqueue scripts and styles only on plugin admin page.
	 *
	 * @since 0.2.0
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_scripts( $hook ) {
		// Only load on our plugin page.
		if ( 'settings_page_' . $this->plugin_slug !== $hook ) {
			return;
		}

		// Enqueue styles.
		wp_enqueue_style(
			$this->plugin_slug . '-admin',
			QR_CODE_CREATOR_URL . 'style.css',
			array(),
			$this->version
		);

		// Enqueue scripts.
		wp_enqueue_script(
			$this->plugin_slug . '-admin',
			QR_CODE_CREATOR_URL . 'script.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		// Localize script with data.
		wp_localize_script(
			$this->plugin_slug . '-admin',
			'qrCodeCreator',
			array(
				'nonce'   => wp_create_nonce( 'qr_code_creator_nonce' ),
				'apiUrl'  => 'https://api.qrserver.com/v1/create-qr-code/',
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'i18n'    => array(
					'errorEmpty' => esc_html__( 'Please enter some content to generate QR code.', 'qr-code-creator' ),
					'errorApi'   => esc_html__( 'Error generating QR code. Please try again.', 'qr-code-creator' ),
					'loading'    => esc_html__( 'Generating QR code...', 'qr-code-creator' ),
					'success'    => esc_html__( 'QR code generated successfully!', 'qr-code-creator' ),
					'download'   => esc_html__( 'Download:', 'qr-code-creator' ),
				),
			)
		);
	}

	/**
	 * Options page callback.
	 *
	 * @since 0.2.0
	 */
	public function create_admin_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'qr-code-creator' ) );
		}

		?>
		<div class="wrap qr-code-creator-admin">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="qr-code-creator-container">
				<div class="qr-code-creator-form">
					<h2><?php esc_html_e( 'Create QR Code', 'qr-code-creator' ); ?></h2>

					<form id="qr_code_creator_form" method="post">
						<?php wp_nonce_field( 'qr_code_creator_action', 'qr_code_creator_nonce' ); ?>

						<table class="form-table" role="presentation">
							<tbody>
								<tr>
									<th scope="row">
										<label for="qr_code_content">
											<?php esc_html_e( 'Content', 'qr-code-creator' ); ?>
										</label>
									</th>
									<td>
										<textarea
											name="qr_code_content"
											id="qr_code_content"
											rows="10"
											cols="70"
											class="large-text"
											placeholder="<?php esc_attr_e( 'Enter your content here (text, URL, etc.)', 'qr-code-creator' ); ?>"
											required
										></textarea>
										<p class="description">
											<?php esc_html_e( 'Enter the text, URL, or other content you want to encode in the QR code.', 'qr-code-creator' ); ?>
										</p>
									</td>
								</tr>

								<tr>
									<th scope="row">
										<label for="qr_code_size">
											<?php esc_html_e( 'Size', 'qr-code-creator' ); ?>
										</label>
									</th>
									<td>
										<select name="qr_code_size" id="qr_code_size">
											<option value="150x150"><?php esc_html_e( 'Small (150x150)', 'qr-code-creator' ); ?></option>
											<option value="200x200" selected><?php esc_html_e( 'Medium (200x200)', 'qr-code-creator' ); ?></option>
											<option value="250x250"><?php esc_html_e( 'Large (250x250)', 'qr-code-creator' ); ?></option>
											<option value="300x300"><?php esc_html_e( 'Extra Large (300x300)', 'qr-code-creator' ); ?></option>
											<option value="500x500"><?php esc_html_e( 'Huge (500x500)', 'qr-code-creator' ); ?></option>
										</select>
										<p class="description">
											<?php esc_html_e( 'Select the size of the QR code image.', 'qr-code-creator' ); ?>
										</p>
									</td>
								</tr>

								<tr>
									<th scope="row">
										<label for="qr_code_ecc">
											<?php esc_html_e( 'Error Correction', 'qr-code-creator' ); ?>
										</label>
									</th>
									<td>
										<select name="qr_code_ecc" id="qr_code_ecc">
											<option value="L"><?php esc_html_e( 'Low (~7%)', 'qr-code-creator' ); ?></option>
											<option value="M" selected><?php esc_html_e( 'Medium (~15%)', 'qr-code-creator' ); ?></option>
											<option value="Q"><?php esc_html_e( 'Quartile (~25%)', 'qr-code-creator' ); ?></option>
											<option value="H"><?php esc_html_e( 'High (~30%)', 'qr-code-creator' ); ?></option>
										</select>
										<p class="description">
											<?php esc_html_e( 'Higher error correction allows the QR code to be readable even if partially damaged.', 'qr-code-creator' ); ?>
										</p>
									</td>
								</tr>

								<tr>
									<th scope="row">
										<label for="qr_code_color">
											<?php esc_html_e( 'Foreground Color', 'qr-code-creator' ); ?>
										</label>
									</th>
									<td>
										<input
											type="color"
											name="qr_code_color"
											id="qr_code_color"
											value="#000000"
										>
										<p class="description">
											<?php esc_html_e( 'Select the color for the QR code pattern.', 'qr-code-creator' ); ?>
										</p>
									</td>
								</tr>

								<tr>
									<th scope="row">
										<label for="qr_code_bgcolor">
											<?php esc_html_e( 'Background Color', 'qr-code-creator' ); ?>
										</label>
									</th>
									<td>
										<input
											type="color"
											name="qr_code_bgcolor"
											id="qr_code_bgcolor"
											value="#FFFFFF"
										>
										<p class="description">
											<?php esc_html_e( 'Select the background color for the QR code.', 'qr-code-creator' ); ?>
										</p>
									</td>
								</tr>
							</tbody>
						</table>

						<p class="submit">
							<button
								type="button"
								id="create_qr_code"
								class="button button-primary button-large"
							>
								<?php esc_html_e( 'Generate QR Code', 'qr-code-creator' ); ?>
							</button>
							<button
								type="button"
								id="reset_qr_code"
								class="button button-secondary button-large"
							>
								<?php esc_html_e( 'Reset', 'qr-code-creator' ); ?>
							</button>
						</p>
					</form>
				</div>

				<div class="qr-code-creator-result">
					<h2><?php esc_html_e( 'QR Code', 'qr-code-creator' ); ?></h2>

					<div id="qr_code_loading" class="qr-code-loading" style="display: none;">
						<span class="spinner is-active"></span>
						<p><?php esc_html_e( 'Generating QR code...', 'qr-code-creator' ); ?></p>
					</div>

					<div id="qr_code_error" class="qr-code-error notice notice-error" style="display: none;">
						<p></p>
					</div>

					<div id="qr_code_preview" class="qr-code-preview" style="display: none;">
						<img id="qr_code" alt="<?php esc_attr_e( 'QR Code', 'qr-code-creator' ); ?>" />
					</div>

					<div id="qr_code_download" class="qr-code-download" style="display: none;">
						<h3><?php esc_html_e( 'Download:', 'qr-code-creator' ); ?></h3>
						<div id="qr_code_download_link" class="qr-code-download-links"></div>
					</div>
				</div>
			</div>

			<div class="qr-code-creator-info">
				<h3><?php esc_html_e( 'About', 'qr-code-creator' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Link to API documentation */
						esc_html__( 'This plugin uses the %s service to create QR codes. As per their terms of service, your data is not stored.', 'qr-code-creator' ),
						'<a href="https://goqr.me/api/" target="_blank" rel="noopener noreferrer">goqr.me API</a>'
					);
					?>
				</p>
			</div>
		</div>
		<?php
	}
}

