<?php

namespace Database\Seeders;

use CrmSell\Users\Domains\Entities\Role;
use Illuminate\Database\Seeder;
use CrmSell\Users\Domains\Entities\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    private string $description = "php artisan db:seed --class=DatabaseSeeder";
    /**
     * Seed the application"s database.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $email = env('SUPER_ADMIN_EMAIL');
            $userByEmail = User::whereIn('email', [$email])->get();
            if ($userByEmail->count() > 0) {
                return;
            }

            $user = User::create([
                'password' => bcrypt(env('SUPER_ADMIN_PASSWORD')),
                'email' => $email,
                'first_name' => 'SUPER_ADMIN',
                'last_name' => 'SUPER_ADMIN',
                'created_by' => '1',
                'modified_user_id' => '1',
            ]);
            $user->save();

            $role = Role::where('name', 'admin')->first();
            $user->assignRole($role);

            DB::commit();
        } catch (\Exception $e) {
            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            DB::rollBack();
        }
    }
}
