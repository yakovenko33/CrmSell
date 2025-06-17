<?php

namespace CrmSell\Goods\Application\GetListByParam;


use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Goods\Infrastructure\Repositories\Interfaces\GoodsRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class GetListByParam
{
    private GoodsRepositoryInterface $repository;
    protected ResultHandler $resultHandler;

    /**
     * @param GoodsRepositoryInterface $repository
     */
    public function __construct(GoodsRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->resultHandler = new ResultHandler();
    }

    /**
     * @param \Exception $e
     * @return ResultHandler
     */
    protected function notSuccessfulResponse(\Exception $e): ResultHandler
    {
        $isHttpCode = array_key_exists($e->getCode(), Response::$statusTexts);

        return $this->resultHandler
            ->setStatusCode($isHttpCode ? $e->getCode() : 500)
            ->setErrors([$isHttpCode ? $e->getMessage() : "Problem on server. Try next time."]);
    }

    /**
     * @param array $params
     * @param string $value
     * @return ResultHandler
     */
    protected function getList(array $params, string $value): ResultHandler
    {
        try {
            $this->resultHandler
                ->setStatusCode()
                ->setResult([
                    "records" => $this->repository->getListByParam($params, $value)
                ]);
        } catch (\Exception $e) {

            Log::error($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param string $value
     * @return ResultHandler
     */
    public function getListByVendorCode(string $value): ResultHandler {
        return $this->getList(['vendor_code' => $value],'vendor_code');
    }

    /**
     * @param string $value
     * @return ResultHandler
     */
    public function getListByGoodsName(string $value): ResultHandler {
        return $this->getList(['name' => $value],'name');
    }
}
