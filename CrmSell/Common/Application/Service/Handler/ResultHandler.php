<?php

namespace CrmSell\Common\Application\Service\Handler;

use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;

class ResultHandler
{
    private array $result = [];
    private int $statusCode = 200;

    private array $errors = [];
    private bool $hasError = false;

    private string $status = ResponseCodeErrors::STATUS_OK;

    public function getErrors(): array { return $this->errors; }

    public function getResult(): array { return $this->result; }

    public function getStatusCode(): int { return $this->statusCode; }

    public function hasErrors(): bool { return $this->hasError; }

    public function getStatus(): string { return $this->status; }

    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        $this->hasError = count($this->errors) > 0;

        return $this;
    }

    public function setResult(array $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function setStatusCode(int $statusCode = 200): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function setStatus(string $status = ResponseCodeErrors::STATUS_OK): self
    {
        $this->status = $status;

        return $this;
    }
}
