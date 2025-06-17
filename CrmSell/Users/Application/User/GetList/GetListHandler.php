<?php

namespace CrmSell\Users\Application\User\GetList;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Users\Application\User\GetList\Request\GetList;
use CrmSell\Users\Infrastructure\Repositories\Interfaces\UsersRepositoryInterface;
use Illuminate\Support\Facades\Log;

class GetListHandler  extends AbstractHandler
{
    private PaginationInterface $pagination;
    private UsersRepositoryInterface $usersRepository;

    public function __construct(
        PaginationInterface $pagination,
        UsersRepositoryInterface $usersRepository
    ) {
        parent::__construct();

        $this->pagination = $pagination;
        $this->usersRepository = $usersRepository;
    }

    /**
     * @param RequestInterface $command
     * @return ResultHandler
     */
    protected function process(RequestInterface $command): ResultHandler
    {
        try {
            $this->getUsers($command);
        } catch (\Exception $e) {

            Log::error($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param GetList $query
     * @return void
     */
    private function getUsers(GetList $query): void
    {
        $this->pagination
            ->setAmount($this->usersRepository->getCountForListUsers())
            ->setPageNumber($query->getPage())
            ->buildPagination();

        $dto = GetListDTO::create($query->getSortField())
            ->setPagination($this->pagination)
            ->setSortDir($query->getSortDir());

        $this->resultHandler
            ->setStatusCode()
            ->setResult([
                "records" => $this->usersRepository->getListUsers($dto)->toArray(),
                "pagination" => $this->pagination->getPagination()
            ]);
    }

    /**
     * @return ResultHandler
     */
    public function getListAll(): ResultHandler
    {
        try {
            $result = $this->usersRepository->getListAll()->map(function ($item) {
                return [
                    "key" => $item->id,
                    "value" => "{$item->last_name} {$item->first_name}",
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
