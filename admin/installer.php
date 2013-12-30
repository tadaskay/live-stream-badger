<?php
namespace livestreambadger;

class LSB_Installer {

    function __construct() {
        // Activation/deactivation hooks work only from plugin's main file
    }

    function install() {
        $this->health_check();

        delete_option('live-stream-badger:streams-backup');
        delete_transient('live-stream-badger:streams');

        // cleanup legacy options <= 1.3
        wp_clear_scheduled_hook( 'lsb_update_all_stream_status' );
        delete_option('lsb-stream-storage');
        delete_transient('lsb-stream-storage');
    }

    function uninstall() {
        delete_option('live-stream-badger:streams-backup');
        delete_transient('live-stream-badger:streams');

        // cleanup legacy options <= 1.3
        wp_clear_scheduled_hook( 'lsb_update_all_stream_status' );
        delete_option('lsb-stream-storage');
        delete_transient('lsb-stream-storage');
    }

    function health_check() {
        global $wp_version;
        if ( version_compare( $wp_version, '3.7', '<' ) ) {
            $antique_wp_version_message = 'Live Stream Badger requires WordPress 3.7 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update.</a>';
            exit( $antique_wp_version_message );
        }
        $php_version = phpversion();
        if ( version_compare( $php_version, '5.3', '<' ) ) {
            $antique_php_version_message = 'Live Stream Badger requires PHP 5.3 or newer. Please inquiry your hosting provider for an upgrade.';
            exit ( $antique_php_version_message );
        }
        if ( !wp_http_supports() ) {
            $no_transport_message = 'No HTTP transport (curl, streams, fsockopen) is available. Please inquiry your hosting provider for an upgrade.';
            exit ( $no_transport_message );
        }
    }
}