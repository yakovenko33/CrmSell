<?php

namespace CrmSell\Status\UI\Http\Controllers;


use CrmSell\Common\UI\Traits\ResponseTrait;
use CrmSell\Status\Application\CRUD\Create\CreateHandler;
use CrmSell\Status\Application\CRUD\Create\Request\Create;
use CrmSell\Status\Application\CRUD\Edit\EditHandler;
use CrmSell\Status\Application\CRUD\Edit\Request\Edit;
use CrmSell\Status\Application\CRUD\GetList\GetListHandler;
use CrmSell\Status\Application\CRUD\GetList\Request\GetList;
use CrmSell\Status\Domains\Entities\Defect;
use CrmSell\Status\Domains\Entities\Status;
use CrmSell\Status\Domains\Enum\StatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController
{
    use ResponseTrait;

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
     * @param EditHandler $handler
     * @return JsonResponse
     */
    public function edit(Request $request, EditHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || !$user->hasRole('admin') || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $data = json_decode($request->getContent(), true);
        $result = $handler->handle(new Edit($data));

        return $this->getResponse($result);
    }

    /**
     * @param string $type
     * @param string $id
     * @return JsonResponse
     */
    public function getStatusById(string $type, string $id): JsonResponse
    {
        $authUser = Auth::user();
        if (empty($authUser) || !$authUser->hasRole('admin') || $authUser->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }
        if ($type === StatusEnum::STATUS->value) {
            $status = Status::find($id);
            return $this->getSuccessfulResponse([
                "status" => $status->getDetail(),
            ]);
        }
        if ($type === StatusEnum::DEFECT->value ) {
            $defect = Defect::find($id);
            return $this->getSuccessfulResponse([
                "status" => $defect->getDetail(),
            ]);
        }

        return $this->getErrorsResponse(["Type: $type does not exist."]);
    }

    /**
     * @param \CrmSell\Providers\Application\GetList\GetListHandler $handler
     * @return JsonResponse
     */
    public function getListAll(GetListHandler $handler): JsonResponse
    {
        $result = $handler->getListAll();

        return $this->getResponse($result);
    }

    /**
     * @param \CrmSell\Providers\Application\GetList\GetListHandler $handler
     * @return JsonResponse
     */
    public function getListAllDefect(GetListHandler $handler): JsonResponse
    {
        $result = $handler->getListAllDefect();

        return $this->getResponse($result);
    }
}
