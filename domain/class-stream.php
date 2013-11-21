<?php

include_once 'class-stream-summary.php';

/**
 * General Live stream information
 */
class LSB_Stream {

	/**
	 * Summary information (URL, API ID, Channel name, etc.),
	 * not tied to the stream statistics.
	 *
	 * @var LSB_Stream_Summary
	 */
	public $summary;

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

	public $screen_cap_url = '';

	function __construct() {
		$this->summary = new LSB_Stream_Summary();
	}

	/**
	 * Compares two streams by 'Watching now' count, descending.
	 * Offline < 0 < 1
	 *
	 * @param $la
	 * @param $lb
	 *
	 * @return int
	 */
	static function sort_by_watching_now( $la, $lb ) {
		$count_a = (int) $la->watching_now;
		$count_b = (int) $lb->watching_now;

		if ( $count_a == $count_b )
			return 0;

		$natural = ( $count_a > $count_b ) ? 1 : -1;
		return ( -1 ) * $natural;
	}

	static function sort_by_status( $la, $lb ) {
		$count_a = $la->watching_now;
		$count_b = $lb->watching_now;

		if ( ( -1 == $count_a && -1 == $count_b ) || ( -1 != $count_a && -1 != $count_b ) )
			return 0;

		$natural = ( $count_a > $count_b ) ? 1 : -1;
		return ( -1 ) * $natural;
	}
	
	function to_string() {
	    return json_encode($this);
	}

}

//eof