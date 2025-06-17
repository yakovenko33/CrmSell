<?php

namespace CrmSell\Status\Infrastructure\Repositories\Interfaces;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use Illuminate\Support\Collection;

interface StatusRepositoryInterface
{
    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     */
    public function getListStatus(GetListDTO $getListDTO): Collection;

    /**
     * @param string $type
     * @return int
     * @throws \Exception
     */
    public function getCountForListStatus(string $type): int;
}
