<?php

namespace NeonBang\LaravelCrudSourcing\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use NeonBang\LaravelCrudSourcing\LaravelCrudSourcingServiceProvider;
use NeonBang\LaravelCrudSourcing\Tests\Reports\ArtistReport;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('crud-sourcing.reports', [ArtistReport::class]);

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelCrudSourcingServiceProvider::class,
        ];
    }

    private function setUpDatabase()
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('concerts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('artist_id')->index();
            $table->dateTime('doors_at')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('concert_id')->index();
            $table->timestamps();
        });

        Schema::create('venues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });

        Schema::create('pivot_concert_venue', function (Blueprint $table) {
            $table->unsignedBigInteger('concert_id')->index();
            $table->unsignedBigInteger('venue_id')->index();
            $table->timestamps();
        });

        Schema::create('albums', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('artist_id')->index();
            $table->string('title')->nullable();
            $table->date('release_date')->nullable();
            $table->timestamps();
        });

        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('album_id')->index();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        // Creates the report table: report_artist_aggregate_data
        Schema::create('report_artist_aggregate_data', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('artist_id')->index();
            $table->date('next_album_release_date')->nullable();
            $table->timestamps();
        });
    }
}
