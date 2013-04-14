<?php

/**
 * Stream + MenuItem DTO.
 * Has reference to the menu item and stream information
 */
class Stream_DTO {

	/**
	 * Reference to menu ($nav_menu->ID)
	 */
	public $menu_id;

	/**
	 * Reference to menu item ($nav_menu_item->ID)
	 */
	public $menu_item_id;
	
	/**
	 * Menu item label (needed for update)
	 */
	public $menu_item_title;

	/**
	 * Stream URL, also $nav_menu_item->url
	 */
	public $url = '';

	/**
	 * Channel name of the stream, normally channel name is the last part of URL.
	 * E.g. http://www.twitch.tv/beyondthesummit -> channel name is 'beyondthesummit'
	 */
	public $channel_name = '';

	/**
	 * Count of people watching the channel at the moment.
	 * -1 indicates that the stream is offline, 0 indicates the stream is online but no viewers.
	 */
	public $watching_now;

	/**
	 * Creates this DTO using given Stream URL
	 */
	static function from_url($stream_url) {
		$url_explosion = explode('http://www.twitch.tv/', $stream_url);

		$channel_name = '';
		if (count($url_explosion) == 2) {
			$channel_name = $url_explosion[1];
		}

		if (empty($channel_name)) {
			return NULL;
		}

		$sb = new Stream_DTO();
		$sb -> url = $stream_url;
		$sb -> channel_name = $channel_name;
		return $sb;
	}

}

//eof