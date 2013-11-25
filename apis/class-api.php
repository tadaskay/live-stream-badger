<?php
namespace livestreambadger;

/**
 * An abstract LiveStream API, allowing to query information.
 */
abstract class LSB_API {

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

	protected function format_url( $url ) {
		$url = strtolower( trim( esc_url( $url ) ) );
		return $url;
	}

	/**
	 * Converts URL to a Channel name (assumes valid URL)
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	abstract protected function url_to_channel( $url );

}