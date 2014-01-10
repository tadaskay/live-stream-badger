<?php
namespace livestreambadger;

class LSB_Embedded_Twitch_View extends LSB_View {

	function get_html( $args ) {
		/** @var $stream_url LSB_Stream_Summary */
		$stream_summary = $args['stream_summary'];

		$w           = $args['width'];
		$h           = $args['height'];
		$show_stream = $args['stream'];

		$cw        = $args['chat_width'];
		$ch        = $args['chat_height'];
		$show_chat = $args['chat'];
		
		$autoplay  = $args['autoplay'];

		$html = '';

		if ( $show_stream ) {
			$html .= '<div class="lsb-embedded-view"><object type="application/x-shockwave-flash" height="' . $h . '" width="' . $w . '" id="live_embed_player_flash" '
				. 'data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' . $stream_summary->channel_name . '" bgcolor="#000000">'
				. '<param name="allowFullScreen" value="true" />'
				. '<param name="allowScriptAccess" value="always" />'
				. '<param name="allowNetworking" value="all" />'
				. '<param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" />'
				. '<param name="flashvars" value="hostname=www.twitch.tv&channel=' . $stream_summary->channel_name . ($autoplay === TRUE ? '&auto_play=true' : '') . '&start_volume=25" />'
				. '</object></div>';
		}

		if ( $show_chat ) {
			$html .= '<div class="lsb-embedded-chat"><iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel=' . $stream_summary->channel_name . '&amp;popout_chat=true" height="' . $ch . '" width="' . $cw . '" ></iframe></div>';
		}

		return $html;
	}
}
//eof