<?php

namespace CrmSell\Providers\Application\Update\Request;

use CrmSell\Providers\Application\Create\Request\Create;

class Edit extends Create
{
    protected string $id = '';

    public function __construct(array $request)
    {
        parent::__construct($request);
    }

    public function getId(): string {
        return $this->id;
    }
}
