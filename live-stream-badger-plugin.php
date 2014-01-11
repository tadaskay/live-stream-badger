<?php
/*
 Plugin Name: Live Stream Badger
 Plugin URI: http://wordpress.org/extend/plugins/live-stream-badger/
 Description: Display status of Twitch.tv live streams
 Version: 1.4.3
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
    define( 'LSB_PLUGIN_VERSION', '1.4.3');
}

register_activation_hook( __FILE__, 'lsb_health_check' );
function lsb_health_check() {
    $errors = array();

    global $wp_version;
    if ( version_compare( $wp_version, '3.7', '<' ) ) {
        $errors[] = sprintf('<p>Live Stream Badger requires WordPress 3.7+. Your version: <span style="color:red">%s</span>.', $wp_version);
    }
    $php_version = phpversion();
    if ( version_compare( $php_version, '5.3', '<' ) ) {
        $errors[] = sprintf('<p>Live Stream Badger requires PHP 5.3+. Your version: <span style="color:red">%s</span>.', $php_version);
    }
    $ssl_loaded = extension_loaded( 'openssl' ) && function_exists( 'openssl_x509_parse' );
    if ( !$ssl_loaded ) {
        $errors[] = sprintf('<p>Live Stream Badger requires PHP extension openssl.</p>');
    }
    if ( !wp_http_supports() ) {
        $errors[] = sprintf('<p>Live Stream Badger requires HTTP transport (curl or streams).</p>');
    }

    if ( !empty( $errors ) ) {
        echo '<pre>';
        foreach ( $errors as $e ) {
            echo $e;
        }
        echo '</pre>';
        exit();
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

