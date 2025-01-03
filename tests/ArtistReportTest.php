<?php

use Illuminate\Support\Carbon;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;
use NeonBang\LaravelCrudSourcing\Tests\Models\RecordLabel;
use NeonBang\LaravelCrudSourcing\Tests\Reports\ArtistReport;

beforeEach(function () {
    // The Report we want to generate:
    // [x] current_record_label - This is primarily to have a related model's SINGLE value
    // [x] next_album_release_date
    // [x] next_album_title
    // [x] total_tickets_revenue
    // [x] total_tickets_sold_count
    // [ ] last_concert_date - Will do last as most likely ran/checked via cron
    // [ ] last_concert_venue - Will do last as most likely ran/checked via cron
    // [ ] last_concert_venue_city - Will do last as most likely ran/checked via cron
    // [ ] last_concert_tickets_sold_count - Will do last as most likely ran/checked via cron
    // [ ] last_concert_tickets_revenue - Will do last as most likely ran/checked via cron

    Carbon::setTestNow('2024-07-01');

    RecordLabel::create(['name' => 'Foo Bar Records']);

    $recordLabel = RecordLabel::create(['name' => 'Underscore Rawhide Records']);

    $this->artist = Artist::create([
        'name' => 'The Bashdogs',
        'record_label_id' => $recordLabel->id,
    ]);
    $this->artist->albums()->create([
        'title' => 'Introducting The Bashdogs',
        'release_date' => '2024-08-01',
    ]);
    // Tickets and Sales
    for ($i = 1; $i <= 5; $i++) {
        $concert = $this->artist->concerts()->create([
            'base_ticket_price' => 25.00,
        ]);
        for ($j = 1; $j <= $i; $j++) {
            $concert->sales()->create([
                'ticket_price' => 25.00,
            ]);
        }
    }
});

it('can create the artist report', function () {
    $report = ArtistReport::for($this->artist);

    expect($report)->not()->toBeNull();
    expect($report->record_label)->toBe('Underscore Rawhide Records');
    expect($report->next_album_release_date)->toBe('2024-08-01 00:00:00');
    expect($report->next_album_title)->toBe('Introducting The Bashdogs');
    expect($report->total_tickets_revenue)->toBe(375.00);
    expect($report->total_tickets_sold_count)->toBe(15);
});

it('can rebuild the artist report via artist model', function () {
    ArtistReport::query()->truncate();

    ArtistReport::rebuildFor($this->artist);

    $report = ArtistReport::for($this->artist);

    expect($report)->not()->toBeNull();
    expect($report->record_label)->toBe('Underscore Rawhide Records');
    expect($report->next_album_release_date)->toBe('2024-08-01 00:00:00');
    expect($report->next_album_title)->toBe('Introducting The Bashdogs');
    expect($report->total_tickets_revenue)->toBe(375.00);
    expect($report->total_tickets_sold_count)->toBe(15);
});

it('can rebuild the artist report via a related album model', function () {
    // ArtistReport::query()
    //     ->first()
    //     ->update([
    //         'record_label' => null,
    //         'next_album_title' => null,
    //     ]);
    //
    // ArtistReport::recalculateFor($this->artist->load('label')->label);

    $report = ArtistReport::for($this->artist);

    expect($report->record_label)->toBe('Underscore Rawhide Records');

    $recordLabel = RecordLabel::find($this->artist->record_label_id);

    $recordLabel->update([
        'name' => 'Newer Digital Records',
    ]);

    $report = ArtistReport::for($this->artist);

    expect($report)->not()->toBeNull();
    expect($report->record_label)->toBe('Newer Digital Records');
    expect($report->next_album_release_date)->toBe('2024-08-01 00:00:00');
    expect($report->next_album_title)->toBe('Introducting The Bashdogs');
    expect($report->total_tickets_revenue)->toBe(375.00);
    expect($report->total_tickets_sold_count)->toBe(15);
});
