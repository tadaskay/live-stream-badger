<?php

/**
 *  This wrapper allows to have no namespaces in plugin's main file, making it parseable in <5.3.
 *  Plugin will not execute but it will be nice enough to provide user a message to upgrade.
 */
class LSB_Plugin_Run {

    static function install() {
        $installer = new livestreambadger\LSB_Installer();
        $installer->install();
    }

    static function uninstall() {
        $installer = new livestreambadger\LSB_Installer();
        $installer->uninstall();
    }

    /**
     *  Anything that needs to be run/setup as a part of the plugin
     */
    static function run() {

        // Register styles
        if ( livestreambadger\Settings::read_settings( 'disable_css' ) == false ) {
            add_action( 'wp_enqueue_scripts', 'lsb_register_styles' );
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
        $embedded_stream_sc = new livestreambadger\LSB_Embedded_Stream();
        add_shortcode( 'livestream', array( $embedded_stream_sc, 'do_shortcode' ) );

        new livestreambadger\LSB_Admin_Settings( 
            new livestreambadger\LSB_Stream_Storage( 
                new livestreambadger\LSB_API_Sync() 
            ) 
        );

    }

}