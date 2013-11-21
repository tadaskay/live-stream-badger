<?php

class LSB_Stream_Summary {

	/**
	 * Original stream URL entered by user
	 *
	 * @var string
	 */
	public $original_url;

	/**
	 * Validated and formatted stream URL
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Unique identifier of the API (e.g. 'twitch')
	 *
	 * @var string
	 */
	public $api_id;

	/**
	 * Channel name of the stream that can be resolved from the URL.
	 * E.g. http://www.twitch.tv/beyondthesummit channel name is beyondthesummit
	 *
	 * @var string
	 */
	public $channel_name;

	/**
	 * Unique identifier of the stream (API ID + channel name)
	 *
	 * @return string
	 */
	function get_id() {
		return $this->api_id . '_' . $this->channel_name;
	}
	
	function to_string() {
	    return json_encode($this);
	}

}

//eof