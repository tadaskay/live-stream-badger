<?php
namespace livestreambadger;

/**
 * This class contains all options stored in WP tables by this plugin (options, transients).
 * Note that transients shall have additional entries (created by WP on its own).
 */
class WP_Options {
    
    const PLUGIN_VERSION    = 'live-stream-badger:version';

    // Stream storage
    const STREAMS_TRANSIENT = 'live-stream-badger:streams';
	const STREAMS_BACKUP    = 'live-stream-badger:streams-backup';
	
	// Admin settings
	const OPTIONS_GROUP     = 'live-stream-badger:options';
	
}