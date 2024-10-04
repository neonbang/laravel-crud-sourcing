<?php

namespace NeonBang\LaravelCrudSourcing\Models\Columns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use NeonBang\LaravelCrudSourcing\Jobs\QueueColumn;
use NeonBang\LaravelCrudSourcing\Tests\Reports\ArtistReport;
use NeonBang\LaravelCrudSourcing\Traits\EloquentEvents;

class ReportData
{
    use EloquentEvents;

    protected ?string $listenerCallback = null;

    protected bool $rebuilding = false;

    protected ?string $subjectPath = null;

    protected ?Model $record = null;

    protected mixed $transformer = null;

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

    public function getAction(): string
    {
        return $this->listenerCallback;
    }

    public function getRelatedModel($subjectModel)
    {
        return $this->listenerCallback::getSubjectModel($subjectModel);
    }

    public function calculate(mixed $report): void
    {
        $listener = new $this->listenerCallback;

        if ($this->rebuilding || $listener->include($this->record)) {
            $data = $listener->scope($this->record);
            $this->insert($report, $this, $data);
        }
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function insert($report, $column, $value = null)
    {
        $record = ! $this->rebuilding ? $this->record : $this->listenerCallback::subjectModelNormalizer($this->record);

        if ($this->transformer) {
            $transformer = new $this->transformer;
            $data = $transformer($record);
        } else {
            $data = method_exists($report, $insertMethod = 'get'.Str::of($column->getColumnName())->studly()->toString().'Data')
                ? $report::$insertMethod($value)
                : [$column->getColumnName() => $value];
        }

        $report::query()
            ->updateOrCreate($report::defaultData($this->record, $this->subjectPath), $data);
    }

    public function subjectPath(string $subjectPath): ReportData
    {
        $this->subjectPath = $subjectPath;
        return $this;
    }

    public function transform($transformCallback): ReportData
    {
        $this->transformer = $transformCallback;
        return $this;
    }

    public function rebuild(mixed $model, mixed $report): void
    {
        $this->run($model, $report, true);
    }

    public function run(mixed $model, mixed $report, bool $rebuild = false): void
    {
        $this->rebuilding = $rebuild;
        $this->record = $model;

        QueueColumn::dispatch($this, $report);
    }
}
