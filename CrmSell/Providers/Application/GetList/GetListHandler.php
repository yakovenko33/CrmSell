<?php

namespace CrmSell\Providers\Application\GetList;

use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Providers\Application\GetList\Request\GetList;
use CrmSell\Providers\Infrastructure\Repositories\Interfaces\ProvidersRepositoryInterface;
use Illuminate\Support\Facades\Log;

class GetListHandler extends AbstractHandler
{
    private PaginationInterface $pagination;
    private ProvidersRepositoryInterface $repository;

    public function __construct(
        PaginationInterface $pagination,
        ProvidersRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->pagination = $pagination;
        $this->repository = $repository;
    }

    /**
     * @param RequestInterface $command
     * @return ResultHandler
     */
    protected function process(RequestInterface $command): ResultHandler
    {
        try {
            $this->getProviders($command);
        } catch (\Exception $e) {

            Log::error($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param GetList $query
     * @return void
     * @throws \Exception
     */
    private function getProviders(GetList $query): void
    {
        $this->pagination
            ->setAmount($this->repository->getCountForListProviders())
            ->setPageNumber($query->getPage())
            ->buildPagination();

        $dto = GetListDTO::create($query->getSortField())
            ->setPagination($this->pagination)
            ->setSortDir($query->getSortDir());

        $this->resultHandler
            ->setStatusCode()
            ->setResult([
                "records" => $this->repository->getListProviders($dto)->toArray(),
                "pagination" => $this->pagination->getPagination()
            ]);
    }

    /**
     * @return ResultHandler
     */
    public function getListAll(): ResultHandler
    {
        try {
            $result = $this->repository->getListAll()->map(function ($item) {
                return [
                    "key" => $item->id,
                    "value" => $item->name,
                    "type" => $item->type,
                ];
            });

            $this->resultHandler
                ->setStatusCode()
                ->setResult($result->toArray());
        } catch (\Exception $e) {
            Log::error($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }
}
