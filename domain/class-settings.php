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
                'title' => 'General Settings',
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