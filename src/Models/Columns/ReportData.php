<?php

namespace NeonBang\LaravelCrudSourcing\Models\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use NeonBang\LaravelCrudSourcing\Jobs\QueueColumn;

class ReportData
{
    protected ?string $listenerCallback = null;

    protected ?Model $record = null;

    public function __construct(protected string $columnName)
    {
    }

    public static function make(string $columnName): ReportData
    {
        return new static($columnName);
    }

    public function action(string $action): ReportData
    {
        $this->listenerCallback = $action;
        return $this;
    }

    public function calculate(mixed $report): void
    {
        $listener = new $this->listenerCallback;
        $listener($this->record, $report, $this);
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function insert($report, $column, $value = null)
    {
        $data = method_exists($report, $insertMethod = 'get'.Str::of($column->getColumnName())->studly()->toString().'Data')
            ? $report::$insertMethod($value)
            : [$column->getColumnName() => $value];

        $report::query()
            ->create(array_merge($report::defaultData($this->record), $data));
    }

    public function run(mixed $model, mixed $report): void
    {
        $this->record = $model;

        QueueColumn::dispatch($this, $report);
    }
}
