<?php

namespace CrmSell\Status\Application\CRUD\GetList\Request;


class GetList extends \CrmSell\Common\Application\Service\Request\GetList
{
    private string $type = '';

    public function __construct(array $request)
    {
        parent::__construct($request);

        $this->type = !empty($request['type']) ? $request['type'] : '';
    }

    public function getType(): string {
        return $this->type;
    }
}
