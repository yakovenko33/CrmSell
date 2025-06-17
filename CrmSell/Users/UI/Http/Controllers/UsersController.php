<?php

namespace CrmSell\Users\UI\Http\Controllers;


use CrmSell\Common\UI\Traits\ResponseTrait;
use CrmSell\Users\Application\User\AddRole\AddRoleHandler;
use CrmSell\Users\Application\User\AddRole\Request\AddRole;
use CrmSell\Users\Application\User\AddUser\AddUserHandler;
use CrmSell\Users\Application\User\AddUser\Request\AddUser;
use CrmSell\Users\Application\User\GetList\GetListHandler;
use CrmSell\Users\Application\User\GetList\Request\GetList;
use CrmSell\Users\Application\User\UntieRole\Request\UntieRole;
use CrmSell\Users\Application\User\UntieRole\UntieRoleHandler;
use CrmSell\Users\Application\User\Update\Request\Update;
use CrmSell\Users\Application\User\Update\UpdateHandler;
use CrmSell\Users\Domains\Entities\Role;
use CrmSell\Users\Infrastructure\Repositories\UsersRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CrmSell\Users\Domains\Entities\User;

class UsersController
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUser(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }

        return $this->getSuccessfulResponse([
            "user" => $user->getForInit(),
            "roles" => $user->getRoleNames(),
            "permissions" => []
        ]);
    }

    /**
     * @param Request $request
     * @param AddUserHandler $handler
     * @return JsonResponse
     */
    public function addUser(Request $request, AddUserHandler $handler): JsonResponse
    {
        /* @var User */
        $user = Auth::user();
        if (empty($user) || !$user->hasRole('admin') || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }

        $result = $handler->handle(new AddUser(
            json_decode($request->getContent(), true)
        ));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param UpdateHandler $handler
     * @return JsonResponse
     */
    public function updateUser(Request $request, UpdateHandler $handler): JsonResponse
    {
        /* @var User */
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }


        $result = $handler->handle(new Update(
            json_decode($request->getContent(), true)
        ));

        return $this->getResponse($result);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserById(string $id, Request $request): JsonResponse
    {
        $authUser = Auth::user();
        if (empty($authUser) || $authUser->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."]);
        }

        $user = User::find($id);

        return $this->getSuccessfulResponse([
            "user" => $user->getDetail(),
        ]);
    }

    /**
     * @param string $id
     * @param UsersRepository $repository
     * @return JsonResponse
     * @throws \Exception
     */
    public function getUserRolesId(string $id, UsersRepository $repository): JsonResponse
    {
        $authUser = Auth::user();
        if (empty($authUser) || $authUser->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        return $this->getSuccessfulResponse([
            "user_roles" => $repository->getUsersRolesList($id),
        ]);
    }

    /**
     * @param Request $request
     * @param AddRoleHandler $handler
     * @return JsonResponse
     */
    public function addRole(Request $request, AddRoleHandler $handler): JsonResponse
    {
        $authUser = Auth::user();
        if (empty($authUser) || $authUser->isNotActive() || !$authUser->hasRole('admin')) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $data = json_decode($request->getContent(), true);
        $result = $handler->handle(new AddRole($data));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param UntieRoleHandler $handler
     * @return JsonResponse
     */
    public function untieRole(Request $request, UntieRoleHandler $handler): JsonResponse
    {
        $authUser = Auth::user();
        if (empty($authUser) || !$authUser->hasRole('admin') || $authUser->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."],403);
        }

        $data = json_decode($request->getContent(), true);
        $result = $handler->handle(new UntieRole($data));

        return $this->getResponse($result);
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
     * @param Request $request
     * @return JsonResponse
     */
    public function getRoles(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || !$user->hasRole('admin') || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $roles = Role::all()->map(function ($item) {
            return [
                "key" => $item->id,
                "value" => $item->name,
            ];
        })->sortBy(function ($item) {
            return strtolower($item['value']);
        });

        return $this->getSuccessfulResponse($roles->toArray());
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
