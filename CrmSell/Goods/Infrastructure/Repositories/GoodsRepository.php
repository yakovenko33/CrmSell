<?php

namespace CrmSell\Goods\Infrastructure\Repositories;

use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Goods\Domains\Entities\Goods;
use CrmSell\Goods\Infrastructure\Repositories\Interfaces\GoodsRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoodsRepository implements GoodsRepositoryInterface
{
    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     * @throws \Exception
     */
    public function getList(GetListDTO $getListDTO): Collection
    {
        try {
            $result = DB::table('goods as g')
                ->select(['g.id', 'g.name', 'g.vendor_code', 'g.deprecated', 'g.created_at', 'g.updated_at'])
                ->limit($getListDTO->getPagination()->getLimit())
                ->offset($getListDTO->getPagination()->getOffset())
                ->orderBy($getListDTO->getSortField(), $getListDTO->getSortDir())
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("GoodsRepository::getListProviders() error.");
        }

        return $result;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getCount(): int
    {
        try {
            $result = DB::table('goods')->select(['id'])->count();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("GoodsRepository::getCountForListProviders() error.");
        }

        return $result;
    }


    /**
     * @param string $id
     * @param string $fieldName
     * @param string $fieldValue
     * @return bool
     */
    public function checkExistSimilar(string $id, string $fieldName, string $fieldValue): bool
    {
        $goods = Goods::where([
            ['id', '<>', $id],
            [$fieldName, '=', $fieldValue]
        ])->first();

        return !empty($goods->id);
    }

    /**
     * @param array $params
     * @param string $sortField
     * @return array
     * @throws \Exception
     */
    public function getListByParam(array $params, string $sortField): array
    {
        $params = $this->getFilter($params);
        $where = implode("AND", array_filter($params["condition"], fn($item) => $item !== ''));
        $where = $where === '' ? "$where" : " $where AND g.deprecated = 0 ";

        try {
            $sql = "
                SELECT g.id,
                       g.vendor_code,
                       g.name
                FROM goods as g
                WHERE $where
                ORDER BY {$sortField} ASC
                LIMIT 30
            ";

            $results = DB::select($sql, $params["bindings"]);
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("GoodsRepository::getListByParam() error.");
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

        if (!empty($params["name"])) {
            $filter["condition"][] = " g.name LIKE :name";
            $filter["bindings"][":name"] = "%{$params["name"]}%";
        }
        if (!empty($params["vendor_code"])) {
            $filter["condition"][] = " g.vendor_code LIKE :vendor_code";
            $filter["bindings"][":vendor_code"] = "%{$params["vendor_code"]}%";
        }

        return $filter;
    }
}
