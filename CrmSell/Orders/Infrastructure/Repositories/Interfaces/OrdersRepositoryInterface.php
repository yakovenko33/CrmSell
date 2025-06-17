<?php

namespace CrmSell\Orders\Infrastructure\Repositories\Interfaces;


use CrmSell\Common\Application\Service\DTO\GetListDTO;

interface OrdersRepositoryInterface
{
    /**
     * @param array $filter
     * @return int
     */
    public function getListCount(array $filter): int;

    /**
     * @param GetListDTO $dto
     * @return array
     */
    public function getList(GetListDTO $dto): array;
}
