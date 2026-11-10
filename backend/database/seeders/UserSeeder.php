<?php

namespace Database\Seeders;

use App\Models\Operator;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        User::updateOrCreate(
            ['email' => 'admin@mpbussaathi.in'],
            [
                'name' => 'MP Transit Admin (प्रशासक)',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'operator_id' => null,
            ]
        );

        // 2. Operator Users linked to existing Operators
        $operators = Operator::all();

        foreach ($operators as $op) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', substr($op->name, 0, 8)));
            User::updateOrCreate(
                ['email' => "operator.{$op->id}@mpbussaathi.in"],
                [
                    'name' => "{$op->contact_person} ({$op->name})",
                    'password' => Hash::make('password123'),
                    'role' => 'operator',
                    'operator_id' => $op->id,
                ]
            );
        }
    }
}

