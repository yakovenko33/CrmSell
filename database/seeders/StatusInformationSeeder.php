<?php

namespace Database\Seeders;


use CrmSell\Status\Domains\Enum\StatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class StatusInformationSeeder extends Seeder
{
    private string $description = "php artisan db:seed --class=StatusInformationSeeder";

    public function run(): void
    {
        try {
            DB::beginTransaction();

            $this->addStatus();
            $this->addDefect();

            DB::commit();
        } catch (\Exception $e) {
            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            DB::rollBack();
        }
    }

    /**
     * @return void
     */
    private function addStatus(): void //Providers
    {
        $date = Carbon::now()->format('Y-m-d H:i:s');
        $data = [];
        foreach ($this->getStatus() as $item) {
            $id = Uuid::uuid4()->toString();
            $data[] = [
                'id' => $id,
                "name" => $item[0],
                "alias" => $item[1],
                'created_by' => '1',
                'modified_user_id' =>  '1',
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }
        DB::table('status')->insert($data);
    }

    /**
     * @return \string[][]
     */
    private function getStatus(): array
    {
        return [
            ["Замовлено", 'reserved'],
            ["Оплачено", 'paid'],
            ["Без чека", "without_check"],
            ["Враховано", "taken_into_account"],
            ["Обмін", "exchange"],
            ["Обміняли у постачальника на робочий(на склад +)", "exchanged_supplier"],
            ["Повернули постачальнику (+ гроші)", "returned_supplier"],
            ["Відмовився обмінювати постачальник (здати в сервіс)", "supplier_refused_exchange"],
            ["Помилково", "mistakenly"],
            ["Нове", "new"],
        ];
    }

    /**
     * @return void
     */
    private function addDefect(): void
    {
        $date = Carbon::now()->format('Y-m-d H:i:s');
        $data = [];
        foreach ($this->getDefect() as $item) {
            $id = Uuid::uuid4()->toString();
            $data[] = [
                'id' => $id,
                "name" => $item[0],
                "alias" => $item[1],
                'created_by' => '1',
                'modified_user_id' =>  '1',
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }
        DB::table('defects')->insert($data);
    }

    /**
     * @return \string[][]
     */
    private function getDefect(): array
    {
        return [
            ["Списаний", 'written_off'],
            ["В замовленні", 'in_order'],
        ];
    }
}
