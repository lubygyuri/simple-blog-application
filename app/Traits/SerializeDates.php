<?php

namespace App\Traits;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeInterface;

trait SerializeDates
{
    protected function serializeDate(DateTimeInterface $date): string {
        return $date instanceof DateTimeImmutable ?
            CarbonImmutable::instance($date)->toISOString(true) :
            Carbon::instance($date)->toISOString(true);
    }
}
