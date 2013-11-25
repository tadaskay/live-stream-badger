<?php

/**
 * Provides storage for Stream infos (from widgets)
 */
class LSB_Stream_Storage {

	const STREAMS_TRANSIENT = 'live-stream-badger:streams';
	const STREAMS_BACKUP    = 'live-stream-badget:streams-backup';
	
	/** @var LSB_Sync */
	private $sync;

    function __construct( $sync ) {
        $this->sync = $sync;
    }

	/**
	 * Gets latest stream list. Synchronizes if needed.
	 */
	function get_streams() {
	    $streams = get_transient( self::STREAMS_TRANSIENT );
	    if ( $streams === false ) {
	        error_log('transient expired');
	        $backup = get_option( self::STREAMS_BACKUP, array() ); 
	        error_log('backup:'.print_r($backup, true));
	        try {
	            $streams = $this->sync->sync( $backup );
	            set_transient( self::STREAMS_TRANSIENT, $streams, 30);
	            update_option( self::STREAMS_BACKUP, $streams);
	            error_log('new store data:'.print_r($streams, true));
	        } catch (LSB_API_Call_Exception $api_call_ex) {
	            error_log('Could not update data from API, using backup...', $api_call_ex);
	            $streams = $backup;
	        }
	    } else {
	        error_log('transient valid');
	        error_log('store:'.print_r($streams, true));
	    }
	    return $streams;
	}
	
}