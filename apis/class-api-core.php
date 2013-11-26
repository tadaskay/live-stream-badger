<?php
namespace livestreambadger;

/**
 * API Core provides access to all Live Stream APIs.
 */
class LSB_API_Core {

	private $apis = array();

	/**
	 * Put all supported APIs here
	 */
	function __construct() {
		//$this->register_api( new LSB_API_Twitch() );
		$this->register_api( new LSB_Twitch_API_V3() );
	}

	/**
	 * Gets streams from all registered APIs
	 * 
	 * @param array $stream_summaries Validated stream summaries
	 * 
	 * @return array Streams
	 */
	function get_streams( $stream_summaries = array() ) {
        // Group URLs by API so we can call them separately
        $summaries_by_api = array();
        foreach ( $stream_summaries as $summary ) {
            /** @var $summary LSB_Stream_Summary */
            if ( !isset( $summaries_by_api[$summary->api_id] ) ) {
                $summaries_by_api[$summary->api_id] = array();
            }
            $summaries_by_api[$summary->api_id][] = $summary;
        }
        
        // Call each API
        $results_all = array();
        foreach ( $summaries_by_api as $api_identifier => $stream_summaries ) {
            /** @var $current_api LSB_API */
            $current_api              = $this->apis[$api_identifier];

            $channels = array_map( function($ss) { return $ss->channel_name; }, $stream_summaries );
            $results_from_current_api = $current_api->get_streams( $channels );

            $results_all              = array_merge( $results_all, $results_from_current_api );
        }
        
        return $results_all;
	}

	/**
	 * Cleans URLs and checks if they are supported by any registered API.
	 * Returned list contains LSB_Stream_Summary only for valid URLs.
	 *
	 * @param array $urls List of string URLs
	 *
	 * @return array List of validated LSB_Stream_Summary
	 */
	function validate_urls( $urls ) {
		$stream_summaries = array();

		foreach ( $urls as $url ) {
			foreach ( $this->apis as $api ) {
				/** @var $api LSB_API */
				$stream_summary = $api->validate_url( $url );
				if ( !empty( $stream_summary ) ) {
					$stream_summaries[] = $stream_summary;
					break; // Skip to the next URL
				}
			}
		}

		return $stream_summaries;
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