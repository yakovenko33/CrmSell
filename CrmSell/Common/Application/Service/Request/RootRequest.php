<?php

namespace CrmSell\Common\Application\Service\Request;

class RootRequest implements RequestInterface
{
    /**
     * @return array
     */
    public function toArray(): array {
        return [];
    }

    /**
     * @return array
     */
    public function toValidation(): array {
        return [];
    }

    /**
     * @return array
     */
    public function getRules(): array {
        return [];
    }

    /**
     * @return array
     */
    public function messages(): array {
        return [];
    }
}
