<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Elquent\UserRepository;
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
    protected UserRepositoryInterface $userRepository;
    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search , status']);
        $perPage = $request->get('per_page',15);

        $users = $this->userRepository->paginate($perPage, $filters);

         return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => new UserCollection($users)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // validation request da . chi lay 1 so truong can thiet trong request
        $data = $request->validated();

        $user = $this->userRepository->create($data);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
       $user = $this->userRepository->find($id);
       if(!$user){
        return response()->json(['message' => 'User not fount']);
       }
        return response()->json([
            'success' => true,
            'message' => 'okay user',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        // can them check quyen la admin  khong . Hien tai chua co
         $user = $this->userRepository->find($id);

        // Users can only update their own profile unless they're admin/moderator
        if (!auth()->user()->isAdmin() &&
            !in_array(auth()->user()->role, ['admin', 'moderator']) &&
            auth()->id() !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions'
            ], 403);
        }
        $data = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = $this->userRepository->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user)
        ]);

    }

    /**
     * Remove the specified resource from storage.
     * using Soft Delete
     */

    public function destroy(int $id)
    {
        $this->userRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);

    }
    public function restore(int $id)
    {
       $this->userRepository->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'User restored successfully'
        ]);
    }
    public function forceDelete(int $id)
    {
        if (!$this->userRepository->forceDelete($id)) {
            return response()->json(['message' => 'Failed to force delete user'], 500);
        }
        return response()->json(['message' => 'User permanently deleted']);
    }

}
