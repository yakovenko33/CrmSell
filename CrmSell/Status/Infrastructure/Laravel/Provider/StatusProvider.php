<?php

namespace CrmSell\Status\Infrastructure\Laravel\Provider;


use CrmSell\Common\Components\Pagination\Pagination;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Status\Infrastructure\Repositories\Interfaces\StatusRepositoryInterface;
use CrmSell\Status\Infrastructure\Repositories\StatusRepository;
use Illuminate\Support\ServiceProvider;

class StatusProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../../UI/Http/Routes/api.php');
        $this->bind();
    }

    private function bind(): void
    {
        $this->app->bind(StatusRepositoryInterface::class, StatusRepository::class);
        $this->app->bind(PaginationInterface::class, Pagination::class);
    }
}
