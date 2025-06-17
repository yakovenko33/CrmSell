<?php

namespace CrmSell\Goods\Application\CRUD\Update\Request;

use CrmSell\Goods\Application\CRUD\Create\Request\Create;

class Update extends Create
{
    protected string $id = '';

    public function __construct(array $request)
    {
        parent::__construct($request);
    }

    public function getId(): string { return $this->id; }
}
