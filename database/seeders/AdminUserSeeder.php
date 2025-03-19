<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'bio' => 'Administrator of the platform',
            'skills' => json_encode(['Management', 'Leadership', 'System Administration']),
            'is_active' => true,
        ]);
        
        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser->roles()->attach($adminRole);
        
        // Create mentor user
        $mentorUser = User::create([
            'name' => 'Mentor User',
            'email' => 'mentor@example.com',
            'password' => Hash::make('password'),
            'bio' => 'Experienced teacher in web development',
            'skills' => json_encode(['Teaching', 'Web Development', 'JavaScript', 'PHP']),
            'is_active' => true,
        ]);
        
        // Assign mentor role
        $mentorRole = Role::where('name', 'mentor')->first();
        $mentorUser->roles()->attach($mentorRole);
        
        // Create student user
        $studentUser = User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'bio' => 'Eager to learn new skills',
            'skills' => json_encode(['HTML', 'CSS']),
            'is_active' => true,
        ]);
        
        // Assign student role
        $studentRole = Role::where('name', 'student')->first();
        $studentUser->roles()->attach($studentRole);
    }
}