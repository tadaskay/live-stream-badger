<?php

/**
 * An abstract LiveStream API, allowing to query information.
 */
abstract class LSB_API {

	/**
	 * Executes query for the given stream summaries.
	 *
	 * @param array $stream_summaries List of LSB_Stream_Summary to query, validated
	 *
	 * @return array List of LSB_Stream
	 */
	function query( $stream_summaries ) {

		// Build channel names for query
		$channel_names = array();
		foreach ( $stream_summaries as $stream_summary ) {
			/** @var $url LSB_Stream_Summary */
			$channel_names[] = $stream_summary->channel_name;
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

		$streams = $this->map_results( $response );

		// Map original URL (for malformed URLs where url != original_url)
		foreach ( $streams as $stream ) {
			/** @var $stream LSB_Stream */
			foreach ( $stream_summaries as $stream_summary ) {
				/** @var $url LSB_Stream_Summary */
				if ( $stream->summary->url == $stream_summary->url ) {
					$stream->summary->original_url = $stream_summary->original_url;
					break; // Go to the next Stream
				}
			}
		}

		return $streams;
	}

	/**
	 * Checks if API supports given URL - if supported, returns LSB_Stream_Summary.
	 *
	 * @param string $url
	 *
	 * @return LSB_Stream_Summary
	 */
	function validate_url( $url ) {
		$original_url = $url;

		$url = $this->format_url( $url );
		if ( empty( $url ) )
			return;

		$channel_name = $this->url_to_channel( $url );
		if ( empty( $channel_name ) )
			return;

		$summary = new LSB_Stream_Summary();

		$summary->original_url = $original_url;
		$summary->url          = $url;
		$summary->channel_name = $channel_name;
		$summary->api_id       = $this->get_api_identifier();

		return $summary;
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
	 * Maps API response to a list of {@link LSB_Stream}.
	 *
	 * @param $result_string
	 *
	 * @return array
	 */
	abstract protected function map_results( $result_string );

}

//eof
