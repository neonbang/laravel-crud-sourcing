<?php

namespace NeonBang\LaravelCrudSourcing\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueColumn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public mixed $subscriber,
        public mixed $report
    )
    {
    }

    public function handle(): void
    {
        $this->subscriber->calculate($this->report);
    }
}
