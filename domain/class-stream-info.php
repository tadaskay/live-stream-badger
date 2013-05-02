<?php


/**
 * General Live stream information
 */
class LSB_Stream_Info {

	/**
	 * Original URL entered by user
	 *
	 * @var string
	 */
	public $original_url = '';

	/**
	 * Validated URL
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * Channel name of the stream, normally channel name is the last part of URL.
	 * E.g. http://www.twitch.tv/beyondthesummit -> channel name is 'beyondthesummit'
	 *
	 * @var string
	 */
	public $channel_name = '';

	/**
	 * Count of people watching the channel at the moment.
	 * -1 indicates that the stream is offline, 0 indicates the stream is online but no viewers.
	 *
	 * @var int
	 */
	public $watching_now = -1;

	/**
	 * URL to the channel image
	 *
	 * @var string
	 */
	public $image_url = '';

}

//eof