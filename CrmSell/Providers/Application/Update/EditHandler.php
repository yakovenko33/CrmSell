<?php

namespace CrmSell\Providers\Application\Update;


use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use \CrmSell\Providers\Application\Update\Request\Edit;
use CrmSell\Providers\Domains\Entities\Provider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EditHandler extends AbstractHandler
{
    /**
     * @param RequestInterface $command
     * @return ResultHandler
     */
    protected function process(RequestInterface $command): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->updateStatus($command);

            DB::commit();
        } catch (\DomainException $e) {
            DB::rollBack();
            return $this->resultHandler;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param Edit $command
     * @return void
     * @throws \Exception
     */
    private function updateStatus(Edit $command): void
    {
        $provider = Provider::find($command->getId());

        if (empty($provider->id)) {
            $this->resultHandler->setStatusCode(404)->setErrors(["Entity not exist."])->setStatus(ResponseCodeErrors::BUSINESS_LOGIC_ERROR);
            return;
        }

        $this->checkExist($command, $provider);
        if (!$provider->update(array_merge($command->toArray(), [
            'modified_user_id' => auth()->id(),
            'updated_at' => Carbon::now()->utc()->format('Y-m-d H:i:s'),
        ]))) {
            throw new \Exception("Error save, try next time.", 500);
        }

        $this->resultHandler
            ->setStatusCode(200)
            ->setResult([
                "id" => $provider->id
            ]);
    }

    /**
     * @param Edit $command
     * @param Provider $provider
     * @return void
     */
    private function checkExist(Edit $command, Provider $provider): void
    {
        $providerByEmail = Provider::where('name', $command->getName())->first();
        if (!empty($providerByEmail->id) && $providerByEmail->id !== $provider->id) {
            $this->resultHandler->setStatusCode(422)->setErrors([
                [
                    "field" => 'name',
                    "message" => 'Name provider is exist.'
                ]
            ])->setStatus(ResponseCodeErrors::VALIDATE_ERROR);
        }
    }
}
