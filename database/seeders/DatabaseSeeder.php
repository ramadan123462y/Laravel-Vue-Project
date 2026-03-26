<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            
        ]);
        // ── Managers ───────────────────────────────
        $managers = User::factory(5)->approved()->create();
        $managers->each(fn($m) => $m->assignRole('manager'));

        // ── Receptionists (created by managers) ────
        $managers->each(function ($manager) {
            $receptionists = User::factory(3)->approved()->create([
                'created_by' => $manager->id,
            ]);
            $receptionists->each(fn($r) => $r->assignRole('receptionist'));
        });

        // ── Clients ────────────────────────────────
        $receptionists = User::role('receptionist')->get();

        // Approved clients
        $receptionists->each(function ($receptionist) {
            $clients = User::factory(5)->approved()->assignCountryToClients()->create([
                'approved_by' => $receptionist->id,
            ]);
            $clients->each(fn($c) => $c->assignRole('client'));
        });

        // Pending clients (no approved_by)
        $pendingClients = User::factory(10)->pending()->create();
        $pendingClients->each(fn($c) => $c->assignRole('client'));
    }
}
