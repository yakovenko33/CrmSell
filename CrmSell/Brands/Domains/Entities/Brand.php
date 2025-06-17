<?php

namespace CrmSell\Brands\Domains\Entities;


use CrmSell\Common\Domains\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use UuidTrait;

    protected $table = 'brands';

    /**
     * @var array
     */
    protected $fillable = [
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
            "id" => $this->id,
            "name" => $this->name,
        ];
    }
}
