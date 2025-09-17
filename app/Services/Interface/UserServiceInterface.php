<?php

namespace App\Services\Interface;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserIndexRequest;
use Illuminate\Http\UploadedFile;

interface UserServiceInterface
{

    public function getAllUsers(StoreUserRequest $request);
    public function createUser(StoreUserRequest $request);
    public function getUserById(int $id, int $currentUserId, string $currentUserRole);
    public function updateUser(UpdateUserRequest $request, int $id, int $currentUserId, string $currentUserRole);
    public function deleteUser(int $id);
    public function restoreUser(int $id);
    public function forceDeleteUser(int $id);
}
