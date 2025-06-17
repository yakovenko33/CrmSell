<?php

namespace CrmSell\Orders\Infrastructure\Repositories;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\ShipmentsRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentsRepository implements ShipmentsRepositoryInterface
{

    /**
     * @param string $id
     * @return int
     * @throws \Exception
     */
    public function getTotalShipmentForByOrder(string $id): int
    {
        try {
            $results = DB::select("SELECT IFNULL(SUM(s.amount), 0) as count FROM shipments as s WHERE s.order_id = :id", ['id' => $id]);
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("ShipmentsRepository::getTotalShipmentForByOrder() error.");
        }

        return !empty($results) ? $results[0]->count : 0;
    }

    /**
     * @param string $id
     * @return int
     * @throws \Exception
     */
    public function getListHistoryCount(string $id): int
    {
        try {
            $results = DB::select("SELECT COUNT(s.id) as count FROM shipments as s WHERE s.order_id = :id", ['id' => $id]);
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("ShipmentsRepository::getTotalShipmentForByOrder() error.");
        }

        return !empty($results) ? $results[0]->count : 0;
    }

    /**
     * @param GetListDTO $dto
     * @return array
     * @throws \Exception
     */
    public function getListHistoryList(GetListDTO $dto): array
    {
        try {
            $sql = "
            SELECT s.id,
                   CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as created_by,
                   s.created_at,
                   s.amount,
                   s.shipment_date
            FROM shipments as s
                LEFT JOIN users u
                   ON s.created_by = u.id
             WHERE s.order_id = :id
             ORDER BY {$dto->getSortField()} {$dto->getSortDir()}
             LIMIT {$dto->getPagination()->getLimit()} OFFSET {$dto->getPagination()->getOffset()}
            ";

            $results = DB::select($sql, ['id' => $dto->getFilterValue("parent_id")]);
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("ShipmentsRepository::getListHistoryList() error.");
        }

        return !empty($results) ? $results : [];
    }
}
