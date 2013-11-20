<?php

class LSB_Installer {

    function __construct() {
        // Activation/deactivation hooks work only from plugin's main file
    }

    function install() {
        $this->health_check();
        wp_schedule_single_event( time(), 'lsb_update_all_stream_status' );
        wp_schedule_event( time(), 'lsb_five_minutes', 'lsb_update_all_stream_status' );

        delete_option('lsb-stream-storage');
        delete_transient('lsb-stream-storage');
    }

    function uninstall() {
        wp_clear_scheduled_hook( 'lsb_update_all_stream_status' );

        delete_option('lsb-stream-storage');
        delete_transient('lsb-stream-storage');
    }

    function health_check() {
        global $wp_version;
        if ( version_compare( $wp_version, '3.5', '<' ) ) {
            $antique_wp_version_message = 'Live Stream Badger requires WordPress 3.5 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update.</a>';
            exit( $antique_wp_version_message );
        }
        $php_version = phpversion();
        if ( version_compare( $php_version, '5.2', '<' ) ) {
            $antique_php_version_message = 'Live Stream Badger requires PHP 5.2 or newer. Please inquiry your hosting provider for an upgrade.';
            exit ( $antique_php_version_message );
        }
        if ( !wp_http_supports() ) {
            $no_transport_message = 'No HTTP transport (curl, streams, fsockopen) is available. Please inquiry your hosting provider for an upgrade.';
            exit ( $no_transport_message );
        }
    }
}