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

namespace Tygh\Addons\OnboardingGuide;

use Tygh\Enum\Addons\OnboardingGuide\StepStatusEnum;
use Tygh\Enum\Addons\OnboardingGuide\GuideStatusEnum;

class OnboardingGuide
{
    /**
     * @var null|array{status: string, open_step_id: string, steps: array<string, array{status: string, number: int, completed_actions: array<string>}>}
     */
    protected static $progress = null;

    /**
     * @param array{status: string, open_step_id: string, steps: array<string, array{status: string, number: int, completed_actions: array<string>}>}|null $progress Progress info
     *
     * @psalm-suppress LessSpecificReturnStatement, MoreSpecificReturnType
     *
     * @return array{total_steps: int, completed_steps: int, percentage: int}
     */
    public static function calculateProgress(array $progress = null): array
    {
        $schema = self::getStepsSchema();
        $total_steps = count($schema);
        $progress = $progress ?? self::getProgress();

        $total_completed_steps = array_reduce($progress['steps'], static function (int $total, array $step) {
            if (StepStatusEnum::isCompleted($step['status'])) {
                $total++;
            }

            return $total;
        }, 0);

        return [
            'total_steps' => $total_steps,
            'completed_steps' => $total_completed_steps,
            'percentage' => $total_completed_steps / $total_steps * 100
        ];
    }

    /**
     * @return array{status: string, open_step_id: string, steps: array<string, array{status: string, number: int, completed_actions: array<string>}>}
     */
    public static function getProgress(): array
    {
        if (self::$progress === null) {
            $progress = fn_get_storage_data('onboarding_guide_progress');

            if ($progress) {
                $progress = json_decode($progress, true);
            } else {
                $steps_schema = self::getStepsSchema();
                reset($steps_schema);
                $first_step_id = key($steps_schema);
                $progress = ['status' => GuideStatusEnum::ACTIVE, 'open_step_id' => $first_step_id, 'steps' => []];
            }

            self::$progress = $progress;
        }

        return self::$progress;
    }

    /**
     * @param array{status: string, open_step_id: string, steps: array<string, array{status: string, number: int, completed_actions: array<string>}>} $progress Progress data
     *
     * @return void
     */
    public static function saveProgress(array $progress)
    {
        fn_set_storage_data('onboarding_guide_progress', json_encode($progress));
        self::$progress = $progress;
    }

    /**
     * Saves step status
     *
     * @param string $step_id     Step ID
     * @param string $step_status Step status
     *
     * @return array{total_steps: int, completed_steps: int, percentage: float}|false
     */
    public static function saveStepStatus(string $step_id, string $step_status)
    {
        if (!$step_id || !StepStatusEnum::hasStatus($step_status)) {
            return false;
        }

        $progress = self::getProgress();
        $progress_step = $progress['steps'][$step_id] ?? [];
        $progress_step['status'] = $step_status;
        $progress['steps'][$step_id] = $progress_step;

        /** @psalm-suppress ArgumentTypeCoercion */
        $total = self::calculateProgress($progress);

        if ($total['total_steps'] === $total['completed_steps']) {
            $progress['status'] = GuideStatusEnum::COMPLETE;
            $progress['open_step_id'] = '';
        } elseif ($step_status === StepStatusEnum::COMPLETED) {
            // Define next open step
            $progress['open_step_id'] = (string) self::getNextUncompletedStepId($step_id);
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        self::saveProgress($progress);

        return $total;
    }

    /**
     * @return array<string>
     */
    public static function saveStepAction(string $step_id, string $action): array
    {
        $progress = self::getProgress();
        $progress_step = $progress['steps'][$step_id] ?? ['status' => StepStatusEnum::ACTIVE];
        $completed_actions = $progress_step['completed_actions'] ?? [];

        if (!in_array($action, $completed_actions)) {
            $completed_actions[] = $action;
        }

        $progress_step['completed_actions'] = $completed_actions;
        $progress['steps'][$step_id] = $progress_step;

        /** @psalm-suppress ArgumentTypeCoercion */
        self::saveProgress($progress);

        return $completed_actions;
    }

    /**
     * @return array<string, array{status: string, completed_actions: array<string>}>
     */
    public static function getSteps(): array
    {
        $progress = self::getProgress();
        $open_step_id = $progress['open_step_id'];
        $steps_schema = self::getStepsSchema();
        $result = [];

        foreach ($steps_schema as $step_id => $step_schema) {
            $progress_step = ['status' => StepStatusEnum::ACTIVE];

            if (isset($progress['steps'][$step_id])) {
                $progress_step = $progress['steps'][$step_id];

                if ($progress_step['status'] === StepStatusEnum::CLOSED) {
                    continue;
                }
            }

            $step_schema['completed_actions'] = $progress_step['completed_actions'] ?? [];
            $step_schema['status'] = $open_step_id === $step_id ? StepStatusEnum::OPEN : $progress_step['status'];

            $result[$step_id] = $step_schema;
        }

        return $result;
    }

    /**
     * Determines that onboarding is complete
     */
    public static function isCompleted(): bool
    {
        $progress = self::getProgress();
        return in_array($progress['status'], [GuideStatusEnum::COMPLETE, GuideStatusEnum::DISMISS]);
    }

    /**
     * Determines that onboarding is dismissed
     */
    public static function isDismissed(): bool
    {
        $progress = self::getProgress();
        return $progress['status'] === GuideStatusEnum::DISMISS;
    }

    /**
     * @return array<string, array{position: string, number: string}>
     */
    public static function getStepsSchema(): array
    {
        $schema = fn_get_schema('onboarding_guide', 'steps');

        $schema = array_filter($schema, static function ($step) {
            if (!isset($step['visible'])) {
                return true;
            }

            $visible = $step['visible'];
            $params = [];

            if (is_array($visible)) {
                [$visible, $params] = $visible;
            }

            if (is_callable($visible)) {
                return call_user_func_array($visible, $params);
            }

            return (bool) $visible;
        });

        uasort($schema, static function (array $a, array $b) {
            $pos_a = $a['position'] ?? 0;
            $pos_b = $b['position'] ?? 0;
            return $pos_a <=> $pos_b;
        });

        $step_number = 1;
        foreach ($schema as &$step) {
            $step['number'] = $step_number;
            $step_number++;
        }

        return $schema;
    }

    /**
     * @return false|string
     */
    public static function getNextUncompletedStepId(string $step_id)
    {
        $steps_schema = self::getStepsSchema();
        $current_step = $steps_schema[$step_id] ?? false;

        if (!$current_step) {
            return false;
        }

        $needle_step_id = null;
        $progress = self::getProgress()['steps'];

        foreach ($steps_schema as $step_schema_id => $step_schema) {
            $next_step_number = (int) $current_step['number'] + 1;

            if ($step_schema['number'] !== $next_step_number) {
                continue;
            }

            $needle_step_status = $progress[$step_schema_id]['status'] ?? '';
            $needle_step_id = StepStatusEnum::isCompleted($needle_step_status)
                ? self::getNextUncompletedStepId($step_schema_id)
                : $step_schema_id;
        }

        if (empty($needle_step_id)) {
            return false;
        }

        return $needle_step_id;
    }

    /**
     * Dismiss onboarding guide
     */
    public static function dismiss(): void
    {
        $progress = OnboardingGuide::getProgress();
        $progress['status'] = GuideStatusEnum::DISMISS;
        OnboardingGuide::saveProgress($progress);
    }

    /**
     * Restart onboarding guide
     */
    public static function restart(): void
    {
        fn_set_storage_data('onboarding_guide_progress');
    }
}
