<?php

namespace CrmSell\Orders\Application\Orders\GetList\Request;


use CrmSell\Common\Helpers\Traits\PropertyTrait;

class GetList extends \CrmSell\Common\Application\Service\Request\GetList
{
    use PropertyTrait;

    private array $filterParams = [];
    private string $order_date_from = '';
    private string $order_date_to = '';
    private string $vendor_code_value = '';
    private string $goods_name_value = '';
    private string $goods_id = '';
    private string $defect = '';
    private string $provider_start = '';
    private string $manager = '';
    private array $status = [];
    private string $date_check_from = '';
    private string $date_check_to = '';
    private string $comment = '';
    private string $order_number = '';
    private bool $remainder = false;

    /**
     * @param array $request
     */
    public function __construct(array $request)
    {
        parent::__construct($request);
        if (!empty($request['filterParams'])) {// TODO think about refactoting
            $this->mapField($request['filterParams'], ["pageNumber", "sortField", "sortDir", "status"]);
        }
        $this->status = !empty($request['filterParams']["status"]) ? $request['filterParams']["status"] : [];
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return [
            "order_date_from" => $this->order_date_from,
            "order_date_to" => $this->order_date_to,
            "vendor_code" => $this->vendor_code_value,
            "goods_name" => $this->goods_name_value,
            "defect" => $this->defect,
            "provider_start" => $this->provider_start,
            "manager" => $this->manager,
            "status" => $this->status,
            "date_check_from" => $this->date_check_from,
            "date_check_to" => $this->date_check_to,
            "comment" => $this->comment,
            "order_number" => $this->order_number,
            "remainder" =>  $this->remainder,
            "goods_id" =>  $this->goods_id,
        ];
    }

    protected function getSortFieldList(): array
    {
        return parent::getSortFieldList();
    }
}
