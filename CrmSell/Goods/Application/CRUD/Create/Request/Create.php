<?php

namespace CrmSell\Goods\Application\CRUD\Create\Request;


use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;

class Create extends RootRequest
{
    use PropertyTrait;

    protected string $vendorCode = '';
    protected string $name = '';
    protected bool $deprecated = false;

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
            "name" => "required|string|max:50|min:2|unique:goods,name",
            "vendorCode" => "required|string|max:50|min:2|unique:goods,vendor_code",
        ];
    }

    /**
     * @return array
     */
    public function toValidation(): array
    {
        return [
            "vendorCode" => $this->vendorCode,
            "name" => $this->name,
        ];
    }

    public function getVendorCode(): string { return $this->vendorCode; }
    public function getDeprecated(): bool { return $this->deprecated; }
    public function getName(): string { return $this->name; }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "vendor_code" => $this->vendorCode,
            "name" => $this->name,
            "deprecated" => $this->deprecated,
        ];
    }
}
