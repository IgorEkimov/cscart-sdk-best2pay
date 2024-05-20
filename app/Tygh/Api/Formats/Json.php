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

namespace Tygh\Api\Formats;

use Tygh\Api\IFormat;

/**
 * JSON format encoder/decoder
 */
class Json implements IFormat
{
    protected $mime_types = array(
        'application/json',
        'application/javascript'
    );

    public function getMimeTypes()
    {
        return $this->mime_types;
    }

    public function encode($data)
    {
        return json_encode($data);
    }

    public function decode($data)
    {
        $result = json_decode($data, true);
        $error = json_last_error() !== JSON_ERROR_NONE ? json_last_error_msg() : '';

        return array($result, $error);
    }
}
