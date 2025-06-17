<?php

namespace CrmSell\Users\Application\User\AddRole\Request;

use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;

class AddRole extends RootRequest
{
    use PropertyTrait;

    private string $userId = '';
    private string $roleId = '';

    public function __construct(array $request)
    {
        $this->mapField($request);
    }

    public function getRoleId(): string {
        return $this->roleId;
    }

    public function getUserId(): string {
        return $this->userId;
    }
}
