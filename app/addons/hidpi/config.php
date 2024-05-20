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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_define('HIDPI_IS_HIGH_RES_TRUE', 'Y');
fn_define('HIDPI_IS_HIGH_RES_FALSE', 'N');

Registry::set('config.lazy_thumbnails.max_width', Registry::get('config.lazy_thumbnails.max_width') * 2);
Registry::set('config.lazy_thumbnails.max_height', Registry::get('config.lazy_thumbnails.max_height') * 2);
