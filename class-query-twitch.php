<?php

/**
 * Represents query for Twitch.tv API
 */
class Query_Twitch {

	/**
	 * Query string for API (URL)
	 */
	private $query_string = '';

	/**
	 * Create query instance from channel names array
	 */
	static function create($channel_names) {
		if (empty($channel_names))
			return NULL;

		$joined = implode(',', $channel_names);

		$q = new Query_Twitch();
		$q -> query_string = sprintf($q -> get_base_url_format(), $joined);
		return $q;
	}

	/**
	 * Format string for query, which formatted with channel names list,
	 * returns a final query string (URL)
	 */
	function get_base_url_format() {
		return 'http://api.justin.tv/api/stream/list.json?channel=%s';
	}

	/**
	 * Returns decoded JSON as a query result
	 */
	function get_results() {
		$response = wp_remote_retrieve_body( wp_remote_get( $this->query_string ) );
		if ( empty( $response ) )
			return NULL;

		$j = json_decode($response, TRUE);
		return $j;
	}

}

//eof