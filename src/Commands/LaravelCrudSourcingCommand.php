<?php

namespace NeonBang\LaravelCrudSourcing\Commands;

use Illuminate\Console\Command;

class LaravelCrudSourcingCommand extends Command
{
    public $signature = 'laravel-crud-sourcing';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
