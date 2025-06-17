<?php

namespace CrmSell\Orders\Infrastructure\Repositories;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Orders\Application\Orders\OrdersCSV\Request\OrdersCSV;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\OrdersRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdersRepository implements OrdersRepositoryInterface
{
    const FILTER_ALL = 'all';

    /**
     * @param array $filter
     * @return int
     * @throws \Exception
     */
    public function getListCount(array $filter): int
    {
        $params = $this->getFilter($filter);
        $where = implode("AND", array_filter($params["condition"], fn($item) => $item !== ''));
        $where = $where === '' ? $where : " WHERE $where ";

        try {
            $sql = "SELECT COUNT(t.id) as count
                FROM ({$this->getQuerySQL()} $where) as t";

            $results = DB::select($sql, $params["bindings"]);
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("OrdersRepository::getListCount() error.");
        }

        return !empty($results) ? $results[0]->count : 0;
    }

    /**
     * @return string
     */
    public function getQuerySQL(): string
    {
        return "SELECT o.id,
                   o.amount_in_order as amount_in_order,
                   o.amount_in_order_paid as amount_in_order_paid,
                   CAST(o.sell_price AS DECIMAL(10, 2)) as sell_price,
                   CAST(o.cost AS DECIMAL(10, 2)) as cost,
                   IFNULL(o.date_check, '') as date_check,
                   o.created_at as order_date,
                   o.order_number as order_number,
                   g.vendor_code as vendor_code,
                   g.name as goods_name,
                   o.manager_comment as manager_comment,
                   o.comment as comment,
                   o.comfy_code as comfy_code,
                   o.comfy_goods_name as comfy_goods_name,
                   IFNULL(o.comfy_brand, '') as comfy_brand_id,
                   IFNULL(b.name, '') as comfy_brand,
                   o.comfy_category as comfy_category,
                   o.comfy_price as comfy_price,
                   o.created_at,
                   CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as manager,
                   s.name as status,
                   s.alias as status_alias,
                   d.name as defect,
                   o.defect as defect_alias,
                   p.id as provider_start_id,
                   p.name as provider_start,
                   p.type as provider_type,
                   IFNULL(shipments.shipments_amount, 0) as shipments_amount,
                   IF(shipments.shipments_amount > 0, o.amount_in_order_paid - shipments.shipments_amount, o.amount_in_order_paid) as remainder,
                   o.comfy_price - o.cost as comfy_price_cost
            FROM orders as o
                LEFT JOIN goods g
                   ON o.goods_id = g.id
                LEFT JOIN users u
                   ON o.manager = u.id
                LEFT JOIN status s
                   ON s.alias = o.status
                LEFT JOIN defects d
                   ON d.alias = o.defect
                LEFT JOIN brands b
                   ON b.id = o.comfy_brand
                LEFT JOIN providers p
                   ON p.id = o.provider_start
                LEFT JOIN (
                    SELECT s.order_id, SUM(s.amount) as shipments_amount
                    FROM shipments as s
                    GROUP BY order_id
                ) as shipments
                    ON shipments.order_id = o.id";
    }

    /**
     * @param GetListDTO $dto
     * @return array
     * @throws \Exception
     */
    public function getList(GetListDTO $dto): array
    {
        $params = $this->getFilter($dto->getFilter());
        $where = implode("AND", array_filter($params["condition"], fn($item) => $item !== ''));
        $where = $where === '' ? $where : " WHERE $where ";

        try {
            DB::enableQueryLog();
            $sql = "
                SELECT t.*
                FROM ({$this->getQuerySQL()} $where) as t
                ORDER BY {$dto->getSortField()} {$dto->getSortDir()}
                LIMIT {$dto->getPagination()->getLimit()} OFFSET {$dto->getPagination()->getOffset()}
            ";
            $results = DB::select($sql, $params["bindings"]);

            $queries = DB::getQueryLog();
            $lastQuery = end($queries);
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("OrdersRepository::getList() error.");
        }

        return !empty($results) ? $results : [];
    }

