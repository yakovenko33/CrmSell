<?php

namespace CrmSell\Goods\Application\CRUD\Create;


use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use \CrmSell\Goods\Application\CRUD\Create\Request\Create;
use CrmSell\Goods\Domains\Entities\Goods;
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
                    "id" => $this->addEntity($command)
                ]);

            DB::commit();
        } catch (\DomainException $e) {
            DB::rollBack();

            return $this->resultHandler;
        }  catch (\Exception $e) {
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
    private function addEntity(Create $command): string
    {
        $status = Goods::create($this->getData($command));
        if (!$status->save()) {
            throw new \Exception("Error save, try next time.", 500);
        }
        return $status->id;
    }

    /**
     * @param Create $command
     * @return array
     */
    private function getData(Create $command): array
    {
        $userId = auth()->id();
        $date = Carbon::now()->utc()->format('Y-m-d H:i:s');

        return array_merge($command->toArray(), [
            'created_by' => $userId,
            'modified_user_id' => $userId,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
