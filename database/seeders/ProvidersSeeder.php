<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ProvidersSeeder extends Seeder
{
    private string $description = "php artisan db:seed --class=ProvidersSeeder";

    public function run(): void
    {
        try {
            DB::beginTransaction();

            $this->addProviders();

            DB::commit();
        } catch (\Exception $e) {
            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            DB::rollBack();
        }
    }

    /**
     * @return void
     */
    private function addProviders(): void
    {
        $date = Carbon::now()->format('Y-m-d H:i:s');
        $data = [];
        foreach ($this->getProviders() as $item) {
            $id = Uuid::uuid4()->toString();
            $data[] = [
                'id' => $id,
                "name" => $item,
                'created_by' => '1',
                'modified_user_id' =>  '1',
                'created_at' => $date,
                'updated_at' => $date,
                'type' => str_contains($item, 'Comfy') ? 'comfy' : ''
            ];
        }
        DB::table('providers')->insert($data);
    }

    /**
     * @return \string[][]
     */
    private function getProviders(): array
    {
        return [
            "Comfy", "Eldorado", "DEX (Гуменюк)", "Игорь(борщаговка)", "Алладин", 'Променада', "Dream town",
            "Гуцо", "Сергиенко", "Comfy (Большевик", "Стас", "Другие", 'Ренклод', "Comfy (Блокбастер)", "Игорь Philips",
            "Герасев", "Cooper&Hunter", "Samsung", "LAVINA", "Полтава", "Харьков", "Вінниця", "АГД", "Suhini Gastrorag",
            "GRANADO", "Mystery", "ФОКСТРОТ", "Магазин Bosch", "OLX", "Sanford", "StoreInUA", "Епіцентр", "Другие (Метро)",
            "Другие (Женя SEB)", "КИТАЙ", 'Сергей Одинокий', "Comfy (Посуда)", "Alpicool", "Дісі", "Теплорадість", "Galati",
            "Ventolux", "Wetair", "Magio", "Термоімпульс", "BoilerON", "Comfy (Городок)", "MIRS", "IPC", "Aquila", "Праймтехнікс",
            "Emir", "Альфа Маркет", "Ромсат"
        ];
    }
}
