<?php

namespace CrmSell\Orders\Application\Orders\Update;


use CrmSell\Audit\Application\Service\AddToAudit\AddToAudit;
use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Orders\Application\Orders\Update\Request\Update;
use CrmSell\Orders\Domains\Entities\Order;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\ShipmentsRepositoryInterface;
use CrmSell\Providers\Domains\Entities\Provider;
use CrmSell\Providers\Domains\Enum\ProviderEnum;
use CrmSell\Status\Domains\Entities\Defect;
use CrmSell\Status\Domains\Entities\Status;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateHandler extends AbstractHandler
{


    private ShipmentsRepositoryInterface $repository;
    private AddToAudit $audit;

    /**
     * @param ShipmentsRepositoryInterface $repository
     * @param AddToAudit $audit
     */
    public function __construct(ShipmentsRepositoryInterface $repository, AddToAudit $audit)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->audit = $audit;
    }

    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    protected function process(RequestInterface $request): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->update($request);

            DB::commit();
        } catch (\DomainException $e) {
            DB::rollBack();

            $this->resultHandler->setStatusCode(400)
                ->setErrors([$e->getMessage()])
                ->setStatus(ResponseCodeErrors::BUSINESS_LOGIC_ERROR);

            return $this->resultHandler;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param Update $request
     * @return void
     * @throws \Exception
     */
    private function update(Update $request): void
    {
        $order = Order::find($request->getEntityId());

        if (empty($order->id)) {
            $this->resultHandler->setStatusCode(404)
                ->setErrors(["Order does not exist {$request->getEntityId()}."])
                ->setStatus(ResponseCodeErrors::BUSINESS_LOGIC_ERROR);
            return;
        }

        $this->check($order, $request);
        $order->fill(array_merge($request->forUpdate(), [
            'modified_user_id' => auth()->id(),
            'updated_at' => Carbon::now()->utc()->format('Y-m-d H:i:s'),
        ]));

        $this->audit->add($order, auth()->id(), $order->getAuditParams());
        if (!$order->save()) {
            throw new \Exception("Error save, try next time.", 500);
        }

        $this->resultHandler
            ->setStatusCode(200)
            ->setResult([
                "id" => $order->id
            ]);
    }

    /**
     * @param Order $order
     * @param Update $request
     * @return void
     */
    private function check(Order $order, Update $request): void
    {
        $forUpdate = $request->forUpdate();
        $totalShipmentForByOrder = $this->repository->getTotalShipmentForByOrder($request->getEntityId());

        if ($request->isThisField("amount_in_order_paid")) {
            $order->changeAmountInOrderPaid($forUpdate["amount_in_order_paid"], $totalShipmentForByOrder);
        }
        if ($request->isThisField("amount_in_order")) {
            $order->changeAmountInOrderPaid($forUpdate["amount_in_order"], $totalShipmentForByOrder);
        }
        if ($request->isThisField("status") && !Status::firstOrNew(['alias' => $forUpdate['status']])->exists()) {
            throw new \DomainException("Status does not exist: {$forUpdate['status']}");
        }
        if ($request->isThisField("defect") && !Defect::firstOrNew(['alias' => $forUpdate['defect']])->exists()) {
            throw new \DomainException("Defect does not exist: {$forUpdate['defect']}");
        }
        if ($request->isThisField("provider") && !Provider::firstOrNew(['id' => $forUpdate['provider']])->exists()) {
            throw new \DomainException("Provider does not exist: {$forUpdate['provider']}");
        }
        if ($this->checkRequiredComfy($order, $request)) {
            throw new \DomainException("Field is Required for 'Comfy' provider.");
        }
    }

    /**
     * @param Order $order
     * @param Update $request
     * @return bool
     */
    private function checkRequiredComfy(Order $order, Update $request): bool
    {
        if (!$request->isComfyField()) {
            return false;
        }

        $provider = Provider::find($order->provider_start);
        return $provider->type === ProviderEnum::COMFY->value;
    }
}
