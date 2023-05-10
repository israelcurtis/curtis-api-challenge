<?php
/**
 * Plugin Name:       Israel Curtis API Plugin Challenge
 * Description:       Application submission for the position of Awesome Motive WordPress Developer
 * Requires at least: 5.9
 * Requires PHP:      7.2
 * Version:           1.0.0
 * Author:            Israel Curtis
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       curtis-api
 */

/**
 * Autoloader
 *
 * @since 1.0.0
 *
 * @param string $class The fully-qualified class name.
 */
spl_autoload_register( function ( $class ) {
	list( $prefix ) = explode( "\\", $class );
	if ( $prefix !== "Curtis" ) {
		return;
	}
	$base_dir = __DIR__ . "/includes/";
	$relative_class = substr( $class, strlen( $prefix ) + 1 );
	$file = wp_normalize_path( $base_dir . $relative_class . ".php" );
	if ( is_readable( $file ) ) {
		require_once $file;
	}
});

/**
 * Provides singleton-style instance for the Core class
 *
 * @since 1.0.0
 *
 * @return Curtis\Core
 */
function curtis_api_challenge() {
	/**
	 * @var \Curtis\Core
	 */
	static $core;

	if ( !isset( $core ) ) {
		$core = new \Curtis\Core();
	}
	return $core;
}

curtis_api_challenge();
