<?php

namespace CrmSell\Status\Application\CRUD\Create;


use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Status\Application\CRUD\Create\Request\Create;
use CrmSell\Status\Domains\Entities\Defect;
use CrmSell\Status\Domains\Entities\Status;
use CrmSell\Status\Domains\Enum\StatusEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
        if (StatusEnum::STATUS->value === $command->getType()) {
            return $this->addStatus($command);
        }
        if (StatusEnum::DEFECT->value === $command->getType()) {
            return $this->addDefect($command);
        }
        return '';
    }

    /**
     * @param Create $command
     * @return string
     * @throws \Exception
     */
    private function addStatus(Create $command): string
    {
        $status = Status::create($this->getData($command));
        if (!$status->save()) {
            throw new \Exception("Error save, try next time.", 500);
        }

        return $status->id;
    }

    /**
     * @param Create $command
     * @return string
     * @throws \Exception
     */
    private function addDefect(Create $command): string
    {
        $defect = Defect::create($this->getData($command));
        if (!$defect->save()) {
            throw new \Exception("Error save, try next time.", 500);
        }

        return $defect->id;
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
