<?php

include_once ( 'class-api.php' );
include_once ( 'class-api-twitch.php' );
include_once LSB_PLUGIN_BASE_URL . '/domain/domain-core.php';

/**
 * API Core provides access to all Live Stream APIs.
 */
class LSB_API_Core {

	private $apis = array();

	/**
	 * Put all supported APIs here
	 */
	function __construct() {
		$this->register_api( new LSB_API_Twitch() );
	}

	/**
	 * Queries a list of URLs from mixed APIs.
	 *
	 * @param array $stream_urls List of valid LSB_Stream_URL
	 *
	 * @return array List of LSB_Stream_Info
	 */
	function query( $stream_urls ) {

		// Group URLs by API so we can call them separately
		$stream_urls_by_api = array();
		foreach ( $stream_urls as $stream_url ) {
			/** @var $stream_url LSB_Stream_URL */
			if ( !isset( $stream_urls_by_api[$stream_url->api_id] ) ) {
				$stream_urls_by_api[$stream_url->api_id] = array();
			}
			$stream_urls_by_api[$stream_url->api_id][] = $stream_url;
		}

		// Call each API
		$results_all = array();
		foreach ( $stream_urls_by_api as $api_identifier => $stream_urls ) {
			/** @var $current_api LSB_API */
			$current_api              = $this->apis[$api_identifier];
			$results_from_current_api = $current_api->query( $stream_urls );
			$results_all              = array_merge( $results_all, $results_from_current_api );
		}

		return $results_all;
	}

	/**
	 * Cleans URLs and checks if they are supported by any registered API.
	 * Returned list contains LSB_Stream_URL only for valid URLs.
	 *
	 * @param array $urls List of string URLs
	 *
	 * @return array List of validated LSB_Stream_URL
	 */
	function validate_urls( $urls ) {
		$all_stream_urls = array();

		foreach ( $urls as $url ) {
			foreach ( $this->apis as $api ) {
				/** @var $api LSB_API */
				$stream_url = $api->validate_url( $url );
				if ( !empty( $stream_url ) ) {
					$all_stream_urls[] = $stream_url;
					break; // Skip to the next URL
				}
			}
		}

		return $all_stream_urls;
	}

	/**
	 * Registers API for use in API Core.
	 *
	 * @param LSB_API $api
	 */
	function register_api( $api ) {
		$this->apis[$api->get_api_identifier()] = $api;
	}

}

//eof