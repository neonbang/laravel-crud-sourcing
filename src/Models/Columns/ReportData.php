<?php

namespace NeonBang\LaravelCrudSourcing\Models\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use NeonBang\LaravelCrudSourcing\Enums\Group;
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

    protected mixed $record = null;

    protected mixed $subject = null;

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

        $userReport = new $report;
        // $userReport->setEventModel($this->record);

        // If the subscriber has conditional logic on whether or not it should "include" the Event Model
        if ($this->record instanceof Model && method_exists($listener, 'include')) {
            if (!$listener->include($this->record)) {
                return;
            }
        }

        if ($this->isGrouped()) {
            $data = $listener->group($this->record);
        } else {
            $data = $listener->scope($this->subject, $this->record);
        }

        // $this->insert($report, $this, $data);
        $this->insert($userReport, $this, $data);

        // if ($this->rebuilding || $listener->include($this->record)) {
        //     if ($this->isGrouped()) {
        //         $data = $listener->group($this->record);
        //     } else {
        //         $data = $listener->scope($this->record, $this->subject);
        //     }
        //
        //     // $this->insert($report, $this, $data);
        //     $this->insert($userReport, $this, $data);
        // }
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
            $data = method_exists($report, $insertMethod = 'get' . Str::of($column->getColumnName())->studly()->toString() . 'Data')
                ? $report::$insertMethod($value)
                : [$column->getColumnName() => $value];
        }

        $defaults = method_exists($report, 'mergeDefaultData')
            ? $report->mergeDefaultData($this->subject, $this->groupBy)
            : $report::defaultData($this->subject, $this->groupBy);

        $report::query()
            ->updateOrCreate($defaults, $data);
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

    public function rebuildFrom(mixed $baseReportModel, mixed $report, string $eventModel = null): void
    {
        $this->run($baseReportModel, $report, $eventModel, true);
    }

    public function regroupFrom(mixed $subjectModel, mixed $report, $group, $since): void
    {
        /** @var Carbon $start */
        $start = $since;
        $end = now();

        // Use "from" and "group" to create a loop to send the $subscriber->group()
        $incrementByMinutes = match ($group) {
            Group::DAY => 60 * 24,
        };

        while ($start->startOfDay()->lessThanOrEqualTo($end->startOfDay())) {
            if ($group) {
                $this->by($group);
            }

            $modelStubForRebuild = new $subjectModel(['created_at' => $start]);

            $this->run($modelStubForRebuild, $report, $modelStubForRebuild, true);

            $start->addMinutes($incrementByMinutes);
        }
    }

    public function run(mixed $baseReportModel, mixed $report, mixed $eventModel = null, bool $rebuild = false): void
    {
        $this->rebuilding = $rebuild;

        $this->record = $eventModel;
        $this->subject = $baseReportModel;

        QueueColumn::dispatch($this, $report);
    }

    public function withValue(string $attribute): ReportData
    {
        $this->attribute = $attribute;
        return $this;
    }
}
