<?php

namespace CrmSell\Providers\Application\Create;


use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Providers\Application\Create\Request\Create;
use CrmSell\Providers\Domains\Entities\Provider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateHandler extends AbstractHandler
{
    /**
     * @param RequestInterface $command
     * @return ResultHandler
     */
    protected function process(RequestInterface $command): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->resultHandler
                ->setStatusCode(201)
                ->setResult([
                    "id" => $this->addProvider($command)
                ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param Create $command
     * @return string
     * @throws \Exception
     */
    private function addProvider(Create $command): string
    {
        $userId = auth()->id();
        $date = Carbon::now()->utc()->format('Y-m-d H:i:s');

        $status = Provider::create(array_merge($command->toArray(), [
            'created_by' => $userId,
            'modified_user_id' => $userId,
            'created_at' => $date,
            'updated_at' => $date,
        ]));

        if (!$status->save()) {
            throw new \Exception("Error save, try next time.", 500);
        }

        return $status->id;
    }
}
