<?php

namespace App\Services;

use App\Services\Interface\UserServiceInterface;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserIndexRequest;
use App\Models\User;
use App\Repositories\Elquent\UserRepository;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService implements UserServiceInterface
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function getAllUsers(StoreUserRequest $request)
    {

        $filters = $request->validated();
        $perPage = $request->get('per_page', 15);

        return $this->userRepository->paginate(15, []);
    }

    /**
     * / Create new User
     * Xu ly Hash password
     * handle avatar
     * set default status role
     * @param \App\Http\Requests\StoreUserRequest $request
     * @return  \App\Models\User
     */
    public function createUser(StoreUserRequest $request)
    {
        try{
            // thong bao de DB chuan bi san
            DB::beginTransaction();

            $data = $request->validated();
            //Hash password
            if(isset($data['password'])){
                $data['password'] = Hash::make($data['password']);
            }
            // handle upload avatar trong truong hop avatar bi trung lap

            //set default values status and role
            $data['status'] = $data['status'] ?? 'active';
            $data['role'] = $data['role'] ?? 'user';

            $user = $this->userRepository->create($data);

            // Luu thay doi trong database .
            DB::commit();

            return $user;
        }catch(Exception $e){
            DB::rollBack();

            throw $e;
        }
    }

    public function getUserById(int $id, int $currentUserId, string $currentUserRole)
    {

           $user = $this->userRepository->find($id);

        return $user;
    }
    public function updateUser(UpdateUserRequest $request, int $id, int $currentUserId, string $currentUserRole)
    {
         try {
            DB::beginTransaction();

            $user = $this->userRepository->find($id);

            // Check permissions
            if (!$this->canUpdateUser($user, $currentUserId, $currentUserRole)) {
                throw new Exception('Insufficient permissions', 403);
            }

            $data = $request->validated();

            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $oldAvatar = $user->avatar;

            // kiem tra  role/status
            if ($currentUserId === $user->id && !in_array($currentUserRole, ['admin'])) {
                unset($data['role'], $data['status']);
            }

            // kiem tra role == admin hien tia va role muon thay doi
            if ($user->role === 'admin' && $currentUserRole !== 'admin') {
                throw new Exception('Cannot modify admin users', 403);
            }

            $updatedUser = $this->userRepository->update($id, $data);

            DB::commit();

            return $updatedUser;
        } catch (Exception $e) {
            DB::rollback();

            if (isset($data['avatar']) && $data['avatar'] !== $oldAvatar) {
                Storage::disk('public')->delete($data['avatar']);
            }

            throw $e;
        }
    }
    public function deleteUser(int $id)
    {
                $user = $this->userRepository->find($id);

        // Prevent deleting admin users
        if ($user->role === 'admin') {
            throw new Exception('Cannot delete admin users', 403);
        }

        return $this->userRepository->delete($id);
    }

    public function restoreUser(int $id)
    {
        return $this->userRepository->restore($id);
    }
// delte luong khong dung soft delete . khong the backup
    public function forceDeleteUser(int $id)
    {
        try {
            DB::beginTransaction();

            $user = $this->userRepository->find($id);

            // Prevent force deleting admin users
            if ($user->role === 'admin') {
                throw new Exception('Cannot permanently delete admin users', 403);
            }

            // Delete avatar file trong thu muc public
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $result = $this->userRepository->forceDelete($id);

            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
