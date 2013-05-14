<?php

if ( !defined( 'ABSPATH' ) )
	die();

include_once LSB_PLUGIN_BASE . 'apis/class-api-core.php';
include_once LSB_PLUGIN_BASE . 'domain/class-stream-summary.php';
include_once LSB_PLUGIN_BASE . 'store/class-stream-storage.php';
include_once LSB_PLUGIN_BASE . 'domain/class-menu-item.php';

/**
 * For usage in wp-cron.
 * Updates all menu items containing stream links with status from Twitch.tv API using settings configured in the Widget.
 * 'Watching now' count is stored in $nav_menu_item->description.
 */
class LSB_Menu_Item_Updater {

	function updateAll() {
		$all_widget_settings = $this->get_all_widget_configuration();

		$all_urls               = array(); // URLs for querying
		$all_widgets_menu_items = array(); // Menu items for saving later
		$this->parse_configuration( $all_widget_settings, $all_widgets_menu_items, $all_urls );

		if ( empty( $all_widgets_menu_items ) || empty( $all_urls ) )
			return;

		$api_core = new LSB_API_Core();

		$stream_summaries = $api_core->validate_urls( $all_urls );
		$streams          = $api_core->query( $stream_summaries );

		$this->update_store( $stream_summaries, $streams );
	}

	/**
	 * Gets configuration of all Stream Status widgets
	 *
	 * @return array List of widget settings
	 */
	private function get_all_widget_configuration() {
		// Get stored widget options
		// http://wordpress.stackexchange.com/questions/2091/using-widget-options-outside-the-widget
		$w = new LSB_Stream_Status_Widget();

		$all_widget_settings = $w->get_settings();
		if ( empty( $all_widget_settings ) )
			return array();

		return $all_widget_settings;
	}

	/**
	 * Builds Menu item and URL array from all widget instances configuration.
	 *
	 * @param array $all_widget_settings    Wordpress Configuration for all Stream Status widget instances
	 * @param array $all_widgets_menu_items Reference to an array to store parsed Menu items in
	 * @param array $all_urls               Reference to an array to store parsed URLs in
	 */
	private function parse_configuration( $all_widget_settings, &$all_widgets_menu_items, &$all_urls ) {

		foreach ( $all_widget_settings as $ws ) {
			$current_menu_id    = $ws['menu_id'];
			$current_menu_items = !empty( $current_menu_id ) ? wp_get_nav_menu_items( $current_menu_id ) : FALSE;

			// There are no menu items in this menu (or menu does not exist), iterate
			if ( !$current_menu_items )
				continue;

			// Build Menu item info for saving later and store URL for querying
			foreach ( $current_menu_items as $m ) {
				$current_menu_item = new LSB_Menu_Item();

				$current_menu_item->id           = $m->ID;
				$current_menu_item->menu_id      = $current_menu_id;
				$current_menu_item->original_url = $m->url;
				$current_menu_item->title        = $m->title;

				$all_widgets_menu_items[] = $current_menu_item;
				$all_urls[]               = $m->url;
			}
		}

	}

	/**
	 * Updates store for all $stream_summaries with information from API ($streams).
	 * All information will be merged except for 'Watching now' (will be set to offline if not found in LSB_Stream).
	 *
	 * When Stream info is not found for menu item, 'Watching now' is set to -1 (which is later
	 * interpreted as 'Offline').
	 *
	 * @param array $stream_summaries list of LSB_Stream_Summary to update information for
	 * @param       $streams          array of LSB_Stream - information from API
	 */
	private function update_store( $stream_summaries, $streams ) {
		$store        = new LSB_Stream_Storage();
		$stored_infos = $store->load();

		$merged_infos = array();

		foreach ( $stream_summaries as $summary ) {
			/** @var $summary LSB_Stream_Summary */
			if ( isset( $merged_infos[$summary->get_id()] ) )
				// Can occur if the same link is in multiple menus
				continue;

			/** @var $update LSB_Stream_Summary */
			$update = NULL;
			foreach ( $streams as $stream ) {
				/** @var $stream LSB_Stream */
				if ( $stream->summary->original_url == $summary->original_url ) {
					$update = $stream;
					break;
				}
			}

			$stored = isset( $stored_infos[$summary->get_id()] ) ? $stored_infos[$summary->get_id()] : NULL;

			if ( empty( $stored ) ) {
				// A new menu item that is not in store yet
				$stored          = new LSB_Stream();
				$stored->summary = $summary;
			}

			if ( empty( $update ) ) {
				// No update, set stream info to offline
				$stored->watching_now = -1;
			}
			else {
				// There is an update from API, copy data
				$stored->watching_now = $update->watching_now;
			}

			$merged_infos[$summary->get_id()] = $stored;
		}

		$store->store( $merged_infos );
	}

}
//eof
