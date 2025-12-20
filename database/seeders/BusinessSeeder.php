<?php

namespace Database\Seeders;

use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Container::getInstance()->make(Generator::class);
    }
}
