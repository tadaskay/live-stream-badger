<?php

/**
 * Plugin's common functions. Can be included anywhere.
 */

/**
 * Alternative sprintf, used for templating.
 * Parameters are provided as an associative array.
 */
function lsb_template_sprintf($str, $vars) {
	return str_replace(array_keys($vars), array_values($vars), $str);
}

//eof