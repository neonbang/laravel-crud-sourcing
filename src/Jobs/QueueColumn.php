<?php

namespace NeonBang\LaravelCrudSourcing\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NeonBang\LaravelCrudSourcing\Aggregates\BelongsToBase;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportData;

class QueueColumn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public BelongsToBase|ReportData $subscriber,
        public mixed                    $report = null
    )
    {
    }

    public function handle(): void
    {
        $this->subscriber->handle();
    }
}
