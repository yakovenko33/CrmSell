<?php

namespace CrmSell\Brands\Application\CRUD\GetList;


use CrmSell\Brands\Application\CRUD\GetList\Request\GetList;
use CrmSell\Brands\Infrastructure\Repositories\Interfaces\BrandsRepositoryInterface;
use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use Illuminate\Support\Facades\Log;

class GetListHandler extends AbstractHandler
{
    private BrandsRepositoryInterface $repository;
    private PaginationInterface $pagination;

    public function __construct(
        BrandsRepositoryInterface $repository,
        PaginationInterface $pagination
    ) {
        $this->repository = $repository;
        $this->pagination = $pagination;

        parent::__construct();
    }

    /**
     * @param RequestInterface $command
     * @return ResultHandler
     */
    protected function process(RequestInterface $command): ResultHandler
    {
        try {
            $this->getBrands($command);
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
    private function getBrands(GetList $query): void
    {
        $this->pagination
            ->setAmount($this->repository->getCountForListPage())
            ->setPageNumber($query->getPage())
            ->buildPagination();

        $dto = GetListDTO::create($query->getSortField())
            ->setPagination($this->pagination)
            ->setSortDir($query->getSortDir());

        $this->resultHandler
            ->setStatusCode()
            ->setResult([
                "records" => $this->repository->getListPage($dto)->toArray(),
                "pagination" => $this->pagination->getPagination()
            ]);
    }

    /**
     * @param string $name
     * @return ResultHandler
     */
    public function getListByName(string $name): ResultHandler
    {
        try {
            $result = array_map(function ($item) {
                return [
                    "key" => $item->id,
                    "value" => $item->name,
                ];
            }, $this->repository->getListByName($name));

            $this->resultHandler
                ->setStatusCode()
                ->setResult(["records" => $result]);
        } catch (\Exception $e) {
            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }
}
