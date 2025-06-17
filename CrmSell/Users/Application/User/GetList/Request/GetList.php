<?php

namespace CrmSell\Users\Application\User\GetList\Request;


use CrmSell\Common\Application\Service\Request\RootRequest;

class GetList extends RootRequest
{
    private int $pageNumber = 1;
    private string $sortField = 'created_at';
    private string $sortDir = 'desc';

    public function __construct(array $request)
    {
        $this->pageNumber = !empty($request['pageNumber']) ? (int)$request['pageNumber'] : 1;
        $this->sortField = !empty($request['sortField']) ? $request['sortField'] : 'created_at';
        $this->sortDir = !empty($request['sortDir']) ? $request['sortDir'] : 'desc';
    }

    public function getPage(): int {
        return $this->pageNumber;
    }

    public function getSortField(): string {
        return $this->sortField;
    }

    public function getSortDir(): string {
        return $this->sortDir;
    }
}
