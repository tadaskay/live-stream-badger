<?php

// Die if we haven't been included by WordPress
if ( !defined( 'ABSPATH' ) )
	die();

include_once ( LSB_PLUGIN_BASE_URL . '/apis/class-api-core.php' );
include_once ( LSB_PLUGIN_BASE_URL . '/domain/domain-core.php' );

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

		$validated_stream_urls = $api_core->validate_urls( $all_urls );
		$stream_infos          = $api_core->query( $validated_stream_urls );

		$this->update_menu_items( $all_widgets_menu_items, $stream_infos );
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
	 * Go through all menu items and update with information from LSB_Stream_Info.
	 * 'Watching now' is placed in Menu item 'description' field.
	 *
	 * When Stream info is not found for menu item, 'Watching now' is set to -1 (which is later
	 * interpreted as 'Offline').
	 *
	 * @param $all_widgets_menu_items
	 * @param $stream_infos
	 */
	private function update_menu_items( $all_widgets_menu_items, $stream_infos ) {
		// Update menu items, placing 'Watching now' count in description
		foreach ( $all_widgets_menu_items as $m ) {
			/** @var $m LSB_Menu_Item */
			$found_stream_info = NULL;
			foreach ( $stream_infos as $s ) {
				/** @var $s LSB_Stream_Info */
				if ( $s->original_url == $m->original_url ) {
					$found_stream_info = $s;
					break;
				}
			}

			// Empty info for offline/non-existent streams
			if ( empty( $found_stream_info ) ) {
				$found_stream_info = new LSB_Stream_Info();
			}

			$menu_item_save_data = array(
				'menu-item-description' => $found_stream_info->watching_now,
				// Don't forget other fields, otherwise they will be updated by empty defaults
				'menu-item-title'       => $m->title,
				// Save URL as it was before (don't format it)
				'menu-item-url'         => $m->original_url
			);

			wp_update_nav_menu_item( $m->menu_id, $m->id, $menu_item_save_data );
		}
	}

}
//eof
