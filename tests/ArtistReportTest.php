<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;

beforeAll(function () {

});

it('can create the artist report', function () {
    // The Report we want to generate:
    // [ ] next_album_release_date
    // [ ] next_album_title
    // [ ] total_tickets_revenue
    // [ ] total_tickets_sold_count
    // [ ] last_concert_date - Will do last as most likely ran/checked via cron
    // [ ] last_concert_venue - Will do last as most likely ran/checked via cron
    // [ ] last_concert_venue_city - Will do last as most likely ran/checked via cron
    // [ ] last_concert_tickets_sold_count - Will do last as most likely ran/checked via cron
    // [ ] last_concert_tickets_revenue - Will do last as most likely ran/checked via cron

    Carbon::setTestNow('2024-07-01');

    $artist = Artist::create(['name' => 'The Bashdogs']);

    // Our base data
    $artist->albums()->create([
        'title' => 'Introducting The Bashdogs',
        'release_date' => '2024-08-01',
    ]);

    // Some new updated data that should override our base data

    $report = \NeonBang\LaravelCrudSourcing\Tests\Reports\ArtistReport::for($artist);

    expect($report)->not()->toBeNull();
});
