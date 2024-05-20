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

defined('BOOTSTRAP') or die('Access denied');

return [
    FiscalData1212Objects::COMMODITY                   => 'commodity',
    FiscalData1212Objects::EXCISE                      => 'excise',
    FiscalData1212Objects::JOB                         => 'job',
    FiscalData1212Objects::SERVICE                     => 'service',
    FiscalData1212Objects::PAYMENT                     => 'payment',
    FiscalData1212Objects::WITH_MARKING_CODE           => 'product_with_marking_code',
    FiscalData1212Objects::EXCISABLE_WITH_MARKING_CODE => 'excisable_product_with_marking_code'
];
