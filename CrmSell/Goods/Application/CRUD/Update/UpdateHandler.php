<?php

namespace CrmSell\Goods\Application\CRUD\Update;


use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Goods\Application\CRUD\Update\Request\Update;
use CrmSell\Goods\Domains\Entities\Goods;
use CrmSell\Goods\Infrastructure\Repositories\Interfaces\GoodsRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateHandler extends AbstractHandler
{
    private GoodsRepositoryInterface $repository;

    public function __construct(GoodsRepositoryInterface $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

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
     * @param Update $command
     * @return void
     * @throws \Exception
     */
    private function updateStatus(Update $command): void
    {
        $goods = Goods::find($command->getId());

        if (empty($goods->id)) {
            $this->resultHandler->setStatusCode(404)->setErrors(["Access is denied."])->setStatus(ResponseCodeErrors::BUSINESS_LOGIC_ERROR);
            return;
        }
        $this->checkExistVendorCode($command);
        $this->checkExistName($command);

        if (!$goods->update(array_merge($command->toArray(), [
            'modified_user_id' => auth()->id(),
            'updated_at' => Carbon::now()->utc()->format('Y-m-d H:i:s'),
        ]))) {
            throw new \Exception("Error save, try next time.", 500);
        }

        $this->resultHandler
            ->setStatusCode(200)
            ->setResult([
                "id" => $goods->id
            ]);
    }

    /**
     * @param Update $command
     * @return void
     */
    private function checkExistVendorCode(Update $command): void
    {
        if ($this->repository->checkExistSimilar($command->getId(), 'vendor_code', $command->getVendorCode())) {
            $this->resultHandler->setStatusCode(404)
                ->setErrors([
                    "field" => 'vendor_code',
                    "message" => "Value exist."
                ])
                ->setStatus(ResponseCodeErrors::VALIDATE_ERROR);
            throw \DomainException("Exist 'vendor_code':{$command->getVendorCode()} in CRM");
        }
    }

    /**
     * @param Update $command
     * @return void
     */
    private function checkExistName(Update $command): void
    {
        if ($this->repository->checkExistSimilar($command->getId(), 'name', $command->getName())) {
            $this->resultHandler->setStatusCode(404)
                ->setErrors([
                    "field" => 'name',
                    "message" => "Value exist."
                ])
                ->setStatus(ResponseCodeErrors::VALIDATE_ERROR);
            throw \DomainException("Exist 'name':{$command->getName()} in CRM");
        }
    }
}
