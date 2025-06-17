<?php

namespace CrmSell\Providers\Infrastructure\Repositories\Interfaces;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use Illuminate\Support\Collection;

interface ProvidersRepositoryInterface
{
    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     * @throws \Exception
     */
    public function getListProviders(GetListDTO $getListDTO): Collection;

    /**
     * @return int
     * @throws \Exception
     */
    public function getCountForListProviders(): int;

    /**
     * @return Collection
     */
    public function getListAll(): Collection;
}
