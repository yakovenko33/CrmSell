<?php

namespace CrmSell\Users\Infrastructure\Repositories;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Users\Infrastructure\Repositories\Interfaces\UsersRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsersRepository implements UsersRepositoryInterface
{
    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     * @throws \Exception
     */
    public function getListUsers(GetListDTO $getListDTO): Collection
    {
        try {
            $result = DB::table('users')
                ->select(['id', 'first_name', 'last_name', 'email', 'status',  'created_at', 'updated_at'])
                ->limit($getListDTO->getPagination()->getLimit())
                ->offset($getListDTO->getPagination()->getOffset())
                ->orderBy($getListDTO->getSortField(), $getListDTO->getSortDir())
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("UsersRepository::getAllCitiesByRegionId() error.");
        }

        return $result;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getCountForListUsers(): int
    {
        try {
            $result = DB::table('users')->select(['id'])->count();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("UsersRepository:: getCountForListUsers() error.");
        }

        return $result;
    }

    /**
     * @param string $userId
     * @return Collection
     * @throws \Exception
     */
    public function getUsersRolesList(string $userId): Collection
    {
        try {
            $result = DB::table('users as u')
                ->select(['r.id as roleId', 'r.name as roleName',])
                ->join('model_has_roles as m_h_r', 'u.id', '=', 'm_h_r.model_uuid')
                ->join('roles as r', 'r.id', '=', 'm_h_r.role_id')
                ->where("u.id", $userId)
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("UsersRepository::getUsersRolesList() error.");
        }

        return $result;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getListAll(): Collection
    {
        try {
            $result = DB::table('users')
                ->select(['id', 'first_name', 'last_name'])
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("UsersRepository::getAllCitiesByRegionId() error.");
        }

        return $result;
    }
}
