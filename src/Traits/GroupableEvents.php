<?php

namespace NeonBang\LaravelCrudSourcing\Traits;

use NeonBang\LaravelCrudSourcing\Enums\Group;

trait GroupableEvents
{
    protected ?Group $groupBy = null;

    public function by(Group $group)
    {
        $this->groupBy = $group;
        return $this;
    }

    public function getGroupBy(): ?Group
    {
        return $this->groupBy;
    }

    public function isGrouped(): bool
    {
        return !! $this->groupBy;
    }
}
