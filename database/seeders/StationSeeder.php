<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venue;

use Illuminate\Support\Facades\Hash;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Venue::create([
            'name' => 'Midvalley Mall KL',
        ]);

        Venue::create([
            'name' => 'Midvalley Southkey JB',
        ]);
    }
}
