<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Hanya buat user dasar saja
        $admin = User::firstOrCreate(
            ['email' => 'admin@otakatik.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'is_instructor' => false,
                'age_range' => '35-44',
                'education_level' => 'Bachelor',
                'location' => 'Jakarta',
                'phone' => '+62 812-3456-7890',
                'date_of_birth' => '1985-01-01',
                'bio' => 'System Administrator',
                'expertise' => 'System Management',
                'email_verified_at' => now(),
            ]
        );

        $instructor = User::firstOrCreate(
            ['email' => 'instructor@otakatik.com'],
            [
                'name' => 'John Instructor',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_instructor' => true,
                'age_range' => '35-44',
                'education_level' => 'Master',
                'location' => 'Bandung',
                'phone' => '+62 811-2233-4455',
                'date_of_birth' => '1988-05-15',
                'bio' => 'Experienced web development instructor.',
                'expertise' => 'Web Development, JavaScript, PHP',
                'email_verified_at' => now(),
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_instructor' => false,
                'age_range' => '25-34',
                'education_level' => 'Bachelor',
                'location' => 'Jakarta',
                'phone' => '+62 833-4455-6677',
                'date_of_birth' => '1995-03-10',
                'bio' => 'Regular user for testing.',
                'expertise' => 'Student',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Seeder berhasil!');
        $this->command->info('Admin: admin@otakatik.com / password');
        $this->command->info('Instructor: instructor@otakatik.com / password');
        $this->command->info('User: user@example.com / password');
        $this->command->info('---');
        $this->command->info('SEMUA COURSE DIBUAT MANUAL OLEH ADMIN!');
        $this->command->info('Login sebagai admin dan buat course pertama Anda.');
    }
}