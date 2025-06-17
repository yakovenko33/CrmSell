<?php

namespace CrmSell\Providers\Infrastructure\Repositories;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Providers\Infrastructure\Repositories\Interfaces\ProvidersRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProvidersRepository implements ProvidersRepositoryInterface
{
    /**
     * @param GetListDTO $getListDTO
     * @return Collection
     * @throws \Exception
     */
    public function getListProviders(GetListDTO $getListDTO): Collection
    {
        try {
            $result = DB::table('providers as p')
                ->select(['p.id', 'p.name', 'p.created_at', 'p.updated_at'])
                ->limit($getListDTO->getPagination()->getLimit())
                ->offset($getListDTO->getPagination()->getOffset())
                ->orderBy($getListDTO->getSortField(), $getListDTO->getSortDir())
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("ProvidersRepository::getListProviders() error.");
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
            $result = DB::table('providers as p')
                ->select(['p.id', 'p.name', 'p.type'])
                ->orderBy('p.name')
                ->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("ProvidersRepository::getListProviders() error.");
        }

        return $result;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getCountForListProviders(): int
    {
        try {
            $result = DB::table('providers')->select(['id'])->count();
        } catch (QueryException $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("ProvidersRepository::getCountForListProviders() error.");
        }

        return $result;
    }
}
