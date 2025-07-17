<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Controller;

use TrafficTracker\Application\Enum\Period;

trait AnalyticsRequestValidator
{
    public function validate(array $data, $isPeriodRequired = false): array
    {
        $errors = [];

        if ($isPeriodRequired) {
            if (empty($data['period'])) {
                $errors[] = 'Period is required';
            }
        }

        if (!empty($data['period'])) {
            $availablePeriods = Period::values();
            if (!in_array($data['period'], $availablePeriods, true)) {
                $errors[] = 'Period must be one of: '.implode(', ', $availablePeriods);
            }

            if ($data['period'] === Period::CUSTOM->value) {
                if (empty($data['from']) || empty($data['to'])) {
                    $errors[] = 'from and to dates are required for custom period';
                }
            }
        }

        if (!empty($data['from']) && !strtotime($data['from'])) {
            $errors[] = 'Invalid from date format';
        }

        if (!empty($data['to']) && !strtotime($data['to'])) {
            $errors[] = 'Invalid to date format';
        }

        return $errors;
    }
}
