<?php

namespace CrmSell\Orders\Application\Orders\Create;


use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use CrmSell\Orders\Application\Orders\Create\Request\Create;
use CrmSell\Orders\Domains\Entities\Order;
use CrmSell\Status\Domains\Enum\DefectEnum;
use CrmSell\Status\Domains\Enum\OrderStatusEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateHandler extends AbstractHandler
{
    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    protected function process(RequestInterface $request): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->resultHandler
                ->setStatusCode(201)
                ->setResult([
                    "id" => $this->create($request)
                ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param Create $request
     * @return string
     * @throws \Exception
     */
    private function create(Create $request): string
    {
        $userId = auth()->id();
        $date = Carbon::now()->utc()->format('Y-m-d H:i:s');

        $order = Order::create(array_merge($request->toMap(), [
            'created_by' => $userId,
            'modified_user_id' => $userId,
            'created_at' => $date,
            'updated_at' => $date,
            "comment" => '',
            'manager' => $userId,
            'status' => OrderStatusEnum::NEW->value,
            'defect' => DefectEnum::IN_ORDER->value,
        ]));

        if (!$order->save()) {
            throw new \Exception("Error save, try next time.", 500);
        }

        return $order->id;
    }
}
