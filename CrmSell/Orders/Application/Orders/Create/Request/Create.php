<?php

namespace CrmSell\Orders\Application\Orders\Create\Request;


use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;
use CrmSell\Providers\Domains\Enum\ProviderEnum;
use CrmSell\Providers\Infrastructure\Repositories\Interfaces\ProvidersRepositoryInterface;

class Create extends RootRequest
{
    use PropertyTrait;

    private string $numberOrder = '';
    private string $goodsId = '';

    private float $sellPrice =  0.0;
    private string $managerComment = '';

    private string $providerStart = '';
    private int $amountInOrder = 0;
    private string $comfyCode = '';

    private string $comfyGoodsName = '';
    private string $comfyBrand = '';

    private string $comfyCategory = '';
    private float $comfyPrice = 0.0;

    private string $providerType = '';

    /**
     * @param array $request
     * @param ProvidersRepositoryInterface $repository
     */
    public function __construct(array $request, ProvidersRepositoryInterface $repository)
    {
        $this->mapField($request, ['providerType']);

        $value = $repository->getListAll()->first(function (\stdClass $value) {
            return $this->providerStart === $value->id;
        });
        $this->providerType = $value->type;
    }

    public function toValidation(): array
    {
        return [
            "numberOrder" => $this->numberOrder,
            "sellPrice" => $this->sellPrice,
            "managerComment" => $this->managerComment,
            "goodsId" => $this->goodsId,
            "providerStart" => $this->providerStart,
            "amountInOrder" => $this->amountInOrder,
            "comfyCode" => $this->comfyCode,
            "comfyGoodsName" => $this->comfyGoodsName,
            "comfyBrand" => $this->comfyBrand,
            "comfyCategory" => $this->comfyCategory,
            "comfyPrice" => $this->comfyPrice,
        ];
    }

    public function getRules(): array
    {
        return [
            "providerStart" => 'required|string|exists:CrmSell\Providers\Domains\Entities\Provider,id',
            "numberOrder" => 'required|string|max:50',
            "sellPrice" => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|gt:0',
            "managerComment" => 'string|max:1000',
            "goodsId" => 'required|string|exists:CrmSell\Goods\Domains\Entities\Goods,id',
            "amountInOrder" => 'required|numeric|gt:0',

            "comfyCode" => $this->comfyCode(),
            "comfyGoodsName" => $this->comfyGoodsName(),
            "comfyBrand" => $this->comfyBrand(),
            "comfyCategory" => $this->comfyCategory(),
            "comfyPrice" => $this->comfyPrice()
        ];
    }

    /**
     * @return string
     */
    private function comfyCode(): string
    {
        if ($this->comfyCode !== '' &&$this->providerType === ProviderEnum::COMFY->value) {
            return 'required|string|max:50';
        }
        return '';
    }

    /**
     * @return string
     */
    public function comfyGoodsName(): string
    {
        if ($this->comfyCode !== '' &&$this->providerType === ProviderEnum::COMFY->value) {
            return 'required|string|max:150';
        }
        return '';
    }

    /**
     * @return string
     */
    public function comfyBrand(): string
    {
        if ($this->comfyCode !== '' &&$this->providerType === ProviderEnum::COMFY->value) {
            return 'required|string|exists:CrmSell\Brands\Domains\Entities\Brand,id';
        }
        return '';
    }

    /**
     * @return string
     */
    public function comfyCategory(): string
    {
        if ($this->comfyCode !== '' &&$this->providerType === ProviderEnum::COMFY->value) {
            return 'required|string|max:150';
        }
        return '';
    }

    /**
     * @return string
     */
    public function comfyPrice(): string
    {
        if ($this->comfyCode !== '' &&$this->providerType === ProviderEnum::COMFY->value) {
            return 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|gt:0';
        }
        return '';
    }

    /**
     * @return array
     */
    public function toMap(): array
    {
        return [
            "order_number" => $this->numberOrder,
            "sell_price" => $this->sellPrice,
            "manager_comment" => $this->managerComment,
            "provider_start" => $this->providerStart,
            "amount_in_order" => $this->amountInOrder,
            "comfy_code" => $this->comfyCode,
            "comfy_goods_name" => $this->comfyGoodsName,
            "comfy_brand" => $this->comfyBrand,
            "comfy_category" => $this->comfyCategory,
            "comfy_price" => $this->comfyPrice,
            'amount_in_order_paid' => $this->amountInOrder,
            "goods_id" => $this->goodsId,
        ];
    }

    public function messages(): array
    {
        return [
            'sellPrice.regex' => 'Цена должна быть дробным с двумя знаками после запятой.',
            'sellPrice.gt' => 'Цена должна быть больше 0.',
            'amountInOrder.gt' => 'Количество заказов должно быть больше 0.',
            'comfyPrice.regex' => 'Цена comfy должна быть дробным с двумя знаками после запятой.',
            'comfyPrice.gt' => 'Цена comfy должна быть больше 0.',
        ];
    }
}
