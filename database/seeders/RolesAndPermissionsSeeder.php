<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Illuminate\Cache\CacheManager::class]->forget('spatie.permission.cache');
        
        // Create permissions
        $permissions = [
            // User permissions
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            
            // Category permissions
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
            
            // Subcategory permissions
            'subcategory.view',
            'subcategory.create',
            'subcategory.edit',
            'subcategory.delete',
            
            // Tag permissions
            'tag.view',
            'tag.create',
            'tag.edit',
            'tag.delete',
            
            // Course permissions
            'course.view',
            'course.create',
            'course.edit',
            'course.delete',
            'course.publish',
            
            // Video permissions
            'video.view',
            'video.create',
            'video.edit',
            'video.delete',
            
            // Enrollment permissions
            'enrollment.view',
            'enrollment.create',
            'enrollment.edit',
            'enrollment.delete',
            'enrollment.approve',
            
            // Statistics permissions
            'statistics.view',
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Create roles and assign permissions
        $roles = [
            'admin' => [
                'description' => 'Administrator with all permissions',
                'permissions' => $permissions
            ],
            'mentor' => [
                'description' => 'Can create and manage own courses',
                'permissions' => [
                    'course.view',
                    'course.create',
                    'course.edit',
                    'video.view',
                    'video.create',
                    'video.edit',
                    'video.delete',
                    'enrollment.view',
                    'enrollment.approve',
                    'tag.view',
                    'category.view',
                    'subcategory.view',
                ]
            ],
            'student' => [
                'description' => 'Can view and enroll in courses',
                'permissions' => [
                    'course.view',
                    'enrollment.create',
                    'tag.view',
                    'category.view',
                    'subcategory.view',
                ]
            ],
        ];
        
        foreach ($roles as $name => $role) {
            $createdRole = Role::create([
                'name' => $name,
                'description' => $role['description'],
            ]);
            
            // Attach permissions to role
            $permissionsToAttach = Permission::whereIn('name', $role['permissions'])->get();
            $createdRole->permissions()->attach($permissionsToAttach);
        }
    }
}