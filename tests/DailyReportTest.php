<?php

use Illuminate\Support\Carbon;
use NeonBang\LaravelCrudSourcing\Enums\Group;
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

    $this->concertA = $this->artist->concerts()->create([
        'base_ticket_price' => 25.00,
    ]);
    $this->concertB = $this->artist->concerts()->create([
        'base_ticket_price' => 50.00,
    ]);

    // 7 Days of Sales:
    // - Day 1: (1 X 25) + (1 X 50) = 75 (2 tickets)
    // - Day 2: (2 X 25) + (2 X 50) = 150 (4 tickets)
    // - Day 3: (3 X 25) + (3 X 50) = 225 (6 tickets)
    // - Day 4: (4 X 25) + (4 X 50) = 300 (8 tickets)
    // - Day 5: (5 X 25) + (5 X 50) = 375 (10 tickets)
    // - Day 6: (6 X 25) + (6 X 50) = 450 (12 tickets)
    // - Day 7: (7 X 25) + (7 X 50) = 525 (14 tickets)
    // Total: 1,750 (56 tickets)
    for($i = 1; $i <= 7; $i++) {
        $today = Carbon::now()->addDays($i);
        for ($j = 1; $j <= $i; $j++) {
            $this->concertA->sales()->create([
                'ticket_price' => $this->concertA->base_ticket_price,
                'created_at' => $today,
            ]);
        }
        for ($j = 1; $j <= $i; $j++) {
            $this->concertB->sales()->create([
                'ticket_price' => $this->concertB->base_ticket_price,
                'created_at' => $today,
            ]);
        }
    }
});

it('can generate a trailing seven day daily report', function () {
    $report = DailyReport::run()
        ->orderBy('id')
        ->get();

    expect($report[0])->toMatchArray([
        'group_type' => DailyReport::class,
        'group_by_type' => Group::DAY->value,
        'group_by' => '2024_07_02',
        'total_tickets_revenue' => 75.00,
        'total_tickets_sold_count' => 2,
    ])
        // Assuming too much?
        ->and($report[6])->toMatchArray([
            'group_type' => DailyReport::class,
            'group_by_type' => Group::DAY->value,
            'group_by' => '2024_07_08',
            'total_tickets_revenue' => 525.00,
            'total_tickets_sold_count' => 14,
        ]);
});

it('can adjust the initial report accordingly', function () {
    $this->concertA->sales()->create([
        'ticket_price' => 100,  // Inflated price!
        'created_at' => '2024-07-02',
    ]);

    $report = DailyReport::run()
        ->where('group_by', '2024_07_02')
        ->first();

    expect($report->toArray())->toMatchArray([
        'group_type' => DailyReport::class,
        'group_by_type' => Group::DAY->value,
        'group_by' => '2024_07_02',
        'total_tickets_revenue' => 175.00,
        'total_tickets_sold_count' => 3,
    ]);
});
