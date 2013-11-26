<?php
namespace livestreambadger;

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
	        $backup = get_option( self::STREAMS_BACKUP, array() ); 
	        try {
	            $streams = $this->sync->sync( $backup );
	            set_transient( self::STREAMS_TRANSIENT, $streams, 30);
	            update_option( self::STREAMS_BACKUP, $streams);
	        } catch (LSB_API_Call_Exception $api_call_ex) {
	            $streams = $backup;
	        }
	    }
	    return $streams;
	}
	
}