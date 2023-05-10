<?php

namespace Curtis;
use WP_CLI;
use WP_CLI_Command;

/**
 * Forces the cached API data to be deleted immediately, resulting in fresh data on next API call
 *
 * ## EXAMPLES
 *
 *     wp curtis force_refresh
 *
 * @when after_wp_load
 */
class Commands extends WP_CLI_Command {

	public function force_refresh() {
		$result = WP_CLI::runcommand( 'transient delete api_cache', array( 'return' => 'all' ) );
		if ( $result->stderr ) {
			WP_CLI::success( "Cache already expired, Next API call will fetch new data!" );
		}
		if ( $result->stdout ) {
			WP_CLI::success( "Next API call will fetch new data!" );
		}
	}
}
