<?php

namespace CrmSell\Common\UI\Traits;

use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * @param ResultHandler $resultHandler
     * @return JsonResponse
     */
    public function getResponse(ResultHandler $resultHandler): JsonResponse
    {
        $hasErrors = $resultHandler->hasErrors();

        return response()->json([
            'status' => $resultHandler->getStatus(),
            "data" => $hasErrors ? [] : $resultHandler->getResult(),
            "errors" => $hasErrors ? $resultHandler->getErrors() : [],
        ], $resultHandler->getStatusCode());
    }

    /**
     * @param array $error
     * @param int $code
     * @param string $status
     * @return JsonResponse
     */
    public function getErrorsResponse(array $error, int $code = 500, string $status = ResponseCodeErrors::SERVER_ERROR): JsonResponse
    {
        return response()->json([
            'status' => $status,
            "data" => [],
            "errors" => $error,
        ], $code);
    }

    /**
     * @param array $data
     * @param int $code
     * @param string $status
     * @return JsonResponse
     */
    public function getSuccessfulResponse(array $data, int $code = 200, string $status = ResponseCodeErrors::STATUS_OK): JsonResponse
    {
        return response()->json([
            'status' => $status,
            "data" => $data,
            "errors" => [],
        ], $code);
    }
}
