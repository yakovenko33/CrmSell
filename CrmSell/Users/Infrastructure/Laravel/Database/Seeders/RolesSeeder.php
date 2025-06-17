<?php

namespace CrmSell\Users\Infrastructure\Laravel\Database\Seeders;


use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use CrmSell\Users\Domains\Entities\Role;

class RolesSeeder extends Seeder
{
    private string $commandForStart = 'php artisan db:seed --class=CrmSell\Users\Infrastructure\Laravel\Database\Seeders\RolesSeeder'; //php8.2 artisan db:seed --class='CrmSell\Users\Infrastructure\Laravel\Database\Seeders\RolesSeeder'

    const ROLES = [
        'admin',
        'manager',
        'accountant',
        'sell-manager',
    ];

    /**
     * @throws \Exception
     */
    public function run()
    {
        try {
            DB::beginTransaction();

            foreach (self::ROLES as $role) {
                $role = Role::create([
                    'id' => Uuid::uuid4(),
                    'name' => $role
                ]);

                if (!$role->save()) {
                    throw new \Exception("Not created super administrator role.");
                }
            }

            DB::commit();
        } catch (QueryException|\Exception $e) {
            Log::warning($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
        }
    }
}
