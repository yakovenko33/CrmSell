<?php

namespace CrmSell\Common\Application\Service\Handler;


use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use Illuminate\Support\Facades\Validator as Validation;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractHandler
{
    protected ResultHandler $resultHandler;

    public function __construct()
    {
        $this->resultHandler = new ResultHandler();
    }

    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    public function handle(RequestInterface $request): ResultHandler
    {
        if (!empty($request->getRules())) {
            return $this->isValid($request)
                ? $this->process($request)
                : $this->resultHandler;
        }

        return $this->process($request);
    }

    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    abstract protected function process(RequestInterface $request): ResultHandler;

    /**
     * @param RequestInterface $request
     * @return bool
     */
    protected function isValid(RequestInterface $request): bool
    {
        $validationError = Validation::make($request->toValidation(), $request->getRules(), $request->messages());

        if (!$validationError->fails()) {
            return true;
        }

        $this->resultHandler
            ->setErrors($this->getErrorsValidation($validationError))
            ->setStatus(ResponseCodeErrors::VALIDATE_ERROR)
            ->setStatusCode(422);

        return false;
    }

    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return array
     */
    private function getErrorsValidation(\Illuminate\Contracts\Validation\Validator $validator): array
    {
        $errors = [];

        foreach ($validator->errors()->toArray() as $field => $error) {
            $errors[] = [
                "field" => $field,
                "message" => $error[0]
            ];
        }

        return $errors;
    }

    /**
     * @param \Exception $e
     * @return ResultHandler
     */
    protected function notSuccessfulResponse(\Exception $e): ResultHandler
    {
        $isHttpCode = array_key_exists($e->getCode(), Response::$statusTexts);

        return $this->resultHandler
            ->setStatus(ResponseCodeErrors::SERVER_ERROR)
            ->setStatusCode($isHttpCode ? $e->getCode() : 500)
            ->setErrors([$isHttpCode ? $e->getMessage() : "Problem on server. Try next time."]);
    }
}
