<?php

use Illuminate\Support\Carbon;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;
use NeonBang\LaravelCrudSourcing\Tests\Models\RecordLabel;
use NeonBang\LaravelCrudSourcing\Tests\Reports\DailyReport;

beforeEach(function () {
    Carbon::setTestNow('2024-07-01');

    RecordLabel::create(['name' => 'Foo Bar Records']);

    $recordLabel = RecordLabel::create(['name' => 'Underscore Rawhide Records']);

    $this->artist = Artist::create([
        'name' => 'The Bashdogs',
        'record_label_id' => $recordLabel->id,
    ]);

    $concertA = $this->artist->concerts()->create([
        'base_ticket_price' => 25.00,
    ]);
    $concertB = $this->artist->concerts()->create([
        'base_ticket_price' => 50.00,
    ]);

    // 7 Days of Sales
    for($i = 1; $i <= 7; $i++) {
        for ($j = 1; $j <= $i; $j++) {
            $concertA->sales()->create([
                'ticket_price' => $concertA->base_ticket_price,
                'created_at' => Carbon::now()->addDays($j),
            ]);
            $concertB->sales()->create([
                'ticket_price' => $concertB->base_ticket_price,
                'created_at' => Carbon::now()->addDays($j),
            ]);
        }
    }
});

it('can generate a trailing seven day daily report', function () {
    $report = DailyReport::run()
        ->orderBy('report_date')
        ->get();

    dd($report->toArray());
});
