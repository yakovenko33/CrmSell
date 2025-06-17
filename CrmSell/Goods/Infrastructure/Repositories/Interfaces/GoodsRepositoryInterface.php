<?php

namespace CrmSell\Goods\Infrastructure\Repositories\Interfaces;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use Illuminate\Support\Collection;

interface GoodsRepositoryInterface
{
    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     */
    public function getList(GetListDTO $getListDTO): Collection;

    /**
     * @return int
     * @throws \Exception
     */
    public function getCount(): int;

    /**
     * @param string $id
     * @param string $fieldName
     * @param string $fieldValue
     * @return bool
     */
    public function checkExistSimilar(string $id, string $fieldName, string $fieldValue): bool;

    /**
     * @param array $params
     * @param string $sortField
     * @return array
     */
    public function getListByParam(array $params, string $sortField): array;
}
