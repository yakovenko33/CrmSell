<?php

namespace CrmSell\Brands\Infrastructure\Repositories\Interfaces;

use CrmSell\Common\Application\Service\DTO\GetListDTO;
use Illuminate\Support\Collection;

interface BrandsRepositoryInterface
{
    /**
     * @param string $name
     * @return array
     */
    public function getListByName(string $name): array;

    /**
     * @return int
     */
    public function getCountForListPage(): int;

    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     */
    public function getListPage(GetListDTO $getListDTO): Collection;
}
