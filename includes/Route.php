<?php

namespace Curtis;


/**
 * Sets up REST endpoint to output data sourced from an external API
 * Caches the API response for 1 hour, no matter how many REST requests it receives
 * (cache can be overridden with WP CLI command)
 *
 * @since 1.0.0
 */
class Route {

	/**
	* URL provided by Awesome Motive to a publicly accessible API endpoint
	*
	* @since 1.0.0
	*
	* @var string
	* returns array JSON data to populate a table
	*/
	protected $api_url = 'https://miusage.com/v1/challenge/1/';

	/**
	* Core constructor
	*
	* @since 1.0.0
	*/
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'init' ] );
	}

	public function init() {
		register_rest_route( 'curtis/v1', '/apicall', [
			'methods' => 'GET',
			'callback' => [ $this, 'api_call' ],
		] );
	}


	/**
	* External API caller
	*
	* @since    1.0.0
	*
	* @return array $body_response JSON data direct from external API response
	* @return array $api_cache JSON data cached in transient with 1 hour expiration
	*/
	public function api_call() {

		// check for cached data first
		$api_cache = get_transient( 'api_cache' );

		// make a new API call if cache expired or missing
		if ( empty( $api_cache ) ) {

			$response = wp_remote_get( esc_url_raw( $this->api_url ), array() );
			$body_response = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			if ( !is_array( $body_response ) || empty( $body_response ) ) {
				return new WP_Error('empty_response', __( "No valid API response!", "curtis-api" ) );
			}

			// save API response to transient cache
			set_transient( "api_cache", $body_response, HOUR_IN_SECONDS );

			// helpful status information to determine if the data returned by our REST endpoint was cached or not
			$body_response[ "status" ] = "NOT CACHED";
			return $body_response;
		}
		$api_cache[ "status" ] = "CACHED";
		return $api_cache;
	}
}
