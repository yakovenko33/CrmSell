<?php

namespace CrmSell\Users\Infrastructure\Laravel\Provider;


use CrmSell\Common\Components\Pagination\Pagination;
use CrmSell\Common\Components\Pagination\PaginationInterface;
use CrmSell\Users\Infrastructure\Repositories\Interfaces\UsersRepositoryInterface;
use CrmSell\Users\Infrastructure\Repositories\UsersRepository;
use Illuminate\Support\ServiceProvider;

class UserProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../../UI/Http/Routes/api.php');

        $this->bind();
    }

    private function bind(): void
    {
        $this->app->bind(UsersRepositoryInterface::class, UsersRepository::class);
        $this->app->bind(PaginationInterface::class, Pagination::class);
    }
}
