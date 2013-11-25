<?php
namespace livestreambadger;

include LSB_PLUGIN_BASE . 'apis/twitch-api-v3.php';

class LSB_Diagnostics {

    function render() {
        $twitch_api = new LSB_Twitch_API_V3();
        
        $result = $twitch_api->get_streams( array('starladder1', 'aznsensation2700', 'icemanee') );
        
        echo( print_r( $result, true ) );
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