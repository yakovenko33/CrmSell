<?php

namespace CrmSell\Users\Application\User\UntieRole;

use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Users\Application\User\UntieRole\Request\UntieRole;
use CrmSell\Users\Domains\Entities\Role;
use CrmSell\Users\Domains\Entities\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UntieRoleHandler extends AbstractHandler
{
    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    protected function process(RequestInterface $request): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->untieRole($request);

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
     * @param UntieRole $command
     * @return void
     */
    private function untieRole(UntieRole $command): void
    {
        $user = User::find($command->getUserId());
        $role = Role::find($command->getRoleId());

        if (empty($user->id)) {
            throw new \DomainException("User does not exist.", 404);
        }
        if (empty($role->id)) {
            throw new \DomainException("User does not exist.", 404);
        }

        $user->removeRole($role);
    }
}
