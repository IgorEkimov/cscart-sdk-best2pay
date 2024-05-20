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

use Tygh\Addons\OnboardingGuide\OnboardingGuide;
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */
/** @var array $auth */

if ($mode === 'index') {
    if (
        $auth['user_type'] === UserTypes::ADMIN
        && $auth['is_root'] === YesNo::YES
        && !Registry::ifGet('config.demo_mode', false)
    ) {
        if (OnboardingGuide::isDismissed()) {
            return [CONTROLLER_STATUS_OK];
        }

        Tygh::$app['view']->assign([
            'onboarding_guide_steps'            => OnboardingGuide::getSteps(),
            'onboarding_guide_progress'         => OnboardingGuide::calculateProgress(),
            'onboarding_guide_is_store_builder' => fn_allowed_for('ULTIMATE')
        ]);
    }
}
