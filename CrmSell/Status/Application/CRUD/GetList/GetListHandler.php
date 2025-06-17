<?php

namespace CrmSell\Status\Application\CRUD\GetList;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Status\Application\CRUD\GetList\Request\GetList;
use CrmSell\Status\Infrastructure\Repositories\Interfaces\StatusRepositoryInterface;
use Illuminate\Support\Facades\Log;

class GetListHandler extends AbstractHandler
{
    private PaginationInterface $pagination;
    private StatusRepositoryInterface $repository;

    public function __construct(
        PaginationInterface $pagination,
        StatusRepositoryInterface $repository
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
            $this->getStatus($command);
        } catch (\Exception $e) {

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param GetList $query
     * @return void
     * @throws \Exception
     */
    private function getStatus(GetList $query): void
    {
        $this->pagination
            ->setAmount($this->repository->getCountForListStatus($query->getType()))
            ->setPageNumber($query->getPage())
            ->buildPagination();

        $dto = GetListDTO::create($query->getSortField())
            ->setPagination($this->pagination)
            ->setSortDir($query->getSortDir())
            ->setFilter(["type" => $query->getType()]);

        $this->resultHandler
            ->setStatusCode()
            ->setResult([
                "records" => $this->repository->getListStatus($dto)->toArray(),
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
                    "key" => $item->alias,
                    "value" => $item->name,
                ];
            });

            $this->resultHandler
                ->setStatusCode()
                ->setResult($result->toArray());
        } catch (\Exception $e) {
            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @return ResultHandler
     */
    public function getListAllDefect(): ResultHandler
    {
        try {
            $result = $this->repository->getListAllDefect()->map(function ($item) {
                return [
                    "key" => $item->alias,
                    "value" => $item->name,
                ];
            });

            $this->resultHandler
                ->setStatusCode()
                ->setResult($result->toArray());
        } catch (\Exception $e) {
            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }
}
