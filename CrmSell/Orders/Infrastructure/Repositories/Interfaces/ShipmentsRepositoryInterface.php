<?php

namespace CrmSell\Orders\Infrastructure\Repositories\Interfaces;

use CrmSell\Common\Application\Service\DTO\GetListDTO;

interface ShipmentsRepositoryInterface
{
    /**
     * @param string $id
     * @return int
     */
    public function getTotalShipmentForByOrder(string $id): int;

    /**
     * @param string $id
     * @return int
     */
    public function getListHistoryCount(string $id): int;

    /**
     * @param GetListDTO $dto
     * @return array
     */
    public function getListHistoryList(GetListDTO $dto): array;
}
