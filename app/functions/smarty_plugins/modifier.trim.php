<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

/**
 * Smarty plugin
 *
 * @package Smarty
 *
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier<br>
 * Name:     trim<br>
 * Purpose:  Strip whitespace (or other characters) from the beginning and end of a string
 * Example:  {$string|trim}
 * -------------------------------------------------------------
 */
/**
 * Strip whitespace (or other characters) from the beginning and end of a string
 *
 * @param string $string     The string that will be trimmed
 * @param string $characters Optional characters to be stripped
 *
 * @return string
 */
function smarty_modifier_trim($string, $characters = " \n\r\t\v\x00")
{
    return trim($string ?? '', $characters);
}
