<?php

class LSB_Diagnostics {

    function render() {
        global $wp_version;
        lsbecho( 'WordPress version: ' . $wp_version );
        lsbecho( 'PHP version: ' . phpversion() );
        lsbecho( 'HTTP transport: ' . ( wp_http_supports() ? 'Supported' : 'Unsupported' ) );
        lsbecho_br();
        $cron_schedule = wp_next_scheduled( 'lsb_update_all_stream_status' ) ? gmdate( 'Y-m-d\TH:i:s\Z', wp_next_scheduled( 'lsb_update_all_stream_status' ) ) : false;
        lsbecho( 'Cron stream updater: ' . ( $cron_schedule ? 'On, next run ' . $cron_schedule : 'Off' ) );
        lsbecho( 'Cron available schedules: ' );
        lsbecho_arr( wp_get_schedules() );

        $w = new LSB_Stream_Status_Widget();
        $all_widget_settings = $w->get_settings();

        lsbecho_br();
        lsbecho( 'Registered widgets: ' );
        lsbecho_arr( $all_widget_settings );
        lsbecho_br();

        lsbecho( 'Widget streams: ' );

        if ( empty( $all_widget_settings ) ) $all_widget_settings = array();

        foreach ( $all_widget_settings as $ws ) {
            $current_menu_id = $ws[ 'menu_id' ];
            $current_menu_items = !empty( $current_menu_id ) ? wp_get_nav_menu_items( $current_menu_id ) : FALSE;

            lsbecho( 'Menu id: ' . $current_menu_id );

            if ( !$current_menu_items ) continue;
            foreach ( $current_menu_items as $m ) {
                lsbecho( 'Stream(' . $m->title . '|' . $m->url . ')', 2 );
            }
        }
    }

}

function lsbecho( $s, $indent = 0 ) {
    if ( empty( $s ) ) return;
    echo str_repeat( '&nbsp;', $indent ) . $s . '<br>' . PHP_EOL;
}

function lsbecho_arr( $arr, $indent = 0 ) {
    if ( empty( $arr ) ) return;
    lsbecho( 'Array:', $indent );
    lsbecho( '(', $indent );
    foreach ( $arr as $key => $val ) {

        if ( is_array( $val ) ) {
            lsbecho( $key . ' => ', $indent + 2 );
            lsbecho_arr( $val, $indent + 2 );
        } else {
            lsbecho( $key . ' => ' . $val, $indent + 2 );
        }
    }
    lsbecho( ')', $indent );
}

function lsbecho_br() {
    lsbecho( str_repeat( '=', 50 ) );
}