<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Admin Homigo',
            'email' => 'admin@homigo.com',
            'role' => 'admin',
        ]);


        $users = User::factory(10)->create();
        $listing = Listing::factory(10)->create();

        // jadi di factory Transaction ini kita membuat data 10, lalu kita inject user_id dan listing_id sesuai variabel $users dan $listing yang sudah kita buat menggunakan factory juga
        Transaction::factory(10)->state(new Sequence(fn (Sequence $sequence) => [
            'user_id' => $users->random(),
            'listing_id' => $listing->random(),
        ]))->create();

    }
}
