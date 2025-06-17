<?php

namespace CrmSell\Brands\Application\CRUD\Update;


use CrmSell\Brands\Application\CRUD\Update\Request\Update;
use CrmSell\Brands\Domains\Entities\Brand;
use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\Application\Service\Handler\AbstractHandler;
use CrmSell\Common\Application\Service\Handler\ResultHandler;
use CrmSell\Common\Application\Service\Request\RequestInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateHandler extends AbstractHandler
{
    /**
     * @param RequestInterface $request
     * @return ResultHandler
     */
    protected function process(RequestInterface $request): ResultHandler
    {
        try {
            DB::beginTransaction();

            $this->updateEntity($request);

            DB::commit();
        }  catch (\DomainException $e) {
            DB::rollBack();
            return $this->resultHandler;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::warning($e->getMessage() . " " . $e->getTraceAsString());

            $this->notSuccessfulResponse($e);
        }

        return $this->resultHandler;
    }

    /**
     * @param Update $command
     * @return void
     * @throws \Exception
     */
    private function updateEntity(Update $command): void
    {
        $brand = Brand::find($command->getId());
        if (empty($brand->id)) {
            $this->resultHandler->setStatusCode(404)
                ->setErrors(["Entity not exist."])
                ->setStatus(ResponseCodeErrors::BUSINESS_LOGIC_ERROR);
            return;
        }

        $this->checkExist($command, $brand);
        if (!$brand->update(array_merge($command->toArray(), [
            'modified_user_id' => auth()->id(),
            'updated_at' => Carbon::now()->utc()->format('Y-m-d H:i:s'),
        ]))) {
            throw new \Exception("Error save, try next time.", 500);
        }

        $this->resultHandler->setStatusCode()
            ->setResult(["id" => $brand->id]);
    }

    /**
     * @param Update $command
     * @param Brand $brand
     * @return void
     */
    private function checkExist(Update $command, Brand $brand): void
    {
        $brandByName = Brand::where('name', $command->getName())->first();
        if (!empty($brandByName->id) && $brandByName->id !== $brand->id) {
            $this->resultHandler->setStatusCode(422)->setErrors([
                [
                    "field" => 'name',
                    "message" => 'Name brand is exist.'
                ]
            ])->setStatus(ResponseCodeErrors::VALIDATE_ERROR);
        }
    }
}
