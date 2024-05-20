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

/**
 * @var array<string, array<string, array>> $schema
 */
$schema['top']['administration']['items']['hybrid_auth'] = [
    'attrs' => [
        'class'=>'is-addon'
    ],
    'href' => 'hybrid_auth.manage',
    'position' => 10100,
    'icon' => 'signin',
];

return $schema;
