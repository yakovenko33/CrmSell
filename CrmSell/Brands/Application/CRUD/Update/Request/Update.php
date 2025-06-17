<?php

namespace CrmSell\Brands\Application\CRUD\Update\Request;


use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;

class Update extends RootRequest
{
    use PropertyTrait;
    private string $id = '';
    private string $name = '';
    protected bool $deprecated = false;

    /**
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->mapField($request);
    }

    public function getName(): string { return $this->name; }
    public function getId(): string { return $this->id; }

    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "deprecated" => $this->deprecated,
        ];
    }
}
