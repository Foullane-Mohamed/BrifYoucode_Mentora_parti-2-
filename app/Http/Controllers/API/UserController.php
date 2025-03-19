<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserController constructor.
     * 
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('permission:user.view')->only(['index', 'show']);
        $this->middleware('permission:user.create')->only(['store']);
        $this->middleware('permission:user.edit')->only(['update']);
        $this->middleware('permission:user.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the users.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $users = $this->userService->getAllUsers();
            
            return response()->json([
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created user in storage.
     * 
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            
            $user = $this->userService->createUser($data);
            
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified user.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = $this->userService->getUserById($id);
            
            return response()->json([
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified user in storage.
     * 
     * @param \App\Http\Requests\User\UpdateUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            $result = $this->userService->updateUser($id, $data);
            
            if ($result) {
                return response()->json([
                    'message' => 'User updated successfully',
                    'user' => $this->userService->getUserById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating user',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $result = $this->userService->deleteUser($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'User deleted successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error deleting user',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get users by role.
     * 
     * @param string $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByRole($role)
    {
        try {
            $users = $this->userService->getUsersByRole($role);
            
            return response()->json([
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching users by role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the authenticated user's profile.
     * 
     * @param \App\Http\Requests\User\UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UpdateUserRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Remove role key if present as users shouldn't change their own roles
            if (isset($data['role'])) {
                unset($data['role']);
            }
            
            $result = $this->userService->updateUser(auth()->id(), $data);
            
            if ($result) {
                return response()->json([
                    'message' => 'Profile updated successfully',
                    'user' => $this->userService->getUserById(auth()->id()),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating profile',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}