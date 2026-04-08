<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'            => 'Admin LKS',
                'email'           => 'admin@lks.test',
                'password'        => 'password',
                'role'            => 'admin',
                'monthly_revenue' => 100_000_000,
            ],
            [
                'name'            => 'Applicant LKS',
                'email'           => 'applicant@lks.test',
                'password'        => 'password',
                'role'            => 'applicant',
                'business_name'   => 'UD Maju Jaya',
                'monthly_revenue' => 50_000_000,
            ],
            
        ];

        foreach ($users as $data) {
            User::create($data);
        }
    }
}