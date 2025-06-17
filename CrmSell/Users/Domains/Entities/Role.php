<?php

namespace CrmSell\Users\Domains\Entities;


use CrmSell\Common\Domains\Traits\UuidTrait;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use UuidTrait;
}
