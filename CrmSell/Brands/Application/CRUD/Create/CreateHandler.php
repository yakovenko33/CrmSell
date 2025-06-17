<?php

namespace CrmSell\Brands\Application\CRUD\Create;


use CrmSell\Brands\Domains\Entities\Brand;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use \CrmSell\Brands\Application\CRUD\Create\Request\Create;
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
                    "id" => $this->addBrand($request)
                ]);

            DB::commit();
        } catch (\DomainException $e) {
            DB::rollBack();
            return $this->resultHandler;
        }  catch (\Exception $e) {
            DB::rollBack();

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param Create $command
     * @return string
     * @throws \Exception
     */
    private function addBrand(Create $command): string
    {
        $userId = auth()->id();
        $date = Carbon::now()->utc()->format('Y-m-d H:i:s');

        $brand = Brand::create(array_merge($command->toArray(), [
            'created_by' => $userId,
            'modified_user_id' => $userId,
            'created_at' => $date,
            'updated_at' => $date,
        ]));
        if (!$brand->save()) {
            throw new \Exception("Error save, try next time.", 500);
        }

        return $brand->id;
    }
}
