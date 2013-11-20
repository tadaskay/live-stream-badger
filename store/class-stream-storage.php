<?php

/**
 * Provides storage for Stream infos (from widgets)
 */
class LSB_Stream_Storage {

	const KEY = 'lsb-stream-storage';

	function store( $stream_infos ) {
		update_option( self::KEY, $stream_infos );
	}

	function load() {
		return get_option( self::KEY, array() );
	}

}