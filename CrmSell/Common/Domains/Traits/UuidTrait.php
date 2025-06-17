<?php

namespace CrmSell\Common\Domains\Traits;


use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait UuidTrait
{
    public function getIncrementing()
    {
        return false;
    }
    public function getKeyType()
    {
        return 'string';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
}
