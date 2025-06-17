<?php

namespace CrmSell\Status\Application\CRUD\Create\Request;


use CrmSell\Status\Domains\Enum\StatusEnum;
use Illuminate\Validation\Rule;
use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;

class Create extends RootRequest
{
    use PropertyTrait;

    private string $name = '';
    private string $alias = '';
    private string $type = '';

    /**
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->mapField($request);
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            "name" => "required|string|max:50|min:2{$this->getRule('name')}",
            "alias" => "required|string|max:50|min:2{$this->getRule('alias')}",
            "type" => [Rule::in([StatusEnum::STATUS->value, StatusEnum::DEFECT->value])],
        ];
    }

    /**
     * @param string $field
     * @return string
     */
    private function getRule(string $field): string
    {
        if (StatusEnum::STATUS->value === $this->type) {
            return "|unique:status,{$field}";
        }
        if (StatusEnum::DEFECT->value === $this->type) {
            return "|unique:defects,{$field}";
        }
        return '';
    }


    public function getAlias(): string { return $this->alias; }
    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }

    public function toValidation(): array {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "alias" =>$this->alias,
            "type" => $this->type,
        ];
    }
}
