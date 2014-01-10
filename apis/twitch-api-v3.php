<?php
namespace livestreambadger;

class LSB_Twitch_API_V3 extends LSB_API {
    
    const API_ID = 'twitch';
    const API_VERSION = 'v3';

    const STREAMS_API   = 'https://api.twitch.tv/kraken/streams';
    const FORMAT_CHANNEL_TO_URL = 'http://www.twitch.tv/%s';
	const REGEX_STREAM_URL      = '%http://(?:www\.)?twitch\.tv/([a-zA-Z0-9_\-\.]+)(?:/.)?%';
    
    function get_api_identifier() {
        return self::API_ID;
    }

    /**
     * Calls given $url in an API specific way.
     * Returns an object, decoded from result JSON.
     * 
     * @throw LSB_API_Call_Exception if remote call is unsuccessful
     */
    protected function remote_get_result( $url ) {
        $accept = 'application/vnd.twitchtv.' . self::API_VERSION . '+json';
        $response = wp_remote_get( $url, array( 'headers' => array( 'Accept' => $accept ) ) );
        if ( is_wp_error( $response ) ) {
            throw new LSB_API_Call_Exception( $response->get_error_message() );
        }
        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body );
        return $result;
    }
    
    protected function url_to_channel( $url ) {
        $matches     = array();
        $regex_match = preg_match( self::REGEX_STREAM_URL, $url, $matches );
        if ( !$regex_match )
            return '';
        $channel_name = $matches[1];
        return esc_attr( $channel_name );
    }
    
    /**
     * Queries API for Stream information based on channel names.
     * 
     * @return array LSB_Stream instances
     */
    function get_streams( $channel_names = array() ) {
        $str_channel_names = implode( ',', $channel_names );
        $url = add_query_arg( 'channel', $str_channel_names, self::STREAMS_API );
        $twitch_result = $this->remote_get_result( $url );
        if ( empty ( $twitch_result ) ) {
            $twitch_result = array();
        }

        $streams = array();

        foreach ($twitch_result->streams as $twitch_stream) {
            $stream = new LSB_Stream();
            $stream->summary->api_id = self::API_ID;
            $stream->summary->url = $twitch_stream->channel->url;
            $stream->summary->channel_name = $twitch_stream->channel->name;
            
            $stream->watching_now = $twitch_stream->viewers;
            $stream->live = true; // Channel object is returned only for live streams
            $stream->screen_cap_url = $twitch_stream->preview->medium;
            $stream->image_url = $twitch_stream->channel->logo;

            $streams[] = $stream;
        }
        
        return $streams;
    }
    
}