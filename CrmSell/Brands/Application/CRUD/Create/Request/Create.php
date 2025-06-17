<?php

namespace CrmSell\Brands\Application\CRUD\Create\Request;


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

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            "name" => "required|string|max:50|min:2|unique:brands,name",
        ];
    }

    public function getName(): string { return $this->name; }
    public function toValidation(): array { return $this->toArray();}

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
