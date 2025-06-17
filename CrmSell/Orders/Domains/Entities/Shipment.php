<?php

namespace CrmSell\Orders\Domains\Entities;


use CrmSell\Common\Domains\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use UuidTrait;

    protected $table = 'shipments';

    protected $fillable = [
        'amount',
        'shipment_date',
        "order_id",
        'created_by',
        "created_at",
    ];
}
