<?php

namespace CrmSell\Brands\Infrastructure\Repositories;


use CrmSell\Brands\Infrastructure\Repositories\Interfaces\BrandsRepositoryInterface;
use CrmSell\Common\Application\Service\DTO\GetListDTO;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BrandsRepository implements BrandsRepositoryInterface
{
    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    public function getListByName(string $name): array
    {
        try {
            $sql = "
                SELECT b.id,
                       b.name
                FROM brands as b
                WHERE b.name LIKE :name
                  AND b.deprecated = 0
                ORDER BY b.name ASC
                LIMIT 30
            ";

            $results = DB::select($sql, ["%{$name}%"]);
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("BrandsRepository::getListByParam() error.");
        }

        return !empty($results) ? $results : [];
    }

    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     * @throws \Exception
     */
    public function getListPage(GetListDTO $getListDTO): Collection
    {
        try {
            $result = DB::table('brands as b')
                ->select(['b.id', 'b.name', 'b.created_at', 'b.updated_at'])
                ->limit($getListDTO->getPagination()->getLimit())
                ->offset($getListDTO->getPagination()->getOffset())
                ->orderBy($getListDTO->getSortField(), $getListDTO->getSortDir())
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("BrandsRepository::getListPage() error.");
        }

        return $result;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getCountForListPage(): int
    {
        try {
            $result = DB::table('providers')->select(['id'])->count();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("BrandsRepository::getCountForListPage() error.");
        }

        return $result;
    }
}
