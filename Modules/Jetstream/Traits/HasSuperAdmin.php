<?php

namespace Modules\Jetstream\Traits;

trait HasSuperAdmin
{
    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_admin;
    }
}
