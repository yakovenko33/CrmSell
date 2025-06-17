<?php

namespace CrmSell\Common\Application\Service\Request;

interface RequestInterface
{
    public function toArray(): array;

    public function toValidation(): array;

    public function getRules(): array;

    public function messages(): array;
}
