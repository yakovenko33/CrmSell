<?php

namespace CrmSell\Providers\Infrastructure\Laravel\Provider;


use CrmSell\Common\Components\Pagination\Pagination;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Providers\Infrastructure\Repositories\Interfaces\ProvidersRepositoryInterface;
use CrmSell\Providers\Infrastructure\Repositories\ProvidersRepository;
use Illuminate\Support\ServiceProvider;

class ProvidersProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../../UI/Http/Routes/api.php');
        $this->bind();
    }

    private function bind(): void
    {
        $this->app->bind(ProvidersRepositoryInterface::class, ProvidersRepository::class);
        $this->app->bind(PaginationInterface::class, Pagination::class);
    }
}
