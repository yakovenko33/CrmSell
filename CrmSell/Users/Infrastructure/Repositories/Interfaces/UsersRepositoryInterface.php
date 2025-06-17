<?php

namespace CrmSell\Users\Infrastructure\Repositories\Interfaces;

use CrmSell\Common\Application\Service\DTO\GetListDTO;
use Illuminate\Support\Collection;

interface UsersRepositoryInterface
{
    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     */
    public function getListUsers(GetListDTO $getListDTO): Collection;

    /**
     * @return int
     */
    public function getCountForListUsers(): int;

    /**
     * @param string $userId
     * @return Collection
     */
    public function getUsersRolesList(string $userId): Collection;

    /**
     * @return Collection
     */
    public function getListAll(): Collection;
}
