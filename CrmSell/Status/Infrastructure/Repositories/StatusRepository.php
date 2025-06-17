<?php

namespace CrmSell\Status\Infrastructure\Repositories;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Status\Domains\Enum\StatusEnum;
use CrmSell\Status\Infrastructure\Repositories\Interfaces\StatusRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatusRepository implements StatusRepositoryInterface
{
    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     * @throws \Exception
     */
    public function getListStatus(GetListDTO $getListDTO): Collection
    {
        try {
            if ($getListDTO->getFilterValue('type') === StatusEnum::DEFECT->value) {
                $query = DB::table('defects as d')->select(['d.id', 'd.name', 'd.alias', 'd.created_at', 'd.updated_at']);
            } else {
                $query = DB::table('status as s')->select(['s.id', 's.name', 's.alias', 's.created_at', 's.updated_at']);
            }

            $result = $query->limit($getListDTO->getPagination()->getLimit())
                ->offset($getListDTO->getPagination()->getOffset())
                ->orderBy($getListDTO->getSortField(), $getListDTO->getSortDir())
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("StatusRepository::getListStatus() error.");
        }

        return $result;
    }

    /**
     * @param string $type
     * @return int
     * @throws \Exception
     */
    public function getCountForListStatus(string $type): int
    {
        try {
            if ($type === StatusEnum::DEFECT->value) {
                $query = DB::table('defects')->select(['id']);
            } else {
                $query = DB::table('status')->select(['id']);
            }

            $result = $query->count();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("StatusRepository::getCountForListStatus() error.");
        }

        return $result;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getListAll(): Collection
    {
        try {
            $result = DB::table('status as s')
                ->select(['s.alias', 's.name'])
                ->orderBy('s.name')
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("StatusRepository::getListAll() error.");
        }

        return $result;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getListAllDefect(): Collection
    {
        try {
            $result = DB::table('defects as d')
                ->select(['d.alias', 'd.name'])
                ->orderBy('d.name')
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("StatusRepository::getListAllDefect() error.");
        }

        return $result;
    }
}
