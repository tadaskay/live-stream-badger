<?php
/*
 Plugin Name: Live Stream Badger
 Plugin URI: http://wordpress.org/extend/plugins/live-stream-badger/
 Description: Display status of Twitch.tv live streams
 Version: 1.0.1 
 Author: Tadas Krivickas
 Author URI: http://profiles.wordpress.org/tkrivickas
 Author email: tadas.krivickas@gmail.com
 License: GPLv2 or later
 */

if (!defined('LSB_PLUGIN_BASE_URL')) {
	define('LSB_PLUGIN_BASE_URL', dirname(__FILE__));
}

include_once (LSB_PLUGIN_BASE_URL . '/stream-status-widget.php');
include_once (LSB_PLUGIN_BASE_URL . '/stream-status-updater.php');

// Register widget
add_action('widgets_init', create_function('', 'return register_widget("LSB_Stream_Status_Widget");'));

// Register styles
add_action('wp_enqueue_scripts', 'lsb_register_styles');
function lsb_register_styles() {
	wp_register_style('lsb-style', plugins_url('style.css', __FILE__));
	wp_enqueue_style('lsb-style');
}

//
// Register updater to start on activation/ stop on deactivation
//
add_action('lsb_update_all_stream_status', 'lsb_update_all_stream_status');

register_activation_hook(__FILE__, 'lsb_activation');
function lsb_activation() {
	wp_schedule_event(time(), 'lsb_five_minutes', 'lsb_update_all_stream_status');
}

register_deactivation_hook(__FILE__, 'lsb_deactivation');
function lsb_deactivation() {
	wp_clear_scheduled_hook('lsb_update_all_stream_status');
}

//
// Add 5 minutes option to wp-cron
//
add_filter('cron_schedules', 'lsb_create_schedule_def');
function lsb_create_schedule_def($schedules) {
	$schedules['lsb_five_minutes'] = array('interval' => 60 * 5, 'display' => __('Each 5 minutes'));
	return $schedules;
}

add_filter('lsb_stream_status_widget_text', 'do_shortcode');

//eof