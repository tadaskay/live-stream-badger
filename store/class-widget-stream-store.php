<?php

/**
 * Provides storage for Stream infos (from widgets)
 */
class LSB_Widget_Stream_Store {

	const KEY = 'lsb-widget-stream-store';

	/**
	 * @param array $stream_infos
	 */
	function store( $stream_infos ) {
		update_option( self::KEY, $stream_infos );
		set_transient( self::KEY, $stream_infos );
	}

	/**
	 * Loads stream infos either from a transient or an option
	 *
	 * @return mixed|void array of stream infos
	 */
	function load() {
		$t = get_transient( self::KEY );
		if ( $t === FALSE ) {
			$t = get_option( self::KEY, array() );
			set_transient( self::KEY, $t );
		}
		return $t;
	}

}