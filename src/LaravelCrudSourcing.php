<?php

namespace NeonBang\LaravelCrudSourcing;

class LaravelCrudSourcing
{

    public function __construct(protected LaravelCrudSourcingConfig $config)
    {
    }

    public function disable(string $report): void
    {
        $this->config->disable($report);
    }

    public function enable(string $report): void
    {
        $this->config->enable($report);
    }

    public function isDisabled(string $report): bool
    {
        return $this->config->isDisabled($report);
    }
}
