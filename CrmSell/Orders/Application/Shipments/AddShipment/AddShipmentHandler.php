<?php

namespace CrmSell\Orders\Application\Shipments\AddShipment;


use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Orders\Application\Shipments\AddShipment\Request\AddShipment;
use CrmSell\Orders\Domains\Entities\Order;
use CrmSell\Orders\Domains\Entities\Shipment;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\OrdersRepositoryInterface;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\ShipmentsRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;

class AddShipmentHandler extends AbstractHandler
{
    private ShipmentsRepositoryInterface $repository;

    /**
     * @param ShipmentsRepositoryInterface $repository
     */
    public function __construct(ShipmentsRepositoryInterface $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    protected function process(RequestInterface $request): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->resultHandler
                ->setStatusCode(201)
                ->setResult([
                    "id" => $this->addShipment($request)
                ]);

            DB::commit();
        } catch (\DomainException $e) {
            DB::rollBack();

            return $this->resultHandler
                ->setStatusCode(424)
                ->setErrors([$e->getMessage()])
                ->setStatus(ResponseCodeErrors::BUSINESS_LOGIC_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param AddShipment $request
     * @return string
     * @throws \Exception
     */
    private function addShipment(AddShipment $request): string
    {
        $order = Order::where('id', $request->getOrderId())->lockForUpdate()->first();
        if (empty($order->id)) {
            throw new \DomainException("Order does not exist");
        }
        $totalShipment = $this->repository->getTotalShipmentForByOrder($order->id);
        if (($order->amount_in_order_paid - $totalShipment) < $request->getAmount()) {
            throw new \DomainException("Amount more `amount order paid`");
        }

        return $this->add($request);
    }

    /**
     * @param AddShipment $request
     * @return string
     * @throws \Exception
     */
    private function add(AddShipment $request): string
    {
        $userId = auth()->id();
        $date = Carbon::now()->utc()->format('Y-m-d H:i:s');

        $shipment = Shipment::create(array_merge($request->toArray(), [
            'created_by' => $userId,
            'created_at' => $date,
        ]));

        if (!$shipment->save()) {
            throw new \Exception("Error save, try next time.");
        }
        return $shipment->id;
    }
}
