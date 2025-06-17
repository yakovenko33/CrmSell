<?php

namespace CrmSell\Goods\UI\Http\Controllers;


use CrmSell\Common\UI\Traits\ResponseTrait;
use CrmSell\Goods\Application\CRUD\Create\CreateHandler;
use CrmSell\Goods\Application\CRUD\Create\Request\Create;
use CrmSell\Goods\Application\CRUD\GetList\GetListHandler;
use CrmSell\Goods\Application\CRUD\GetList\Request\GetList;
use CrmSell\Goods\Application\CRUD\Update\Request\Update;
use CrmSell\Goods\Application\CRUD\Update\UpdateHandler;
use CrmSell\Goods\Application\GetListByParam\GetListByParam;
use CrmSell\Goods\Domains\Entities\Goods;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoodsController
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @param CreateHandler $handler
     * @return JsonResponse
     */
    public function create(Request $request, CreateHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || !$user->hasRole('admin') || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $data = json_decode($request->getContent(), true);
        $result = $handler->handle(new Create($data));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param UpdateHandler $handler
     * @return JsonResponse
     */
    public function update(Request $request, UpdateHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || !$user->hasRole('admin') || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $data = json_decode($request->getContent(), true);
        $result = $handler->handle(new Update($data));

        return $this->getResponse($result);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function getGoodsById(string $id): JsonResponse
    {
        $authUser = Auth::user();
        if (empty($authUser) || $authUser->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }

        $provider = Goods::find($id);

        return $this->getSuccessfulResponse($provider->getDetail());
    }

    /**
     * @param Request $request
     * @param GetListHandler $handler
     * @return JsonResponse
     */
    public function getList(Request $request, GetListHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || !$user->hasRole('admin') || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $result = $handler->handle(new GetList($request->toArray()));

        return $this->getResponse($result);
    }

    /**
     * @param string $value
     * @param GetListByParam $handler
     * @return JsonResponse
     */
    public function getListByVendorCode(string $value, GetListByParam $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $result = $handler->getListByVendorCode($value);

        return $this->getResponse($result);
    }

    /**
     * @param string $value
     * @param GetListByParam $handler
     * @return JsonResponse
     */
    public function getListByGoodsName(string $value, GetListByParam $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $result = $handler->getListByGoodsName($value);

        return $this->getResponse($result);
    }
}
