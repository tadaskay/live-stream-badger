<?php

include_once LSB_PLUGIN_BASE . 'view/class-embedded-twitch-view.php';
include_once LSB_PLUGIN_BASE . 'domain/class-stream-summary.php';

class LSB_Embedded_Stream {

	function do_shortcode( $attrs ) {
		$attrs = shortcode_atts( array(
		                              'url'         => '',
		                              'width'       => '620',
		                              'height'      => '378',
		                              'stream'      => TRUE,

		                              'chat_width'  => '620',
		                              'chat_height' => '400',
		                              'chat'        => FALSE
		                         ), $attrs );

		if ( empty( $attrs['url'] ) )
			return '';

		$api_core         = new LSB_API_Core();
		$stream_summaries = $api_core->validate_urls( array( $attrs['url'] ) );
		$stream_summary   = !empty( $stream_summaries ) ? $stream_summaries[0] : NULL;

		/** @var $stream_summary LSB_Stream_Summary */
		if ( empty( $stream_url ) )
			return '';

		$view = NULL;
		switch ( $stream_summary->api_id ) {
			case 'twitch' :
				$view = new LSB_Embedded_Twitch_View();
				break;
		}

		if ( empty( $view ) )
			return '';

		$attrs['stream_summary'] = $stream_summary;

		return $view->get_html( $attrs );
	}

}