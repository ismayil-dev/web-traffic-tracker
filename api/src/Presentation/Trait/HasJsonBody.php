<?php

declare(strict_types=1);

namespace TrafficTracker\Presentation\Trait;

trait HasJsonBody
{
    public function getJsonBody(): array
    {
        $encodedBody = file_get_contents('php://input');

        if (empty($encodedBody)) {
            return [];
        }

        return json_decode($encodedBody, true);
    }
}
