<?php

/**
 * Admin functions class.
 *
 * Defines miscellaneous supporting functions used in the admin area.
 *
 * @since      1.0.0
 * @package    EMT
 * @subpackage EMT/admin/functions
 */
class EMT_Admin_Functions {

	/**
	 * Return markup to display a tooltip.
	 *
	 * @param $text		Description that is displayed when user hovers over tooltip.
	 * @param $tooltip	The tooltip over which user hovers to display the text, default question mark ("?").
	 *
	 * @since	1.0.0
	 */
	public static function tooltip($text, $tooltip = "?") {

		$html = "";

		if ($tooltip === "?") {
			$html = "<span class='emt_tooltip_default'>$tooltip<span class='emt_tooltiptext'>$text</span></span>";
		} else {
			$html = "<span class='emt_tooltip'>$tooltip<span class='emt_tooltiptext'>$text</span></span>";
		}

		return $html;
	}

}
