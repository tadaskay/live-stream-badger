<?php
namespace livestreambadger;

/**
 * Sorts a list of Streams
 *
 * Class LSB_Stream_Sorter
 */
class LSB_Stream_Sorter {

	/**
	 * @var array $menu_items stream_id=>menu_item associative array
	 */
	private $menu_items;

	function __construct( $menu_items ) {
		$this->menu_items = $menu_items;
	}

	/**
	 * Sorts: Online streams ordered by 'Watching now' count, offline streams in menu order.
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	function sort_by_watching_now( $a, $b ) {
		$by_watching_now = LSB_Stream::sort_by_watching_now( $a, $b );
		return ( $by_watching_now != 0 ) ? $by_watching_now : $this->sort_by_menu_order( $a, $b );
	}

	/**
	 * Sorts: Online streams in menu order, offline streams in menu order
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	function sort_by_status( $a, $b ) {
		$by_status = LSB_Stream::sort_by_status( $a, $b );
		return ( $by_status != 0 ) ? $by_status : $this->sort_by_menu_order( $a, $b );
	}

	/**
	 * Sorts by menu order.
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	function sort_by_menu_order( $a, $b ) {
		/** @var $a LSB_Stream */
		/** @var $b LSB_Stream */
		$id_a = $a->summary->get_id();
		$id_b = $b->summary->get_id();

		$menu_items = $this->menu_items;

		$menu_a = isset( $menu_items[$id_a] ) ? $menu_items[$id_a] : NULL;
		$menu_b = isset( $menu_items[$id_b] ) ? $menu_items[$id_b] : NULL;

		if ( empty( $menu_a ) || empty( $menu_b ) )
			return 0;

		return ( (int) $menu_a->menu_order > (int) $menu_b->menu_order ) ? 1 : -1;
	}

}