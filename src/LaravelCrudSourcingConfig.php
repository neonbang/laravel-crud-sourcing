<?php

namespace NeonBang\LaravelCrudSourcing;

class LaravelCrudSourcingConfig
{
    protected static array $disabledReports = [];

    public function disable(string $report): void
    {
        self::$disabledReports[] = $report;
    }

    public function isDisabled(string $report): bool
    {
        return in_array($report, self::$disabledReports);
    }
}
