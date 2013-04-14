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
		error_log("Executing Twitch query: " . $this->query_string);
		$json_content = file_get_contents($this->query_string);
		$j = json_decode($json_content, TRUE);
		return $j;
	}

}

//eof