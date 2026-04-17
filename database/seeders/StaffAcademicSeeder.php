<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffAcademicSeeder extends Seeder
{
    /**
     * Seed akun default staff akademik.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'staff.akademik.si@telkomuniversity.ac.id'],
            [
                'name' => 'staffsi',
                'password' => Hash::make('StaffSI123!'),
            ]
        );
    }
}
