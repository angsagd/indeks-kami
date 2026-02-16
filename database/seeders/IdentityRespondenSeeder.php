<?php

namespace Database\Seeders;

use App\IdentitasResponden;
use Illuminate\Database\Seeder;

class IdentityRespondenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IdentitasResponden::factory()->count(1)->create();
    }
}
