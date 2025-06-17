<?php

namespace CrmSell\Users\Application\User\Update;

use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Users\Application\User\Update\Request\Update;
use CrmSell\Users\Domains\Entities\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UpdateHandler extends AbstractHandler
{
    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    protected function process(RequestInterface $request): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->updateUser($request);

            DB::commit();
        } catch (\DomainException $e) {
            DB::rollBack();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param Update $request
     * @return void
     * @throws \Exception
     */
    protected function updateUser(Update $request): void
    {
        $userAuth = Auth::user();
        if ($userAuth->id !== $request->getEntityId() && !$userAuth->hasRole('admin')) {
            $this->resultHandler->setStatusCode(403)->setErrors(["Access is denied."])->setStatus(ResponseCodeErrors::FORBIDDEN_ERROR);
            return;
        }

        $user = User::find($request->getEntityId());
        $userByEmail = User::where('email', $request->getEmail())->first();
        if (!empty($userByEmail->id) && $userByEmail->id !== $user->id) {
            $this->resultHandler->setStatusCode(422)->setErrors([
                [
                    "field" => 'email',
                    "message" => 'Email is occupied by another user.'
                ]
            ])->setStatus(ResponseCodeErrors::VALIDATE_ERROR);
            return;
        }
        $this->saveData($request, $user);
    }

    /**
     * @param Update $request
     * @param User $user
     * @return void
     * @throws \Exception
     */
    protected function saveData(Update $request, User $user): void
    {
        $forUpdate = ($request->getSwitchResetPassword())
            ? array_merge($request->forUpdate(), ["password" => bcrypt($request->getPassword())])
            : $request->forUpdate();

        if (!$user->update(array_merge([
            'modified_user_id' => auth()->id(),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ], $forUpdate))) {
            throw new \Exception("Error save, try next time.", 500);
        }
        $this->resultHandler->setStatusCode()->setStatus();
    }
}
