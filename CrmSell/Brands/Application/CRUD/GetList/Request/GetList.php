<?php

namespace CrmSell\Brands\Application\CRUD\GetList\Request;

class GetList extends \CrmSell\Common\Application\Service\Request\GetList
{
    /**
     * @param array $request
     */
    public function __construct(array $request)
    {
        parent::__construct($request);
    }
}
