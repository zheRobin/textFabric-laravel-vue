<?php

namespace Modules\Subscriptions\Services;

use Carbon\Carbon;

class Period
{
    /**
     * @var int
     */
    protected int $period;

    /**
     * @var string
     */
    protected string $interval;

    /**
     * @var Carbon
     */
    protected Carbon $start;

    /**
     * @var Carbon
     */
    protected Carbon $end;

    public function __construct(int $period, ?Carbon $start = null, string $interval = 'day')
    {
        $this->period = $period;
        $this->interval = $interval;

        if (is_null($start)) {
            $this->start = Carbon::now();
        } else {
            $this->start = $start;
        }

        $start = clone $this->start;
        $method = 'add'.ucfirst($this->interval).'s';
        $this->end = $start->{$method}($this->period);
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
