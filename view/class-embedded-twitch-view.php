<?php

include_once ( 'class-view.php' );

class LSB_Embedded_Twitch_View extends LSB_View {

	function get_html( $args ) {
		/** @var $stream_url LSB_Stream_URL */
		$stream_url = $args['stream_url'];
		$w          = $args['width'];
		$h          = $args['height'];

		$html = '<object type="application/x-shockwave-flash" height="'.$h.'" width="'.$w.'" id="live_embed_player_flash" '
				. 'data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' . $stream_url->channel_name . '" bgcolor="#000000">'
				. '<param name="allowFullScreen" value="true" />'
				. '<param name="allowScriptAccess" value="always" />'
				. '<param name="allowNetworking" value="all" />'
				. '<param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" />'
				. '<param name="flashvars" value="hostname=www.twitch.tv&channel=' . $stream_url->channel_name . '&auto_play=true&start_volume=25" />'
				. '</object>';

		return $html;
	}
}
//eof