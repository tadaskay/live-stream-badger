<?php

// Die if we haven't been included by WordPress
if ( ! defined( 'ABSPATH' ) )
	die( );

include_once (LSB_PLUGIN_BASE_URL . '/stream-status-widget.php');
include_once (LSB_PLUGIN_BASE_URL . '/class-stream-dto.php');
include_once (LSB_PLUGIN_BASE_URL . '/class-query-twitch.php');

/**
 * For usage in wp-cron.
 * Updates all menu items containing stream links with status from Twitch.tv API using settings configured in the Widget.
 * 'Watching now' count is stored in $nav_menu_item->description.
 */
function lsb_update_all_stream_status( ) {

	// Get stored widget options
	// http://wordpress.stackexchange.com/questions/2091/using-widget-options-outside-the-widget
	$w = new LSB_Stream_Status_Widget( );
	$all_widget_settings = $w->get_settings( );
	if ( empty( $all_widget_settings ) )
		return;

	// Get menu items from all widgets (when there is more than one widget configured)
	// And build Stream_Bookmark DTOs
	$stream_bookmarks = array( );
	foreach ( $all_widget_settings as $ws ) {
		$menu_id = $ws['menu_id'];
		$menu_items = ! empty( $menu_id ) ? wp_get_nav_menu_items( $menu_id ) : false;

		// There are no menu items in this menu (or menu does not exist), iterate
		if ( ! $menu_items )
			continue;

		// Iterate through all menu items in this menu
		foreach ( $menu_items as $m ) {
			// Try to build a DTO from menu item URL
			$sb = Stream_DTO::from_url( $m->url );
			if ( empty( $sb ) )
				continue;

			// Copy the remaining fields (URL is copied via factory method already)
			$sb->menu_id = $menu_id;
			$sb->menu_item_id = $m->ID;
			$sb->menu_item_title = $m->title;

			// Add to DTO list
			$stream_bookmarks[] = $sb;
		}
	}

	// Build channel names array
	$channel_names = array( );
	foreach ( $stream_bookmarks as $sb )
		$channel_names[] = $sb->channel_name;

	// Create an API query
	$query = Query_Twitch::create( $channel_names );
	if ( empty( $query ) )
		return;

	// Transform JSON results into associative array
	// with a channel name as the key
	$j_results = array( );
	$json = $query->get_results( );
	if ( empty( $json ) )
		return;

	foreach ( $json as $j ) {
		$j_live_user_split = explode( 'live_user_', $j['name'] );
		$j_channel_name = ! empty( $j_live_user_split ) ? $j_live_user_split[1] : '';

		if ( empty( $j_channel_name ) )
			continue;

		// Store results with channel key lower cased
		$j_channel_name = strtolower($j_channel_name);
		$j_results[$j_channel_name] = $j;
	}

	// Copy data JSON->Stream_Bookmarks
	foreach ( $stream_bookmarks as $sb ) {
		$j = isset( $j_results[ $sb->channel_name ] ) ? $j_results[ $sb->channel_name ] : NULL;
		$sb->watching_now = ! empty( $j ) ? $j['channel_count'] : -1;
	}

	// Update menu items, placing 'Watching now' count in description
	foreach ( $stream_bookmarks as $sb ) {
		$menu_item_data = array( 
			'menu-item-description' => $sb->watching_now,
			// Don't forget other fields, otherwise they will be updated by empty defaults
			'menu-item-title' => $sb->menu_item_title, 
			'menu-item-url' => $sb->url
		);
		wp_update_nav_menu_item( $sb->menu_id, $sb->menu_item_id, $menu_item_data );
	}
}

//eof