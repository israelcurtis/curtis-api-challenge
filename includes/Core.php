<?

namespace Curtis;
use WP_CLI;
use Curtis\Table;

/**
 * Class Core to handle all plugin initialization.
 *
 * @since 1.0.0
 */
class Core {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	public $plugin_name = 'curtis-api';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	public $version = '1.0.0';

    /**
     * URL to plugin directory.
     *
     * @since 1.0.0
     *
     * @var string Without trailing slash.
     */
    public $plugin_url;

    /**
     * Path to plugin directory.
     *
     * @since 1.0.0
     *
     * @var string Without trailing slash.
     */
    public $plugin_path;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

        $this->plugin_url  = rtrim( plugin_dir_url( __DIR__ ), '/\\' );
        $this->plugin_path = rtrim( plugin_dir_path( __DIR__ ), '/\\' );

		$this->hooks();

        // initialize our CLI commands only if WPCLI installed
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
            WP_CLI::add_command( 'curtis', new \Curtis\Commands() );
		}
		// initialize our REST endpoint
		new \Curtis\Route();
	}


    /**
     * Assign all hooks
     *
     * @since 1.0.0
     */
    public function hooks() {
        add_action( 'init', [ $this, 'block_init' ] );
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
        add_action( 'admin_post_force_refresh_form', [ $this, 'force_refresh_response' ] );
    }


    /**
     * Enqueue stylesheets and scripts for the WordPress admin settings page
     */
    function admin_enqueue_scripts( $hook ) {
        if ( 'toplevel_page_curtis-api' == $hook ) {
			wp_enqueue_style( 'curtis-api-table', $this->plugin_url . '/includes/blocks/style-index.css', array(), $this->version, 'all' );
            wp_enqueue_style( 'curtis-api-settings', $this->plugin_url . '/includes/css/admin.css', array(), $this->version, 'all' );
        }
    }


	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @since    1.0.0
	 */
	public function block_init() {
		$block = register_block_type( __DIR__ . '/blocks' );
	}


	/**
	 * Add menu item for settings page
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		add_menu_page(
            'Curtis',
            'Curtis Challenge',
            'manage_options',
            'curtis-api',
            array( $this, 'admin_menu_page' ),
            'dashicons-cloud',
            80
        );
	}

	/**
	 * Output HTML content for settings page
	 *
	 * @since    1.0.0
	 */
	public function admin_menu_page() {

	$table = new \Curtis\Table();

    $nonce = wp_create_nonce( 'force_refresh_form_nonce' );
	?>
    <div class="wrap curtis-admin-page">
        <h1>
            <?php esc_html_e( 'Awesome Motive API Plugin Challenge', 'curtis-api' ); ?>
        </h1>
	<?php

	// admin notice display after form submit
	if ( isset( $_GET[ 'refreshed' ] ) ) {
		if ( 'true' === $_GET[ 'refreshed' ] ) { ?>
			<div class="notice-success notice is-dismissible">
				<p><?php esc_html_e( 'Success! Cache purged, the table data has been updated', 'curtis-api' ); ?></p>
			</div>
			<script>
				jQuery(document).ready(function($) {
					/* auto-hide the response notice after 5 seconds */
					$(".notice").delay(5000).fadeOut();
				});
			</script>
		<?php
		}
	}
	?>
		<div class="curtis-admin-page-content">
			<div class="curtis-admin-setting-row">
			<h2><?php esc_html_e( 'This plugin provides the following functionality', 'curtis-api' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'A REST endpoint that provides the data retrieved from your provided external API (caching results for an hour)', 'curtis-api' ); ?></li>
				<li><?php esc_html_e( 'A Gutenberg block, named "Israel Curtis API Challenge" which displays the retrieved data in a table, with settings to toggle columns', 'curtis-api' ); ?></li>
				<li><?php esc_html_e( 'A WP CLI command,', 'curtis-api' ); ?> <code>wp curtis force_refresh</code> <?php esc_html_e( 'to delete the API cache and ensure fresh data upon next call of the REST endpoint', 'curtis-api' ); ?></li>
				<li><?php esc_html_e( 'An admin page which displays the table, and provides a button to manually refresh the data', 'curtis-api' ); ?></li>
			</ul>
			</div>
			<div class="curtis-admin-setting-row">
				<div class="curtis-admin-setting-label">
					<label><?php esc_html_e( 'API Table Data', 'curtis-api' ); ?></label>
				</div>
				<div class="curtis-admin-setting-field">
					<div class="wp-block-curtis-api-challenge">
						<?php $table->renderTable(); ?>
					</div>
				</div>
			</div>
			<div class="curtis-admin-setting-row">
				<div class="curtis-admin-setting-label">
					<label><?php esc_html_e( 'Force Refresh', 'curtis-api' ); ?></label>
				</div>
				<div class="curtis-admin-setting-field">
						<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="force_refresh_form">
							<input type="hidden" name="action" value="force_refresh_form">
							<input type="hidden" name="force_refresh_form_nonce" value="<?php echo $nonce ?>" />
							<input type="submit" name="submit" id="submit" class="button button-primary button-large" value="<?php esc_html_e( 'Refresh Data', 'curtis-api' );?>">
						</form>
					<p class="desc">
						<?php esc_html_e( 'Click the button to purge the cache and retrieve new data from the API', 'curtis-api' ); ?>
					</p>
				</div>
			</div>
		</div>
    </div>
    <?php
	}


	/**
	 * Form submission handler for the settings page
	 * Clears the API cache and reloads settings page
	 *
	 * @since    1.0.0
	 */
	public function force_refresh_response() {
        if ( isset( $_POST[ 'force_refresh_form_nonce' ] ) && wp_verify_nonce( $_POST[ 'force_refresh_form_nonce' ], 'force_refresh_form_nonce' ) ) {
            delete_transient( 'api_cache' );
            wp_redirect( admin_url( '/admin.php?page=' . $this->plugin_name . '&refreshed=true' ) );
            exit;
        } else {
            wp_die( esc_html__( 'Invalid nonce specified', 'curtis-api' ), esc_html__( 'Error', 'curtis-api' ), array(
                    'response' 	=> 403,
                    'back_link' => 'admin.php?page=' . $this->plugin_name,
            ) );
        }
    }
}
