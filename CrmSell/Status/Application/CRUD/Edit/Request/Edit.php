<?php

namespace CrmSell\Status\Application\CRUD\Edit\Request;



use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;
use CrmSell\Status\Domains\Enum\StatusEnum;
use Illuminate\Validation\Rule;

class Edit extends RootRequest
{
    use PropertyTrait;

    private string $id = '';
    private string $name = '';
    private string $type = '';

    public function __construct(array $request)
    {
        $this->mapField($request);
    }

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }

    /**
     * @return array
     */
    public function getRules(): array {
        return [
            "name" => "required|string|max:30|min:2{$this->getRule()}",
            "type" => ['required','string','max:30','min:2', Rule::in([StatusEnum::STATUS->value, StatusEnum::DEFECT->value])],
        ];
    }

    /**
     * @return string
     */
    private function getRule(): string
    {
        if (StatusEnum::STATUS->value === $this->type) {
            return "|unique:status,name";
        }
        if (StatusEnum::DEFECT->value === $this->type) {
            return "|unique:defects,name";
        }
        return '';
    }

    /**
     * @return array
     */
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
            "type" => $this->type,
        ];
    }
}
