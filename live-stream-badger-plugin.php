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
namespace livestreambadger;

if ( !defined( 'LSB_PLUGIN_BASE' ) ) {
	define( 'LSB_PLUGIN_BASE', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'LSB_PLUGIN_BASENAME' ) ) {
    define( 'LSB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( !defined( 'LSB_PLUGIN_VERSION' ) ) {
    define( 'LSB_PLUGIN_VERSION', '1.4');
}

require LSB_PLUGIN_BASE . 'autoloader.php';

// Register styles
if ( Settings::read_settings( 'disable_css' ) == false ) {
    add_action( 'wp_enqueue_scripts', 'livestreambadger\lsb_register_styles' );
}
function lsb_register_styles() {
	wp_register_style( 'lsb-style', plugins_url( 'style.css', __FILE__ ) );
	wp_enqueue_style( 'lsb-style' );
}

// Register widget
add_action( 'widgets_init', function() {
    register_widget( 'livestreambadger\Stream_Status_Widget' );
});
add_filter( 'lsb_stream_status_widget_text', 'do_shortcode' );

// Register shortcode
$embedded_stream_sc = new LSB_Embedded_Stream();
add_shortcode( 'livestream', array( $embedded_stream_sc, 'do_shortcode' ) );

$installer = new LSB_Installer();
register_activation_hook( __FILE__, array( $installer, 'install' ) );
register_deactivation_hook( __FILE__, array( $installer, 'uninstall' ) );

new LSB_Admin_Settings( new LSB_Stream_Storage( new LSB_API_Sync() ) );
