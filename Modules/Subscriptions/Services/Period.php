<?php

namespace Modules\Subscriptions\Services;

use Carbon\Carbon;
use LogicException;

class Period
{
    /**
     * @var int
     */
    protected int $period;

    /**
     * @var Carbon
     */
    protected Carbon $start;

    /**
     * @var Carbon
     */
    protected Carbon $end;

    public function __construct(int $period, ?Carbon $start = null)
    {
        $this->period = $period;

        if (is_null($start)) {
            $this->start = Carbon::now();
        } else {
            $this->start = $start;
        }

        $this->end = $this->start->copy()->addDays($this->period);
    }

    /**
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->start;
    }

    /**
     * @return Carbon
     */
    public function getEndDate(): Carbon
    {
        return $this->end;
    }

    /**
     * @return int
     */
    public function getPeriod(): int
    {
        return $this->period;
    }
}
