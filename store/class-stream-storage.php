<?php

/**
 * Provides storage for Stream infos (from widgets)
 */
class LSB_Stream_Storage {

	const KEY = 'lsb-stream-storage';

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

	function clear() {
		delete_transient(self::KEY);
		delete_option(self::KEY);
	}

}