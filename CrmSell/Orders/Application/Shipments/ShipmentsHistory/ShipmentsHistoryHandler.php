<?php

namespace CrmSell\Orders\Application\Shipments\ShipmentsHistory;

use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Orders\Application\Orders\GetList\Request\GetList;
use CrmSell\Orders\Application\Shipments\ShipmentsHistory\Request\ShipmentsHistory;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\ShipmentsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ShipmentsHistoryHandler extends AbstractHandler
{
    private ShipmentsRepositoryInterface $repository;
    private PaginationInterface $pagination;

    /**
     * @param ShipmentsRepositoryInterface $repository
     * @param PaginationInterface $pagination
     */
    public function __construct(
        ShipmentsRepositoryInterface $repository,
        PaginationInterface $pagination
    ) {
        parent::__construct();

        $this->repository = $repository;
        $this->pagination = $pagination;
    }

    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    protected function process(RequestInterface $request): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->getRecords($request);

            DB::commit();
        }  catch (\Exception $e) {
            DB::rollBack();

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param ShipmentsHistory $query
     * @return void
     */
    private function getRecords(ShipmentsHistory $query): void
    {
        $this->pagination
            ->setAmount($this->repository->getListHistoryCount($query->getParentId()))
            ->setPageNumber($query->getPage())
            ->buildPagination();

        $dto = GetListDTO::create($query->getSortField())
            ->setPagination($this->pagination)
            ->setSortDir($query->getSortDir())
            ->setFilter(["parent_id" => $query->getParentId()]);

        $this->resultHandler
            ->setStatusCode()
            ->setResult([
                "records" => $this->repository->getListHistoryList($dto),
                "pagination" => $this->pagination->getPagination()
            ]);
    }
}
