<?php
namespace livestreambadger;

/**
 * Provides storage for Stream infos (from widgets)
 */
class LSB_Stream_Storage {

	/** @var LSB_Sync */
	private $sync;

    function __construct( $sync ) {
        $this->sync = $sync;
    }

	/**
	 * Gets latest stream list. Synchronizes if needed.
	 */
	function get_streams() {
	    $streams = get_transient( WP_Options::STREAMS_TRANSIENT );
	    if ( $streams === false ) {
	        $backup = get_option( WP_Options::STREAMS_BACKUP, array() ); 
	        try {
	            $streams = $this->sync->sync( $backup );
	            $cache_time_setting = Settings::read_settings( 'cache_time' );
	            if ( $cache_time_setting != 0 ) {
	                set_transient( WP_Options::STREAMS_TRANSIENT, $streams, $cache_time_setting );
	            }
	            update_option( WP_Options::STREAMS_BACKUP, $streams);
	        } catch (LSB_API_Call_Exception $api_call_ex) {
	        	lsbdebug( $api_call_ex );
	            $streams = $backup;
	        }
	    }
	    return $streams;
	}
	
	function reset() {
	    delete_transient( WP_Options::STREAMS_TRANSIENT );
	}
	
}