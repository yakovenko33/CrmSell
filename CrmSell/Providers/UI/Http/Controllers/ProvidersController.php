<?php

namespace CrmSell\Providers\UI\Http\Controllers;


use CrmSell\Common\UI\Traits\ResponseTrait;
use CrmSell\Providers\Application\Create\CreateHandler;
use CrmSell\Providers\Application\Create\Request\Create;
use CrmSell\Providers\Application\GetList\GetListHandler;
use CrmSell\Providers\Application\GetList\Request\GetList;
use CrmSell\Providers\Application\Update\EditHandler;
use CrmSell\Providers\Application\Update\Request\Edit;
use CrmSell\Providers\Domains\Entities\Provider;
use CrmSell\Users\Domains\Entities\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProvidersController
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @param CreateHandler $handler
     * @return JsonResponse
     */
    public function create(Request $request, CreateHandler $handler): JsonResponse
    {
        /* @var User */
        $user = Auth::user();
        if (empty($user) || !$user->hasRole('admin') || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }

        $result = $handler->handle(new Create(
            json_decode($request->getContent(), true)
        ));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param EditHandler $handler
     * @return JsonResponse
     */
    public function edit(Request $request, EditHandler $handler): JsonResponse
    {
        /* @var User */
        $user = Auth::user();
        if (empty($user) || !$user->hasRole('admin') || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }

        $result = $handler->handle(new Edit(
            json_decode($request->getContent(), true)
        ));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param GetListHandler $handler
     * @return JsonResponse
     */
    public function getList(Request $request, GetListHandler $handler): JsonResponse
    {
        /* @var User */
        $user = Auth::user();
        if (empty($user) || !$user->hasRole('admin') || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }

        $result = $handler->handle(new GetList(
            $request->toArray()
        ));

        return $this->getResponse($result);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function getProviderById(string $id): JsonResponse
    {
        $authUser = Auth::user();
        if (empty($authUser) || $authUser->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }

        $provider = Provider::find($id);

        return $this->getSuccessfulResponse([
            "provider" => $provider->getDetail(),
        ]);
    }

    /**
     * @param GetListHandler $handler
     * @return JsonResponse
     */
    public function getListAll(GetListHandler $handler): JsonResponse
    {
        $result = $handler->getListAll();

        return $this->getResponse($result);
    }
}
