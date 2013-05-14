<?php


/**
 * General Live stream information
 */
class LSB_Stream_Info {

	/**
	 * Original URL entered by user
	 *
	 * @var string
	 */
	public $original_url = '';

	/**
	 * Validated URL
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * Channel name of the stream, normally channel name is the last part of URL.
	 * E.g. http://www.twitch.tv/beyondthesummit -> channel name is 'beyondthesummit'
	 *
	 * @var string
	 */
	public $channel_name = '';

	/**
	 * Count of people watching the channel at the moment.
	 * -1 indicates that the stream is offline, 0 indicates the stream is online but no viewers.
	 *
	 * @var int
	 */
	public $watching_now = -1;

	/**
	 * URL to the channel image
	 *
	 * @var string
	 */
	public $image_url = '';

	/**
	 * API ID
	 *
	 * @var string
	 */
	public $api_id = '';

	/**
	 * Builds a unique stream ID (API ID + channel name)
	 *
	 * @param string $api_id
	 * @param string $channel_name
	 *
	 * @return string
	 */
	static function make_stream_id( $api_id, $channel_name ) {
		return $api_id . '_' . $channel_name;
	}

	static function sort_by_watching_now( $la, $lb ) {
		$count_a = (int) $la->watching_now;
		$count_b = (int) $lb->watching_now;

		if ( $count_a == $count_b )
			return 0;

		$natural = ( $count_a > $count_b ) ? 1 : -1;
		return ( -1 ) * $natural;
	}
}

//eof