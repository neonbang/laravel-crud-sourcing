<?php

namespace NeonBang\LaravelCrudSourcing\Models\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NeonBang\LaravelCrudSourcing\Jobs\QueueColumn;
use NeonBang\LaravelCrudSourcing\Traits\EloquentEvents;
use NeonBang\LaravelCrudSourcing\Traits\GroupableEvents;

class ReportData
{
    use EloquentEvents;
    use GroupableEvents;

    protected ?string $attribute = null;

    protected ?string $listenerCallback = null;

    protected bool $rebuilding = false;

    protected ?string $subjectPath = null;

    protected ?Model $record = null;

    protected ?Model $subject = null;

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

    public function getSubjectPath(): ?string
    {
        return $this->subjectPath;
    }

    public function calculate(mixed $report): void
    {
        $listener = new $this->listenerCallback;

        if ($this->rebuilding || $listener->include($this->record)) {
            if ($this->isGrouped()) {
                $data = $listener->group($this->record);
            } else {
                $data = $listener->scope($this->record, $this->subject);
            }

            $this->insert($report, $this, $data);
        }
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function insert($report, $column, $value = null)
    {
        // @todo Ick
        if ($value && $this->attribute) {
            $value = $value->{$this->attribute};
        }

        if ($this->transformer) {
            $transformer = new $this->transformer;
            // @todo Ick
            $data = $transformer($this->rebuilding || $this->isGrouped() ? $value : $this->record);
        } else {
            $data = method_exists($report, $insertMethod = 'get'.Str::of($column->getColumnName())->studly()->toString().'Data')
                ? $report::$insertMethod($value)
                : [$column->getColumnName() => $value];
        }

        $report::query()
            ->updateOrCreate($report::defaultData($this->subject, $this->groupBy), $data);
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

    public function rebuildFrom(mixed $subjectModel, mixed $report, string $relationshipTrace = null): void
    {
        $this->run($subjectModel, $report,  $subjectModel, true);
    }

    public function run(mixed $model, mixed $report, mixed $subjectModel = null, bool $rebuild = false): void
    {
        $this->rebuilding = $rebuild;

        $this->record = $model;
        $this->subject = $subjectModel;

        QueueColumn::dispatch($this, $report);
    }

    public function withValue(string $attribute): ReportData
    {
        $this->attribute = $attribute;
        return $this;
    }
}
