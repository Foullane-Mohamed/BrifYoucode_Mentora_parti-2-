<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * UserService constructor.
     * 
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers()
    {
        return $this->userRepository->all([], ['roles']);
    }

    /**
     * Get user by ID.
     * 
     * @param int $id
     * @return User
     */
    public function getUserById($id)
    {
        return $this->userRepository->findById($id, ['*'], ['roles']);
    }

    /**
     * Create new user.
     * 
     * @param array $data
     * @return User
     */
    public function createUser(array $data)
    {
        // Handle password
        $data['password'] = Hash::make($data['password']);
        
        // Handle avatar if provided
        if (isset($data['avatar']) && $data['avatar']) {
            $avatar = $data['avatar'];
            $filename = Str::random(20) . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('public/avatars', $filename);
            $data['avatar'] = 'avatars/' . $filename;
        }
        
        // Handle skills if provided
        if (isset($data['skills']) && is_array($data['skills'])) {
            $data['skills'] = json_encode($data['skills']);
        } else {
            $data['skills'] = json_encode([]);
        }
        
        // Create user
        $user = $this->userRepository->create($data);
        
        // Assign role
        if (isset($data['role'])) {
            $this->userRepository->assignRole($user->id, $data['role']);
        } else {
            // By default, assign 'student' role
            $this->userRepository->assignRole($user->id, 'student');
        }
        
        return $user;
    }

    /**
     * Update user.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateUser($id, array $data)
    {
        $user = $this->userRepository->findById($id);
        
        // Handle password if provided
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        // Handle avatar if provided
        if (isset($data['avatar']) && $data['avatar']) {
            // Delete old avatar
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }
            
            $avatar = $data['avatar'];
            $filename = Str::random(20) . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('public/avatars', $filename);
            $data['avatar'] = 'avatars/' . $filename;
        }
        
        // Handle skills if provided
        if (isset($data['skills']) && is_array($data['skills'])) {
            $data['skills'] = json_encode($data['skills']);
        }
        
        // Update user
        $result = $this->userRepository->update($id, $data);
        
        // Handle role if provided
        if (isset($data['role'])) {
            // Get all current roles
            $currentRoles = $user->roles->pluck('name')->toArray();
            
            // Remove current roles
            foreach ($currentRoles as $role) {
                $this->userRepository->removeRole($id, $role);
            }
            
            // Assign new role
            $this->userRepository->assignRole($id, $data['role']);
        }
        
        return $result;
    }

    /**
     * Delete user.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteUser($id)
    {
        return $this->userRepository->deleteById($id);
    }

    /**
     * Get users by role.
     * 
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole($role)
    {
        return $this->userRepository->getUsersByRole($role);
    }

    /**
     * Check if user has role.
     * 
     * @param int $userId
     * @param string $roleName
     * @return bool
     */
    public function userHasRole($userId, $roleName)
    {
        return $this->userRepository->hasRole($userId, $roleName);
    }

    /**
     * Check if user has permission.
     * 
     * @param int $userId
     * @param string $permissionName
     * @return bool
     */
    public function userHasPermission($userId, $permissionName)
    {
        return $this->userRepository->hasPermission($userId, $permissionName);
    }
}