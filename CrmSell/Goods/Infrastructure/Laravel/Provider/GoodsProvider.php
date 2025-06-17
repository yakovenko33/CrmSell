<?php

namespace CrmSell\Goods\Infrastructure\Laravel\Provider;


use CrmSell\Common\Components\Pagination\Pagination;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Goods\Infrastructure\Repositories\GoodsRepository;
use Illuminate\Support\ServiceProvider;
use CrmSell\Goods\Infrastructure\Repositories\Interfaces\GoodsRepositoryInterface;

class GoodsProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../../UI/Http/Routes/api.php');
        $this->bind();
    }

    private function bind(): void
    {
        $this->app->bind(GoodsRepositoryInterface::class, GoodsRepository::class);
        $this->app->bind(PaginationInterface::class, Pagination::class);
    }
}
