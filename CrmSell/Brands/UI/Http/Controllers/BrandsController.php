<?php

namespace CrmSell\Brands\UI\Http\Controllers;


use CrmSell\Brands\Application\CRUD\GetList\GetListHandler;
use CrmSell\Brands\Application\CRUD\GetList\Request\GetList;
use CrmSell\Brands\Domains\Entities\Brand;
use CrmSell\Common\UI\Traits\ResponseTrait;
use CrmSell\Brands\Application\CRUD\Create\CreateHandler;
use CrmSell\Brands\Application\CRUD\Create\Request\Create;
use CrmSell\Brands\Application\CRUD\Update\Request\Update;
use CrmSell\Brands\Application\CRUD\Update\UpdateHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandsController
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

        return $this->getResponse(
            $handler->handle(new Create($data))
        );
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

        return $this->getResponse(
            $handler->handle(new Update($data))
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function getBrandById(string $id): JsonResponse
    {
        $authUser = Auth::user();
        if (empty($authUser) || $authUser->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }

        $provider = Brand::find($id);

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
     * @param GetListHandler $handler
     * @return JsonResponse
     */
    public function getListByName(string $value, GetListHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        return $this->getResponse(
            $handler->getListByName($value)
        );
    }
}
