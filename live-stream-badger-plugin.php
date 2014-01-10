<?php
/*
 Plugin Name: Live Stream Badger
 Plugin URI: http://wordpress.org/extend/plugins/live-stream-badger/
 Description: Display status of Twitch.tv live streams
 Version: 1.4.1-dev
 Author: Tadas Krivickas
 Author URI: http://profiles.wordpress.org/tkrivickas
 Author email: tadas.krivickas@gmail.com
 License: GPLv3
 License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( !defined( 'LSB_PLUGIN_BASE' ) ) {
	define( 'LSB_PLUGIN_BASE', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'LSB_PLUGIN_BASENAME' ) ) {
    define( 'LSB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( !defined( 'LSB_PLUGIN_VERSION' ) ) {
    define( 'LSB_PLUGIN_VERSION', '1.4');
}

register_activation_hook( __FILE__, 'lsb_health_check' );
function lsb_health_check() {
    lsbdebug('Running health check in installer');
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

function lsbdebug( $what ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
        if ( is_array( $what ) ) {
            error_log( print_r( $what, true) );
        } else {
            error_log( $what );
        }
    }
}

$can_run = version_compare( phpversion(), '5.3', '>=' );
if ( !$can_run ) {
    return 0;
}

require LSB_PLUGIN_BASE . 'autoloader.php';

require LSB_PLUGIN_BASE . 'plugin-run.php';

register_activation_hook( __FILE__, array( 'LSB_Plugin_Run', 'install' ) );
register_deactivation_hook( __FILE__, array( 'LSB_Plugin_Run', 'uninstall' ) );

LSB_Plugin_Run::run();

