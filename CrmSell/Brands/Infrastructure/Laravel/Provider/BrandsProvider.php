<?php

namespace CrmSell\Brands\Infrastructure\Laravel\Provider;


use CrmSell\Brands\Infrastructure\Repositories\BrandsRepository;
use CrmSell\Brands\Infrastructure\Repositories\Interfaces\BrandsRepositoryInterface;
use Illuminate\Support\ServiceProvider;
class BrandsProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../../UI/Http/Routes/api.php');
        $this->bind();
    }

    private function bind(): void
    {
        $this->app->bind(BrandsRepositoryInterface::class, BrandsRepository::class);
    }
}
