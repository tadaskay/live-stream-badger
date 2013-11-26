<?php
namespace livestreambadger;

/**
 * General Live stream information
 */
class LSB_Stream {

    /**
	 * Summary information (URL, API ID, Channel name, etc.), not tied to the stream statistics.
	 * @var LSB_Stream_Summary
	 */
	public $summary;

    /**
     * Is stream online?
     * @var boolean
     */
    public $live = false;

	/**
	 * Amount of channel viewers at the moment, when $live is true. Otherwise undefined.
	 * @var int
	 */
	public $watching_now = -1;
	
	/**
	 * Static channel image URL
	 * @var string
	 */
	public $image_url = '';

    /**
     * Screen capture (live preview) image URL
     * @var string
     */
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