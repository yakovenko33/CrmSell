<?php

namespace CrmSell\Orders\UI\Http\Controllers;

use CrmSell\Common\Application\Service\Enum\ResponseCodeErrors;
use CrmSell\Common\UI\Traits\ResponseTrait;
use CrmSell\Orders\Application\Orders\Create\CreateHandler;
use CrmSell\Orders\Application\Orders\Create\Request\Create;
use CrmSell\Orders\Application\Orders\GetList\GetListHandler;
use CrmSell\Orders\Application\Orders\GetList\Request\GetList;
use CrmSell\Orders\Application\Orders\OrdersCSV\OrdersCSVHandler;
use CrmSell\Orders\Application\Orders\OrdersCSV\Request\OrdersCSV;
use CrmSell\Orders\Application\Orders\Update\Request\Update;
use CrmSell\Orders\Application\Orders\Update\UpdateHandler;
use CrmSell\Orders\Application\Shipments\AddShipment\AddShipmentHandler;
use CrmSell\Orders\Application\Shipments\AddShipment\Request\AddShipment;
use CrmSell\Orders\Application\Shipments\ShipmentsHistory\Request\ShipmentsHistory;
use CrmSell\Orders\Application\Shipments\ShipmentsHistory\ShipmentsHistoryHandler;
use CrmSell\Providers\Infrastructure\Repositories\ProvidersRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @param CreateHandler $handler
     * @return JsonResponse
     */
    public function create(Request $request, CreateHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $data = json_decode($request->getContent(), true);
        $result = $handler->handle(new Create($data, new ProvidersRepository()));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param GetListHandler $handler
     * @return JsonResponse
     */
    public function getOrders(Request $request, GetListHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }
        $data = json_decode($request->getContent(), true);
        $result = $handler->handle(new GetList($data));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param AddShipmentHandler $handler
     * @return JsonResponse
     */
    public function addShipment(Request $request, AddShipmentHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $result = $handler->handle(new AddShipment($request->toArray()));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param ShipmentsHistoryHandler $handler
     * @return JsonResponse
     */
    public function shipmentsHistory(Request $request, ShipmentsHistoryHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $result = $handler->handle(new ShipmentsHistory($request->toArray()));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param UpdateHandler $handler
     * @return JsonResponse
     */
    public function patchOrder(Request $request, UpdateHandler $handler): JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        $data = json_decode($request->getContent(), true);
        $result = $handler->handle(new Update($data));

        return $this->getResponse($result);
    }

    /**
     * @param Request $request
     * @param OrdersCSVHandler $handler
     * @return JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFileOrdersCSV(Request $request, OrdersCSVHandler $handler): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (empty($user) || $user->isNotActive()) {
            return $this->getErrorsResponse(["Access is denied."], 403);
        }

        return $handler->handle(new OrdersCSV($request->toArray()));
    }
}
