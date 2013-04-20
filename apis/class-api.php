<?php

/**
 * An abstract LiveStream API, allowing to query information.
 */
abstract class LSB_API {

	/**
	 * Executes query for all given URLs.
	 *
	 * @param array $urls List of LSB_Stream_URL to query, validated
	 *
	 * @return array List of LSB_Stream_Info
	 */
	function query( $urls ) {

		// Build channel names for query
		$channel_names = array();
		foreach ( $urls as $url ) {
			/** @var $url LSB_Stream_URL */
			$channel_names[] = $url->channel_name;
		}

		if ( empty( $channel_names ) )
			return array();

		// Create query and execute
		$query_string = $this->create_query_string( $channel_names );
		$response     = $this->query_execute( $query_string );

		// Empty result list or an error
		// TODO improve error handling?
		if ( empty( $response ) )
			return array();

		$stream_infos = $this->map_results( $response );

		// Map original URL (for malformed URLs where url != original_url)
		foreach ( $stream_infos as $s ) {
			/** @var $s LSB_Stream_Info */
			foreach ( $urls as $url ) {
				/** @var $url LSB_Stream_URL */
				if ( $s->url == $url->url ) {
					$s->original_url = $url->original_url;
					break; // Go to next Stream info
				}
			}
		}

		return $stream_infos;
	}

	/**
	 * Checks if API supports given URL - if supported, returns LSB_Stream_URL.
	 *
	 * @param string $url
	 *
	 * @return LSB_Stream_URL
	 */
	function validate_url( $url ) {
		$original_url = $url;

		$url = $this->format_url( $url );
		if ( empty( $url ) )
			return;

		$channel_name = $this->url_to_channel( $url );
		if ( empty( $channel_name ) )
			return;

		$stream_url = new LSB_Stream_URL();

		$stream_url->original_url = $original_url;
		$stream_url->url          = $url;
		$stream_url->channel_name = $channel_name;
		$stream_url->api_id       = $this->get_api_identifier();

		return $stream_url;
	}

	/**
	 * Returns API's unique identifier
	 *
	 * @return string
	 */
	abstract function get_api_identifier();

	//
	// PROTECTED - Used in common query workflow of APIs
	//

	/**
	 * Tries to format URL in an API specific way.
	 * E.g. Twitch API will format it into http://www.twitch.tv/channel
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	protected function format_url( $url ) {
		$url = strtolower( trim( esc_url( $url ) ) );
		return $url;
	}

	/**
	 * Executes query to API via HTTP
	 *
	 * @param string $query_string URL to query API
	 *
	 * @return string Response body
	 */
	private function query_execute( $query_string ) {
		$response_raw  = wp_remote_get( $query_string );
		$response_body = wp_remote_retrieve_body( $response_raw );
		return !empty( $response_body ) ? $response_body : '';
	}

	/**
	 * Converts URL to a Channel name (assumes valid URL)
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	abstract protected function url_to_channel( $url );

	/**
	 * Converts Channel name to an URL (assumes valid Channel name)
	 *
	 * @param string $channel
	 *
	 * @return string
	 */
	abstract protected function channel_to_url( $channel );

	/**
	 * Creates a query string using Channel names
	 *
	 * @param array $channel_names List of channel names
	 *
	 * @return string
	 */
	abstract protected function create_query_string( $channel_names );

	/**
	 * Maps API response to a list of {@link LSB_Stream_Info}.
	 *
	 * @param $result_string
	 *
	 * @return array
	 */
	abstract protected function map_results( $result_string );

}

//eof
