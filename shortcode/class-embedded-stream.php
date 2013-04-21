<?php

class LSB_Embedded_Stream {

	function do_shortcode( $attrs ) {
		$defaults = array(
			'url'    => '',
			'width'  => '640',
			'height' => '480'
		);
		$attrs    = shortcode_atts( $defaults, $attrs );

		if ( empty( $attrs['url'] ) )
			return '';

		$api_core       = new LSB_API_Core();
		$validated_urls = $api_core->validate_urls( array( $attrs['url'] ) );
		$stream_url     = !empty( $validated_urls ) ? $validated_urls[0] : NULL;

		/** @var $stream_url LSB_Stream_URL */
		if ( empty( $stream_url ) )
			return '';

		$html = '<object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' . $stream_url->channel_name . '" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=' . $stream_url->channel_name . '&auto_play=true&start_volume=25" /></object>';
		return $html;
	}

}