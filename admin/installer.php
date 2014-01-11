<?php
namespace livestreambadger;

class LSB_Installer {

    function __construct() {
        // Activation/deactivation hooks work only from plugin's main file
    }

    function install() {
        delete_option( WP_Options::STREAMS_BACKUP );
        delete_transient( WP_Options::STREAMS_TRANSIENT );

        $previous_version = get_option( WP_Options::PLUGIN_VERSION ) ?: '1.3';
        $this->upgrade( $previous_version, LSB_PLUGIN_VERSION );
        update_option( WP_Options::PLUGIN_VERSION, LSB_PLUGIN_VERSION );
    }
    
    function upgrade( $from, $to ) {
        if ( $from === '1.3' || $from === '1.4' || $from === '1.4.1' || $from === '1.4.2' ) {
            wp_clear_scheduled_hook( 'lsb_update_all_stream_status' );
            delete_option('lsb-stream-storage');
            delete_transient('lsb-stream-storage');
        }
    }

    function uninstall() {
        delete_option( WP_Options::STREAMS_BACKUP );
        delete_transient( WP_Options::STREAMS_TRANSIENT );
    }

}