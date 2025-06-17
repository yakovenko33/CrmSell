<?php

namespace CrmSell\Brands\Infrastructure\Laravel\Database\Seeders\BrandSeeder;


use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class BrandSeeder extends Seeder
{
    private string $commandForStart = 'php artisan db:seed --class=CrmSell\Brands\Infrastructure\Laravel\Database\Seeders\BrandSeeder\BrandSeeder';
    //php8.2 artisan db:seed --class='CrmSell\Brands\Infrastructure\Laravel\Database\Seeders\BrandSeeder\BrandSeeder'

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $this->command->info('User John Doe added to the database!');
            $this->addBrands();

            DB::commit();
        } catch (QueryException|\Exception $e) {
            Log::warning($e->getMessage() . $e->getTraceAsString());
            DB::rollBack();
        }
    }

    /**
     * @return void
     */
    private function addBrands(): void
    {
        $date = Carbon::now()->format('Y-m-d H:i:s');
        $data = [];
        foreach ($this->readCsv() as $item) {
            $this->command->info('addBrands');
            $this->command->info(print_r($item));
            $id = Uuid::uuid4()->toString();
            $data[] = [
                'id' => $id,
                "name" => $item[1],
                'created_by' => '1',
                'modified_user_id' =>  '1',
                'created_at' => $date,
                'updated_at' => $date,
                'deprecated' => 0
            ];
        }
        DB::table('brands')->insert($data);
    }

    /**
     * @return \Generator
     */
    private function readCsv(): \Generator
    {
        $handle = fopen(__DIR__ . '/Brands.csv', "r");
        if ($handle === false) {
            throw new \RuntimeException("Unable to open file: 'Brands.csv'");
        }
        fgetcsv($handle, 1000, ",");
        try {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if (!empty($data) && empty($data[0])) {
                    $data[0] = '-blank-';
                }
                yield $data;
            }
        } finally {
            fclose($handle);
        }
    }
}
