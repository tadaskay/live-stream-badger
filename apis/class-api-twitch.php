<?php

/**
 * Represents query for Twitch.tv API
 */
class LSB_API_Twitch extends LSB_API {

	const API_ID         = 'twitch';
	const Q_CHANNEL_INFO = 'http://api.justin.tv/api/stream/list.json?channel=%s';

	const FORMAT_CHANNEL_TO_URL = 'http://www.twitch.tv/%s';
	const REGEX_TWITCH_URL      = '%http://(?:www)?\.twitch\.tv/([a-zA-Z0-9_\-\.]+)(?:/.)?%';

	function get_api_identifier() {
		return self::API_ID;
	}

	protected function format_url( $url ) {
		$url = parent::format_url( $url );
		if ( empty( $url ) )
			return '';

		$formatted_url = $this->channel_to_url( $this->url_to_channel( $url ) );
		return $formatted_url;
	}

	protected function create_query_string( $channel_names ) {
		$joined = implode( ',', $channel_names );
		$q      = sprintf( self::Q_CHANNEL_INFO, $joined );
		return $q;
	}

	protected function url_to_channel( $url ) {
		$matches     = array();
		$regex_match = preg_match( self::REGEX_TWITCH_URL, $url, $matches );
		if ( !$regex_match )
			return '';
		$channel_name = $matches[1];
		return $channel_name;
	}

	protected function channel_to_url( $channel ) {
		return sprintf( self::FORMAT_CHANNEL_TO_URL, $channel );
	}

	protected function map_results( $result_string ) {
		$json = json_decode( $result_string, TRUE );
		if ( empty( $json ) )
			return array();

		$dtos = array();

		foreach ( $json as $j ) {
			$j_channel_name = $this->map_results_channel_name( $j['name'] );
			if ( empty( $j_channel_name ) )
				continue;

			$url = $this->channel_to_url( $j_channel_name );

			$stream_dto               = new LSB_Stream_Info();
			$stream_dto->channel_name = $j_channel_name;
			$stream_dto->url          = $url;
			$stream_dto->watching_now = $j['channel_count'];

			$dtos[] = $stream_dto;
		}

		return $dtos;
	}

	private function map_results_channel_name( $j_live_user ) {
		$j_split = explode( 'live_user_', $j_live_user );
		$j_name  = count( $j_split ) == 2 ? $j_split[1] : '';
		$j_name  = strtolower( $j_name );
		return $j_name;
	}

}

//eof
