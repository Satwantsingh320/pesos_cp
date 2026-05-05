<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {

            DB::table('orders')->insert([
                'order_number' => $this->generateOrderNumber(),
                'customer_id'  => rand(2, 3),
                'product_id'   => rand(10,15),
                'price'        => rand(100, 5000),
                'order_status' => rand(0, 3),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    private function generateOrderNumber(): string
    {
        return strtoupper(Str::random(6));
    }
}
