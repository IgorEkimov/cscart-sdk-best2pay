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

use Tygh\Enum\YesNo;

$schema['testimonials'] = [
    'templates' => [
        'addons/discussion/blocks/testimonials.tpl' => [],
    ],
    'wrappers' => 'blocks/wrappers',
    'settings' => [
        'limit' => [
            'type'          => 'input',
            'default_value' => '10'
        ],
        'random' => [
            'type'          => 'checkbox',
            'default_value' => YesNo::NO
        ],
        'not_scroll_automatically' => [
            'type' => 'checkbox',
            'default_value' => YesNo::NO
        ],
        'speed' =>  [
            'type'          => 'input',
            'default_value' => 400
        ],
        'pause_delay' =>  [
            'type'          => 'input',
            'default_value' => 3
        ],
        'item_quantity' =>  [
            'type'          => 'input',
            'default_value' => 1
        ],
        'outside_navigation' => [
            'type'          => 'checkbox',
            'default_value' => YesNo::NO
        ]
    ],
    'cache' => [
        'update_handlers' => ['discussion', 'discussion_messages', 'discussion_posts', 'discussion_rating'],
    ]
];

$schema['products']['content']['items']['fillings']['rating'] = [
    'params' => [
        'rating'  => true,
        'sort_by' => 'rating'
    ],
];
$schema['products']['cache']['update_handlers'][] = 'discussion_rating';

$schema['categories']['content']['items']['fillings']['rating'] = [
    'params' => [
        'rating'  => true,
        'sort_by' => 'rating'
    ],
];

if (!empty($schema['categories']['cache']['update_handlers'])) {
    $schema['categories']['cache']['update_handlers'][] = 'discussion_rating';
}

$schema['pages']['content']['items']['fillings']['rating'] = [
    'params' => [
        'rating'  => true,
        'sort_by' => 'rating'
    ],
];

if (!empty($schema['pages']['cache']['update_handlers'])) {
    $schema['pages']['cache']['update_handlers'][] = 'discussion_rating';
}

if (!empty($schema['vendors']['cache']['update_handlers'])) {
    $schema['vendors']['cache']['update_handlers'][] = 'discussion';
    $schema['vendors']['cache']['update_handlers'][] = 'discussion_messages';
    $schema['vendors']['cache']['update_handlers'][] = 'discussion_posts';
    $schema['vendors']['cache']['update_handlers'][] = 'discussion_rating';
}

$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'discussion';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'discussion_messages';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'discussion_posts';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'discussion_rating';

$schema['main']['cache_overrides_by_dispatch']['categories.view']['update_handlers'][] = 'discussion';
$schema['main']['cache_overrides_by_dispatch']['categories.view']['update_handlers'][] = 'discussion_messages';
$schema['main']['cache_overrides_by_dispatch']['categories.view']['update_handlers'][] = 'discussion_posts';
$schema['main']['cache_overrides_by_dispatch']['categories.view']['update_handlers'][] = 'discussion_rating';

return $schema;
