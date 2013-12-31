<?php
namespace livestreambadger;

spl_autoload_register( array( 'livestreambadger\\Autoloader', 'autoload' ) );

class Autoloader {
    
    private static $classes = array(
        // admin
        'LSB_Admin_Settings'        => 'admin/admin-settings.php',
        'LSB_Diagnostics'           => 'admin/diagnostics.php',
        'LSB_Installer'             => 'admin/installer.php',
        // apis
        'LSB_API_Core'              => 'apis/class-api-core.php',
        'LSB_API'                   => 'apis/class-api.php',
        'LSB_Twitch_API_V3'         => 'apis/twitch-api-v3.php',
        // domain
        'LSB_API_Call_Exception'    => 'domain/class-api-call-exception.php',
        'Settings'                  => 'domain/class-settings.php',
        'LSB_Stream_Sorter'         => 'domain/class-stream-sorter.php',
        'LSB_Stream_Summary'        => 'domain/class-stream-summary.php',
        'LSB_Stream'                => 'domain/class-stream.php',
        'WP_Options'                => 'domain/class-wp-options.php',
        // scheduler
        'LSB_API_Sync'              => 'scheduler/class-api-sync.php',
        // shortcode
        'LSB_Embedded_Stream'       => 'shortcode/class-embedded-stream.php',
        // store
        'LSB_Stream_Storage'        => 'store/class-stream-storage.php',
        // view
        'LSB_Embedded_Twitch_View'  => 'view/class-embedded-twitch-view.php',
        'LSB_View'                  => 'view/class-view.php',
        // extend
        'Templates'                 => 'extend/class-templates.php',
        // widget
        'Stream_Status_Widget'      => 'stream-status-widget.php'
    );

    public static function autoload( $class_name ) {
        $class_name = str_replace( __NAMESPACE__ . '\\', '', $class_name );
        if ( isset( self::$classes[ $class_name ] ) ) {
            include LSB_PLUGIN_BASE . self::$classes[ $class_name ];
        }
    }
    
}