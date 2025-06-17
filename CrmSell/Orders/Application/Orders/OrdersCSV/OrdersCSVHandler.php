<?php

namespace CrmSell\Orders\Application\Orders\OrdersCSV;


use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Orders\Application\Orders\OrdersCSV\Request\OrdersCSV;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\OrdersRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrdersCSVHandler
{
    private OrdersRepositoryInterface $repository;

    public function __construct(OrdersRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param OrdersCSV $request
     * @return StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(OrdersCSV $request): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
    {
        try {
            return response()->stream(function () use ($request) {
                $handle = fopen('php://output', 'w');

                fputcsv($handle, $this->getHeader());
                foreach ($this->repository->getListOrdersCSV($request) as $row) {
                    fputcsv($handle, $row);
                }

                fclose($handle);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="data.csv"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . " " . $e->getTraceAsString());

            return response()->json([
                'status' => ResponseCodeErrors::SERVER_ERROR,
                "data" => [],
                "errors" => ["Problem on server. Try next time."],
            ], 500);
        }
    }

    /**
     * @return string[]
     */
    private function getHeader(): array
    {
        return [
            'Менеджер',
            'Дата',
            '№ Замовлення',
            'Артикул',
            'Товар',
            'Коментар/уточнення постачальника',
            'Ціна',
            'Статус замовлення',
            'К-ть оплачених',
            'Ціна закупки',
            'К-ть забраних',
            'Залишок',
            'Постачальник',
            'Дата Чеку',
            'Коментар',
            'Списаний',
            'Код номенклатуры',
            'Наименование продукта',
            'Наименование бренда',
            'Наименование категории',
            'Цена/значение',
            'Цена/значение - Ціна закупки'
        ];
    }
}
