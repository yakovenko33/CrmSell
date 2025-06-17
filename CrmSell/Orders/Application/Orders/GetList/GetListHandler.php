<?php

namespace CrmSell\Orders\Application\Orders\GetList;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Orders\Application\Orders\GetList\Request\GetList;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\OrdersRepositoryInterface;
use Illuminate\Support\Facades\Log;

class GetListHandler extends AbstractHandler
{
    private PaginationInterface $pagination;
    private OrdersRepositoryInterface $repository;

    public function __construct(
        PaginationInterface $pagination,
        OrdersRepositoryInterface $repository
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
            $this->getRecords($command);
        } catch (\Exception $e) {

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param GetList $query
     * @return void
     */
    private function getRecords(GetList $query): void
    {
        $this->pagination
            ->setAmount($this->repository->getListCount($query->getFilter()))
            ->setPageNumber($query->getPage())
            ->buildPagination();

        $dto = GetListDTO::create($query->getSortField())
            ->setPagination($this->pagination)
            ->setSortDir($query->getSortDir())
            ->setFilter($query->getFilter());

        $this->resultHandler
            ->setStatusCode()
            ->setResult([
                "records" => $this->repository->getList($dto),
                "pagination" => $this->pagination->getPagination()
            ]);
    }
}
