<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            SubscriptionPlanSeeder::class,
            PermissionSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
