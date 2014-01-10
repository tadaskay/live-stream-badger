<?php
namespace livestreambadger;

abstract class LSB_View {

	/**
	 * Builds HTML view using provided attributes
	 *
	 * @param array $attrs
	 *
	 * @return string HTML
	 */
	abstract function get_html( $attrs );

}
// eof