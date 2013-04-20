<?php

class LSB_Menu_Item {

	/**
	 * Menu Item persistence ID
	 *
	 * @var
	 */
	public $id;

	/**
	 * ID of a menu, containing this Menu item
	 *
	 * @var int
	 */
	public $menu_id;

	/**
	 * Original URL entered by user
	 *
	 * @var string
	 */
	public $original_url;

	/**
	 * Validated/formatted URL - may or may not match the $original_url
	 *
	 * @var string
	 */
	public $valid_url;

	/**
	 * Title/label
	 *
	 * @var string
	 */
	public $title;

}

//eof