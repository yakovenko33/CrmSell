<?php

namespace CrmSell\Orders\Application\Shipments\ShipmentsHistory\Request;


use CrmSell\Common\Application\Service\DTO\GetListDTO;
use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Orders\Application\Orders\GetList\Request\GetList;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\ShipmentsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentsHistory extends \CrmSell\Common\Application\Service\Request\GetList
{
    private string $parentId = '';

    public function __construct(array $request)
    {
        parent::__construct($request);

        $this->parentId = !empty($request['parentId']) ? $request['parentId'] : '';
    }

    public function getParentId(): string { return $this->parentId; }
}
