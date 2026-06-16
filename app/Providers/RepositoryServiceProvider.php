<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Repositories\Contracts\BahanBakuRepositoryInterface;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use App\Repositories\Contracts\RiwayatStokRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\MenuRepository;
use App\Repositories\BahanBakuRepository;
use App\Repositories\TransaksiRepository;
use App\Repositories\RiwayatStokRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
        $this->app->bind(BahanBakuRepositoryInterface::class, BahanBakuRepository::class);
        $this->app->bind(TransaksiRepositoryInterface::class, TransaksiRepository::class);
        $this->app->bind(RiwayatStokRepositoryInterface::class, RiwayatStokRepository::class);
    }
}