<?php

namespace Modules\Jetstream\Services;

use App\Models\Team;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class TeamsFilterService
{
    /**
     * @var array|string[]
     */
    protected array $allowedFilters = [
        'subscribed',
        'expired',
        'trial',
        'plan',
        'search',
        'sort',
    ];

    /**
     * @var array|string[]
     */
    protected array $allowedSorts = [
        'expires_soon',
    ];

    /**
     * @var Builder
     */
    protected Builder $teamsQuery;

    public function __construct()
    {
        $this->teamsQuery = Team::query();
    }

    public function apply(array $input)
    {
        foreach ($input as $filter => $value) {
            if (in_array($filter, $this->allowedFilters)) {
                $methodName = 'apply' . ucfirst($filter);

                if (method_exists($this, $methodName)) {
                    $this->$methodName($value);
                } else {
                    throw new Exception("Method [$methodName] doesn't exists for specified filter [$filter].");
                }
            }
        }

        return $this->teamsQuery->with(['users', 'owner'])
            ->paginate(5)
            ->onEachSide(2)
            ->withQueryString();
    }

    public function applySubscribed($value): void
    {
        if (is_bool($value) || $value === 'true') {
            $this->teamsQuery->whereHas('planSubscription', function ($query) {
                $query->active();
            });
        }
    }

    public function applyExpired($value): void
    {
        if (is_bool($value) || $value === 'true') {
            $this->teamsQuery->whereHas('planSubscription', function ($query) {
                $query->inactive();
            });
        }
    }

    public function applyTrial($value): void
    {
        if (is_bool($value) || $value === 'true') {
            $this->teamsQuery->whereHas('planSubscription', fn($query) => $query->where('on_trial', true));
        }
    }

    public function applyPlan($value): void
    {
        if (is_array($value)) {
            $values = array_map(fn($e) => intval($e), $value);

            $this->teamsQuery->whereHas('planSubscription', function ($query) use ($values) {
                $query->whereIn('plan_id', $values);
            });
        }
    }

    public function applySearch($value): void
    {
        if (is_string($value) && $value) {
            $this->teamsQuery->where(function ($teams) use ($value) {
                $teams->where('teams.name', 'like', "%$value%")
                    ->orWhereHas('users', function ($query) use ($value) {
                        $query->where('email', 'like', "%$value%")
                            ->where('role', 'admin');
                    })->orWhereHas('owner', fn($owner) => $owner->where('email', 'like', "%$value%"));
            });
        }
    }

    public function applySort($value): void
    {
        if (in_array($value, $this->allowedSorts)) {
            switch ($value) {
                case 'expires_soon':
                {
                    $this->teamsQuery
                        ->join('plan_subscriptions', 'plan_subscriptions.subscriber_id', '=', 'teams.id')
                        ->orderByRaw('DATE(`ends_at`) < CURDATE()')
                        ->orderBy('plan_subscriptions.ends_at')
                        ->select('teams.*');
                }
            }
        }
    }
}
