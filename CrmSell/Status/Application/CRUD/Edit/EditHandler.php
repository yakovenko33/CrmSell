<?php

namespace CrmSell\Status\Application\CRUD\Edit;


use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Status\Application\CRUD\Create\Request\Create;
use CrmSell\Status\Application\CRUD\Edit\Request\Edit;
use CrmSell\Status\Domains\Entities\Defect;
use CrmSell\Status\Domains\Entities\Status;
use CrmSell\Status\Domains\Enum\StatusEnum;
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

            $this->updateEntity($command);

            DB::commit();
        }  catch (\DomainException $e) {
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
    private function updateEntity(Edit $command): void
    {
        $id = $command->getType() === StatusEnum::DEFECT->value ? $this->updateDefect($command) : $this->updateStatus($command);

        $this->resultHandler
            ->setStatusCode(200)
            ->setResult([
                "id" => $id
            ]);
    }

    /**
     * @param Edit $command
     * @return string
     * @throws \Exception
     */
    private function updateStatus(Edit $command): string
    {
        $status = Status::find($command->getId());
        if (empty($status->id)) {
            $this->addErrorExist();
        }

        if (!$status->update(array_merge($command->toArray(), [
            'modified_user_id' => auth()->id(),
            'updated_at' => Carbon::now()->utc()->format('Y-m-d H:i:s'),
        ]))) {
            throw new \Exception("Error save, try next time.", 500);
        }

        return $status->id;
    }

    /**
     * @param Edit $command
     * @return string
     * @throws \Exception
     */
    private function updateDefect(Edit $command): string
    {
        $defect = Defect::find($command->getId());
        if (empty($defect->id)) {
            $this->addErrorExist();
        }
        if (!$defect->update($this->getData($command))) {
            throw new \Exception("Error save, try next time.", 500);
        }
        return $defect->id;
    }

    /**
     * @return void
     */
    private function addErrorExist(): void
    {
        $this->resultHandler
            ->setStatusCode(404)
            ->setErrors(["Entity does not exist."])
            ->setStatus(ResponseCodeErrors::BUSINESS_LOGIC_ERROR);
        throw new \DomainException("Error save, try next time.", 500);
    }

    /**
     * @param Edit $command
     * @return array
     */
    private function getData(Edit $command): array
    {
        return array_merge($command->toArray(), [
            'modified_user_id' => auth()->id(),
            'updated_at' => Carbon::now()->utc()->format('Y-m-d H:i:s'),
        ]);
    }
}
