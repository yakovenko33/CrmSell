<?php

namespace CrmSell\Orders\Application\Shipments\AddShipment\Request;


use Carbon\Carbon;
use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;

class AddShipment extends RootRequest
{
    use PropertyTrait;

    private string $orderId = '';
    private int $amount = 0;
    private string $shipmentDate = '';

    /**
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->mapField($request);
        $this->shipmentDate = !empty($request['shipmentDate']) ? Carbon::parse($request['shipmentDate'])->format('Y-m-d') : '';
    }

    public function getOrderId(): string { return $this->orderId; }
    public function getAmount(): string { return $this->amount; }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "order_id" => $this->orderId,
            "shipment_date" => $this->shipmentDate,
            "amount" => $this->amount,
        ];
    }

    public function getRules(): array {
        return ['amount' => 'required|integer|min:0'];
    }

    public function toValidation(): array {
        return ['amount' => $this->amount];
    }
}