    /**
     * @param array $params
     * @return array|array[]
     */
    private function getFilter(array $params): array
    {
        $filter = [
            "condition" => [],
            "bindings" => [],
        ];

        if (!empty($params["order_date_from"])) {
            $filter["condition"][] = " DATE(o.created_at) >= :order_date_from ";
            $filter["bindings"]["order_date_from"] = $params["order_date_from"];
        }
        if (!empty($params["order_date_to"])) {
            $filter["condition"][] = " DATE(o.created_at) <= :order_date_to ";
            $filter["bindings"]["order_date_to"] = $params["order_date_to"];
        }
        if (!empty($params["date_check_from"])) {
            $filter["condition"][] = " DATE(o.date_check) >= :date_check_from ";
            $filter["bindings"]["date_check_from"] = $params["date_check_from"];
        }
        if (!empty($params["date_check_to"])) {
            $filter["condition"][] = " DATE(o.created_at) <= :date_check_to ";
            $filter["bindings"]["date_check_to"] = $params["date_check_to"];
        }
        if (!empty($params["goods_id"])) {
            $filter["condition"][] = " o.goods_id = :goods_id ";
            $filter["bindings"]["goods_id"] = $params["goods_id"];
        }
        if (!empty($params["status"]) && !in_array(self::FILTER_ALL, $params["status"])) {
            list($bindings, $placeholders) = $this->getFilterStatus($params["status"]);
            $filter["condition"][] = " o.status IN ( " . implode(',', $placeholders) . ") ";
            $filter["bindings"] = array_merge($filter["bindings"], $bindings);
        }
        if (!empty($params["remainder"])) {
            $filter["condition"]["remainder"] = " IF(shipments.shipments_amount > 0, o.amount_in_order_paid - shipments.shipments_amount, o.amount_in_order_paid) > 0 ";
        }
        if (!empty($params["provider_start"]) && $params["provider_start"] !== self::FILTER_ALL) {
            $filter["condition"][] = " o.provider_start = :provider_start ";
            $filter["bindings"]["provider_start"] = $params["provider_start"];
        }
        if (!empty($params["defect"]) && $params["defect"] !== self::FILTER_ALL) {
            $filter["condition"][] = " o.defect = :defect ";
            $filter["bindings"]["defect"] = $params["defect"];
        }
        if (!empty($params["comment"])) {
            $filter["condition"][] = " o.comment LIKE :comment ";
            $filter["bindings"]["comment"] = "%{$params["comment"]}%";
        }
        if (!empty($params["order_number"])) {
            $filter["condition"][] = " o.order_number = :order_number ";
            $filter["bindings"]["order_number"] = $params["order_number"];
        }

        return $filter;
    }

    /**
     * @param array $statuses
     * @return array
     */
    private function getFilterStatus(array $statuses): array
    {
        $bindings = [];
        $placeholders = [];
        foreach ($statuses as $index => $status) {
            $placeholder = ":status{$index}";
            $placeholders[] = $placeholder;
            $bindings[$placeholder] = $status;
        }

        return [$bindings, $placeholders];
    }

    /**
     * @param OrdersCSV $dto
     * @return \Generator
     * @throws \Exception
     */
    public function getListOrdersCSV(OrdersCSV $dto): \Generator
    {
        $params = $this->getFilter($dto->getFilter());
        $where = implode("AND", array_filter($params["condition"], fn($item) => $item !== ''));
        $where = $where === '' ? $where : " WHERE $where ";

        try {
            $sql = "SELECT t.*  FROM ({$this->getQuerySQL()} $where) as t";

            foreach (DB::cursor($sql, $params["bindings"]) as $row) {
                yield (array) $row;
            }
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("OrdersRepository::getListOrdersCSV error.");
        }
    }
}
