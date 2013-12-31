<?php

namespace livestreambadger;

/**
 * Settings declaration and access API, usable both from admin UI and front-end
 */
class Settings {
    
    static function default_settings() {
        $settings = array(
            'general' => array (
                'type' => 'section',
                'title' => 'General',
                'group' => WP_Options::OPTIONS_GROUP
            ),
                'cache_time' => array (
                    'type' => 'select',
                    'title' => 'Cache time',
                    'description' => 'Time to live for remote API cache. Increase for performance gain, decrease to get more frequent updates. (Default: 3 minutes)',
                    'default' => 180,
                    'options' => array (
                        'Realtime (DO NOT USE FOR PRODUCTION)' => 0,
                        '30 seconds' => 30,
                        '1 minute' => 60,
                        '3 minutes' => 180,
                        '5 minutes' => 300,
                        '10 minutes' => 600
                    ),
                    'section' => 'general'
                ),
            'appearance' => array (
                'type' => 'section',
                'title' => 'Appearance',
                'group' => WP_Options::OPTIONS_GROUP
            ),
                'disable_css' => array(
                    'type' => 'checkbox',
                    'title' => 'Disable plugin stylesheet',
                    'description' => 'The bundled stylesheet is really minimal and provides compatibility with most themes.<br>However, you can disable it completely if you want to style things by yourself in the theme. (Default: unchecked)',
                    'default' => false,
                    'section' => 'appearance'
                )
        );
        return $settings;
    }
    
    /**
     * Reads a setting value or gets a default (if empty).
     */
    static function read_settings( $name = '' ) {
        $options = get_option( WP_Options::OPTIONS_GROUP );
        if ( empty ( $name ) )
            return $options;
        
        if ( !isset( $options[ $name ] ) ) {
            $defaults = self::default_settings();
            return isset($defaults[ $name ]) ? $defaults[ $name ]['default'] : null;
        }
        
        return $options[ $name ];
    }

}