<?php

namespace CrmSell\Providers\Domains\Entities;


use CrmSell\Common\Domains\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use UuidTrait;

    protected $table = 'providers';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'created_by',
        'modified_user_id',
        "created_at",
        "updated_at",
    ];

    public function getDetail(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
        ];
    }
}
