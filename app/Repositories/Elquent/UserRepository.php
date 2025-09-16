<?php

namespace App\Repositories\Elquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserRepository implements UserRepositoryInterface
{
     protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

     public function all(array $filters = [])
    {
         return $this->applyFilters($this->model->query(), $filters)->get();

    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $user = $this->find($id);
        $user->update($data);
        return $user->fresh();
    }

    public function delete(int $id)
    {
        $user = $this->find($id);
        return $user->delete();
    }

    public function restore(int $id)
    {
        $user = $this->model->withTrashed()->findOrFail($id);
        return $user->restore();
    }

    public function forceDelete(int $id)
    {
        $user = $this->model->withTrashed()->findOrFail($id);
        return $user->softDeletes();
    }

    public function find(int $id)
    {
        return $this->model->withTrashed()->findOrFail($id);
    }
    public function finByEmail(string $email)
    {
        return $this->model->where('email', $email)->filters();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->applyFilters($this->model->query(), $filters)
        ->paginate($perPage);
    }

    // dung khi search vi du getall() theo dieu kien nao do
     protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['role'])) {
            $query->byRole($filters['role']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['verified'])) {
            if ($filters['verified'] === 'true') {
                $query->verified();
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if (!empty($filters['include_deleted'])) {
            $query->withTrashed();
        }

        return $query;
    }
}
