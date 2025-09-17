<?php

namespace App\Http\Controllers;

use App\Services\Interface\UserServiceInterface;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Elquent\UserRepository;
use Exception;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // DI (dependency ...)
    protected UserServiceInterface $userService;
    public function __construct(UserServiceInterface $userService){
        $this->userService = $userService;
    }

    public function index(StoreUserRequest $request)
    {
       try {
            $users = $this->userService->getAllUsers($request);

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => new UserCollection($users)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
       try {
            $user = $this->userService->createUser($request);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => new UserResource($user)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
      try {
        //lay user theo id nguoi dung , kiem tra role ,
            $user = $this->userService->getUserById(
                $id,
                auth()->id(),
                auth()->user()->role
            );

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => new UserResource($user)
            ]);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
          try {
            $user = $this->userService->updateUser(
                $request,
                $id,
                auth()->id(),
                auth()->user()->role
            );

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($user)
            ]);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * Remove the specified resource from storage.
     * using Soft Delete
     */

    public function destroy(int $id)
    {
        try {
            $this->userService->deleteUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }
    public function restore(int $id)
    {
        try {
            $this->userService->restoreUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User restored successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function forceDelete(int $id)
    {
        try {
            $this->userService->forceDeleteUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User permanently deleted'
            ]);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }


}