<?php

include_once LSB_PLUGIN_BASE . 'view/class-embedded-twitch-view.php';

class LSB_Embedded_Stream {

	function do_shortcode( $attrs ) {
		$attrs = shortcode_atts( array(
		                              'url'    => '',
		                              'width'  => '620',
		                              'height' => '378',
		                              'stream' => TRUE,

		                              'chat_width' => '620',
		                              'chat_height' => '400',
		                              'chat'   => FALSE
		                         ), $attrs );

		if ( empty( $attrs['url'] ) )
			return '';

		$api_core       = new LSB_API_Core();
		$validated_urls = $api_core->validate_urls( array( $attrs['url'] ) );
		$stream_url     = !empty( $validated_urls ) ? $validated_urls[0] : NULL;

		/** @var $stream_url LSB_Stream_URL */
		if ( empty( $stream_url ) )
			return '';

		$view = NULL;
		switch ( $stream_url->api_id ) {
			case 'twitch' :
				$view = new LSB_Embedded_Twitch_View();
				break;
		}

		if ( empty( $view ) )
			return '';

		$attrs['stream_url'] = $stream_url;

		return $view->get_html( $attrs );
	}

}