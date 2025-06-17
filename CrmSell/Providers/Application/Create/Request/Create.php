<?php

namespace CrmSell\Providers\Application\Create\Request;


use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;

class Create extends RootRequest
{
    use PropertyTrait;

    private string $name = '';

    /**
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->mapField($request);
    }

    public function getName(): string {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getRules(): array {
        return [
            "name" => 'required|string|max:30|min:2|unique:providers,name',
        ];
    }

    public function toValidation(): array {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "name" => $this->name,
        ];
    }
}
