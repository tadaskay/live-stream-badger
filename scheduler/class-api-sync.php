<?php

if ( !defined( 'ABSPATH' ) )
	die();

include_once LSB_PLUGIN_BASE . 'apis/class-api-core.php';
include_once LSB_PLUGIN_BASE . 'domain/class-stream-summary.php';
include_once LSB_PLUGIN_BASE . 'store/class-stream-storage.php';

/**
 * Live Stream API data synchronizer. Invoked via hooks (Menu update and WP-Cron).
 *
 * <ol>
 *      <li>Reads all Menu items that are used in Live Stream Badger widgets and collects URLs</li>
 *      <li>Queries APIs</li>
 *      <li>Updates {@link LSB_Stream_Storage}</li>
 * </ol>
 */
class LSB_API_Sync {

    function __construct() {
        // Hook sync:
        add_action( 'lsb_update_all_stream_status', array( $this, 'sync' ) ); // Scheduled update
        add_action( 'wp_update_nav_menu', array( $this, 'sync' ) );           // On menu update

        // Create schedule for sync
        add_filter( 'cron_schedules', array( $this, 'create_schedule' ) );
    }

    /**
     * Create a 5 minute schedule for WP-Cron
     *
     * @param $schedules Original WP-Cron schedules
     * @return array Updated WP-Cron schedules
     */
    function create_schedule( $schedules ) {
        $schedules[ 'lsb_five_minutes' ] = array(
            'interval' => 5 * MINUTE_IN_SECONDS,
            'display' => __( 'Once every 5 minutes' )
        );
        return $schedules;
    }

    /**
     * Synchronizes with Live Stream APIs
     */
    function sync() {
		$all_widget_settings = $this->get_all_widget_configuration();

		$all_urls = $this->parse_configuration( $all_widget_settings );

		if ( empty( $all_urls ) )
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
        /** @var LSB_Stream_Status_Widget */
        $w = new LSB_Stream_Status_Widget();
		$all_widget_settings = $w->get_settings();
        return !empty($all_widget_settings) ? $all_widget_settings : array();
	}

	/**
	 * Gathers Stream URLs from active widget instances.
	 *
	 * @param array $all_widget_settings    Wordpress Configuration for all Stream Status widget instances
     *
     * @return array Stream URLs
     */
    private function parse_configuration( $all_widget_settings ) {
        $urls = array();

        // If there are multiple widgets referencing the same menu, here's the marker for not gathering their links twice.
        $processed_menus = array();

        foreach ( $all_widget_settings as $ws ) {
            $menu_id = $ws[ 'menu_id' ];

            // Pass it or mark it
            if ( in_array( $menu_id, $processed_menus ) ) {
                continue;
            } else {
                $processed_menus[] = $menu_id;
            }

            $menu_items = !empty( $menu_id ) ? wp_get_nav_menu_items( $menu_id ) : false;

            // There are no menu items in this menu (or menu does not exist), iterate
            if ( !$menu_items )
                continue;

            foreach ( $menu_items as $menu_item ) {
                $urls[] = $menu_item->url;
            }
        }

        return $urls;
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

			/** @var $update LSB_Stream */
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
				$stored->image_url = $update->image_url;
				$stored->screen_cap_url = $update->screen_cap_url;
			}

			$merged_infos[$summary->get_id()] = $stored;
		}

		$store->store( $merged_infos );
	}

}
//eof
