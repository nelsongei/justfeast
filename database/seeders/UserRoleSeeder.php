<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Seed users by role:
     *  - 1 Admin
     *  - 2 Runners
     *  - 3 Vendors
     *  - 5 Clients
     *
     * All passwords default to: password
     */
    public function run(): void
    {
        // ─── Admin ───────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@justfeast.com'],
            [
                'name'     => 'Sarah Admin',
                'phone'    => '0742000001',
                'role'     => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // ─── Runners ─────────────────────────────────────────────────────────
        $runners = [
            ['name' => 'Mike Runner',  'email' => 'runner1@justfeast.com', 'phone' => '0732000001'],
            ['name' => 'Jane Runner',  'email' => 'runner2@justfeast.com', 'phone' => '0732000002'],
        ];

        foreach ($runners as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'phone'    => $data['phone'],
                    'role'     => 'runner',
                    'password' => Hash::make('password'),
                ]
            );
        }

        // ─── Vendors ─────────────────────────────────────────────────────────
        $vendors = [
            ['name' => 'James Vendor (Burger World)', 'email' => 'burger@justfeast.com',  'phone' => '0722000001'],
            ['name' => 'Maria Vendor (Taco Fiesta)',  'email' => 'taco@justfeast.com',    'phone' => '0722000002'],
            ['name' => 'David Vendor (Choma Zone)',   'email' => 'choma@justfeast.com',   'phone' => '0722000003'],
        ];

        foreach ($vendors as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'phone'    => $data['phone'],
                    'role'     => 'vendor',
                    'password' => Hash::make('password'),
                ]
            );
        }

        // ─── Clients ─────────────────────────────────────────────────────────
        $clients = [
            ['name' => 'Alice Njeri',   'email' => 'alice@justfeast.com',   'phone' => '0711000001'],
            ['name' => 'Brian Otieno',  'email' => 'brian@justfeast.com',   'phone' => '0711000002'],
            ['name' => 'Clara Wanjiku', 'email' => 'clara@justfeast.com',   'phone' => '0711000003'],
            ['name' => 'Dennis Kamau', 'email' => 'dennis@justfeast.com',   'phone' => '0711000004'],
            ['name' => 'Eva Achieng',   'email' => 'eva@justfeast.com',     'phone' => '0711000005'],
        ];

        foreach ($clients as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'phone'    => $data['phone'],
                    'role'     => 'client',
                    'password' => Hash::make('password'),
                ]
            );
        }

        $this->command->info('✅ UserRoleSeeder: 1 admin, 2 runners, 3 vendors, 5 clients seeded.');
    }
}
