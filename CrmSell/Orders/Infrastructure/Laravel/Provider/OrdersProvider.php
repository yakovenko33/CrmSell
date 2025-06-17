<?php

namespace CrmSell\Orders\Infrastructure\Laravel\Provider;


use CrmSell\Common\Components\Pagination\Pagination;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\OrdersRepositoryInterface;
use CrmSell\Orders\Infrastructure\Repositories\Interfaces\ShipmentsRepositoryInterface;
use CrmSell\Orders\Infrastructure\Repositories\OrdersRepository;
use CrmSell\Orders\Infrastructure\Repositories\ShipmentsRepository;
use Illuminate\Support\ServiceProvider;

class OrdersProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../../UI/Http/Routes/api.php');
        $this->bind();
    }

    private function bind(): void
    {
        $this->app->bind(OrdersRepositoryInterface::class, OrdersRepository::class);
        $this->app->bind(ShipmentsRepositoryInterface::class, ShipmentsRepository::class);
        $this->app->bind(PaginationInterface::class, Pagination::class);
    }
}
