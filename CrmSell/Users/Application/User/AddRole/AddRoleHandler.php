<?php

namespace CrmSell\Users\Application\User\AddRole;


use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Users\Application\User\AddRole\Request\AddRole;
use CrmSell\Users\Application\User\UntieRole\Request\UntieRole;
use CrmSell\Users\Domains\Entities\Role;
use CrmSell\Users\Domains\Entities\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddRoleHandler extends AbstractHandler
{
    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    protected function process(RequestInterface $request): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->addRole($request);

            $this->resultHandler
                ->setStatusCode(200);

            DB::commit();
        } catch (\DomainException $e) {
            DB::rollBack();

            $this->notSuccessfulResponse($e);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param AddRole $command
     * @return void
     */
    private function addRole(AddRole $command): void
    {
        $user = User::find($command->getUserId());
        $role = Role::find($command->getRoleId());

        if ($user->hasRole($role->name)) {
            throw new \DomainException("User has roles: {$role->name}.", 422);
        }
        if (empty($user->id)) {
            throw new \DomainException("User does not exist.", 404);
        }
        if (empty($role->id)) {
            throw new \DomainException("User does not exist.", 404);
        }

        $user->assignRole($role);
    }
}
