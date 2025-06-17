<?php

namespace CrmSell\Goods\Domains\Entities;


use CrmSell\Common\Domains\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    use UuidTrait;

    protected $table = 'goods';

    /**
     * @var array
     */
    protected $fillable = [
        'vendor_code',
        'name',
        'created_by',
        'modified_user_id',
        "created_at",
        "updated_at",
        "deprecated"
    ];

    public function getDetail(): array
    {
        return [
            "name" => $this->name,
            "vendor_code" => $this->vendor_code,
            "deprecated" => $this->deprecated
        ];
    }
}
