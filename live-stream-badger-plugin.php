<?php
/*
 Plugin Name: Live Stream Badger
 Plugin URI: http://wordpress.org/extend/plugins/live-stream-badger/
 Description: Display status of Twitch.tv live streams
 Version: 1.4-dev
 Author: Tadas Krivickas
 Author URI: http://profiles.wordpress.org/tkrivickas
 Author email: tadas.krivickas@gmail.com
 License: GPLv3
 License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( !defined( 'LSB_PLUGIN_BASE' ) ) {
	define( 'LSB_PLUGIN_BASE', plugin_dir_path( __FILE__ ) );
}

// Includes: admin
include_once LSB_PLUGIN_BASE . 'admin/admin-settings.php';
include_once LSB_PLUGIN_BASE . 'admin/diagnostics.php';
// Includes: other
include_once LSB_PLUGIN_BASE . 'apis/class-api-core.php';
include_once LSB_PLUGIN_BASE . 'stream-status-widget.php';
include_once LSB_PLUGIN_BASE . 'shortcode/class-embedded-stream.php';
include_once LSB_PLUGIN_BASE . 'scheduler/class-api-sync.php';

// Register widget
add_action( 'widgets_init', create_function( '', 'return register_widget("LSB_Stream_Status_Widget");' ) );

// Register styles
add_action( 'wp_enqueue_scripts', 'lsb_register_styles' );
function lsb_register_styles() {
	wp_register_style( 'lsb-style', plugins_url( 'style.css', __FILE__ ) );
	wp_enqueue_style( 'lsb-style' );
}

// Register shortcode
$embedded_stream_sc = new LSB_Embedded_Stream();
add_shortcode( 'livestream', array( $embedded_stream_sc, 'do_shortcode' ) );

//
// Hook synchronization with APIs to certain actions
//
$lsb_api_sync = new LSB_API_Sync();
add_action( 'lsb_update_all_stream_status', array( $lsb_api_sync, 'updateAll' ) ); // Scheduled action (5 minutes)
add_action( 'wp_update_nav_menu', array( $lsb_api_sync, 'updateAll' ) );           // When an administrator updates any menu

register_activation_hook( __FILE__, 'lsb_activation' );
function lsb_activation() {
	lsb_health_check();
	wp_schedule_single_event( time(), 'lsb_update_all_stream_status' );
	wp_schedule_event( time(), 'lsb_five_minutes', 'lsb_update_all_stream_status' );
}

function lsb_health_check() {
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

register_deactivation_hook( __FILE__, 'lsb_deactivation' );
function lsb_deactivation() {
	wp_clear_scheduled_hook( 'lsb_update_all_stream_status' );
}

//
// Add 5 minutes option to wp-cron
//
add_filter( 'cron_schedules', 'lsb_create_schedule_def' );
function lsb_create_schedule_def( $schedules ) {
	$schedules['lsb_five_minutes'] = array( 'interval' => 60 * 5, 'display' => __( 'Each 5 minutes' ) );
	return $schedules;
}

add_filter( 'lsb_stream_status_widget_text', 'do_shortcode' );

new LSB_Admin_Settings(
    new LSB_Diagnostics()
);

//eof