<?php

namespace CrmSell\Orders\Application\Orders\Update\Request;

use Carbon\Carbon;
use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;
use CrmSell\Providers\Infrastructure\Repositories\Interfaces\ProvidersRepositoryInterface;

class Update extends RootRequest
{
    use PropertyTrait;

    const COMFY_FIELD = [
        "comfy_code",
        "comfy_goods_name",
        "comfy_brand",
        "comfy_category",
        "comfy_price",
    ];

    private string $order_number = '';
    private string $vendor_code = '';

    private string $goods_name = '';
    private string $manager_comment = '';

    private float $sell_price = 0.0;
    private string $status = '';

    private float $cost = 0.0;
    private string $provider_start = '';

    private string $comment = '';
    private string $defect = '';

    private int $amount_in_order = 0;
    private int $amount_in_order_paid = 0;

    private string $date_check = '';
    private string $comfy_code = '';

    private string $comfy_goods_name = '';
    private string $comfy_brand = '';

    private string $comfy_category = '';
    private float $comfy_price = 0.0;

    private string $fieldName = '';
    private string $entityId = '';

    private string $providerType = '';

    /**
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->fieldName = !empty($request['field']) ? $request['field'] : '';
        $this->entityId = !empty($request['entityId']) ? $request['entityId'] : '';

        if (!property_exists($this, $this->fieldName)) {
            throw new \DomainException("Field does not exist: {$this->fieldName}");
        }

        if ($this->fieldName === 'date_check') {
            $this->{$this->fieldName} = Carbon::parse($request['value'])->format("Y-m-d");
        } else {
            $data[$this->fieldName] = $request['value'];
            $this->mapField($data, ['date_check']);
        }
    }


    public function getFieldName(): string { return $this->fieldName; }
    public function getEntityId(): string { return $this->entityId; }

    public function isThisField(string $fieldName): bool {
        return $this->fieldName === $fieldName;
    }

    public function isComfyField(): bool {
        return in_array($this->fieldName, self::COMFY_FIELD);
    }

    public function forUpdate(): array {
        return $this->toValidation();
    }

    /**
     * @return array
     */
    public function toValidation(): array
    {
        return [
            $this->fieldName => $this->toArray()[$this->fieldName]
        ];
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            $this->fieldName => $this->getRulesList()[$this->fieldName]
        ];
    }

    public function getRulesList(): array
    {
        return [
            "order_number" => 'required|string|max:50',
            "vendor_code" => 'required|string|max:50',
            "sell_price" => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|gt:0',
            "cost" => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|gt:0',
            "manager_comment" => 'required|string|max:1000',
            "status" => 'required',
            'comment' => 'string|max:1000',
            "goods_name" => 'required|string|max:150',
            "provider_start" => 'required|string|exists:CrmSell\Providers\Domains\Entities\Provider,id',
            "amount_in_order" => 'required|numeric|gt:0',
            "amount_in_order_paid" => 'required|numeric|gt:0',
            "date_check" => 'date_format:Y-m-d',

            "comfy_code" => 'string|max:50',
            "comfy_goods_name" => 'string|max:150',
            "comfy_brand" => 'string|max:50',
            "comfy_category" => 'string|max:150',
            "comfy_price" => 'numeric|regex:/^\d+(\.\d{1,2})?$/|gt:0',
        ];
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "order_number" => $this->order_number,
            "vendor_code" => $this->vendor_code,
            "sell_price" => $this->sell_price,
            "manager_comment" => $this->manager_comment,
            "goods_name" => $this->goods_name,
            "status" => $this->status,
            "cost" => $this->cost,
            "provider_start" => $this->provider_start,
            "comment" => $this->comment,
            "amount_in_order_paid" => $this->amount_in_order_paid,
            "amount_in_order" => $this->amount_in_order,
            "defect" => $this->defect,
            "date_check" => $this->date_check,
            "comfy_code" => $this->comfy_code,
            "comfy_goods_name" => $this->comfy_goods_name,
            "comfy_brand" => $this->comfy_brand,
            "comfy_category" => $this->comfy_category,
            "comfy_price" => $this->comfy_price,
        ];
    }

    public function messages(): array
    {
        return [
            'sell_price.regex' => 'Цена должна быть дробным с двумя знаками после запятой.',
            'sell_price.gt' => 'Цена должна быть больше 0.',
            'cost.regex' => 'Цена закупки должна быть дробным с двумя знаками после запятой.',
            'cost.gt' => 'Цена закупки должна быть больше 0.',
            'amount_in_order.gt' => 'Количество заказов должно быть больше 0.',
            'amount_in_order_paid.gt' => 'Количество оплаченных заказов должно быть больше 0.',
            'comfy_price.regex' => 'Цена comfy должна быть дробным с двумя знаками после запятой.',
            'comfy_price.gt' => 'Цена comfy должна быть больше 0.',
        ];
    }
}
