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

use Tygh\Enum\FiscalData1212Objects;
use Tygh\Enum\MarkingCodeFormats;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @param array<string, string|int> $product    Product data
 * @param string                    $field_name Field name
 */
function fn_rus_taxes_set_payment_object(array $product, string $field_name): int
{
    if (isset($product[$field_name]) && FiscalData1212Objects::isExists((int) $product[$field_name])) {
        return $product[$field_name];
    }

    return FiscalData1212Objects::COMMODITY;
}

/**
 * @param array<string, string|int|null> $product    Product data
 * @param string                         $field_name Field name
 *
 * @return string|null
 */
function fn_rus_taxes_set_mark_code_type(array $product, string $field_name)
{
    if (isset($product[$field_name]) && $product[$field_name] === MarkingCodeFormats::FUR) {
        return MarkingCodeFormats::FUR;
    }

    return null;
}
