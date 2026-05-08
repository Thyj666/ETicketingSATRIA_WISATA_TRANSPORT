<?php

declare(strict_types=1);

namespace Infrastructure;

class DependencyInjection
{
    private array $bindings  = [];
    private array $instances = [];
    private AppDbContext $db;

    public function __construct(AppDbContext $db)
    {
        $this->db = $db;
    }

    public static function build(AppDbContext $db): self
    {
        $container = new self($db);
        $container->registerAll();
        return $container;
    }

    private function registerAll(): void
    {
        $db = $this->db;

        $files = [
            // Services/Clients
            BASE_PATH . '/07Client/Master/User/UserService.php',
            BASE_PATH . '/07Client/Master/Armada/ArmadaService.php',
            BASE_PATH . '/07Client/Transaction/Tiket/TiketService.php',
            BASE_PATH . '/07Client/Transaction/Pemesanan/PemesananService.php',
            BASE_PATH . '/07Client/Transaction/Laporan/LaporanService.php',
            // Application - User
            BASE_PATH . '/04Application/Master/User/Commands/Create/CreateUserCommand.php',
            BASE_PATH . '/04Application/Master/User/Commands/Update/UpdateUserCommand.php',
            BASE_PATH . '/04Application/Master/User/Commands/Delete/DeleteUserCommand.php',
            BASE_PATH . '/04Application/Master/User/Queries/GetUserByIdQuery.php',
            BASE_PATH . '/04Application/Master/User/Queries/GetUserByListQuery.php',
            // Application - Armada
            BASE_PATH . '/04Application/Master/Armada/Commands/Create/CreateArmadaCommand.php',
            BASE_PATH . '/04Application/Master/Armada/Commands/Update/UpdateArmadaCommand.php',
            BASE_PATH . '/04Application/Master/Armada/Commands/Delete/DeleteArmadaCommand.php',
            BASE_PATH . '/04Application/Master/Armada/Queries/GetArmadaByIdQuery.php',
            BASE_PATH . '/04Application/Master/Armada/Queries/GetArmadaByListQuery.php',
            // Application - Tiket
            BASE_PATH . '/04Application/Transaction/Tiket/Commands/Create/CreateTiketCommand.php',
            BASE_PATH . '/04Application/Transaction/Tiket/Commands/Update/UpdateTiketCommand.php',
            BASE_PATH . '/04Application/Transaction/Tiket/Commands/Delete/DeleteTiketCommand.php',
            BASE_PATH . '/04Application/Transaction/Tiket/Queries/GetTiketByIdQuery.php',
            BASE_PATH . '/04Application/Transaction/Tiket/Queries/GetTiketByListQuery.php',
            // Application - Pemesanan
            BASE_PATH . '/04Application/Transaction/Pemesanan/Commands/Create/CreatePemesananCommand.php',
            BASE_PATH . '/04Application/Transaction/Pemesanan/Queries/GetPemesananByIdQuery.php',
            BASE_PATH . '/04Application/Transaction/Pemesanan/Queries/GetPemesananByListQuery.php',
            // Application - Laporan
            BASE_PATH . '/04Application/Transaction/Laporan/Queries/GetLaporanQuery.php',
            // Controllers
            BASE_PATH . '/06WebApi/AuthController.php',
            BASE_PATH . '/06WebApi/DashboardController.php',
            BASE_PATH . '/06WebApi/ProfileController.php',
            BASE_PATH . '/06WebApi/Master/Armada/ArmadaController.php',
            BASE_PATH . '/06WebApi/Master/User/UserController.php',
            BASE_PATH . '/06WebApi/Transaction/Tiket/TiketController.php',
            BASE_PATH . '/06WebApi/Transaction/Pesanan/PemesananController.php',
            BASE_PATH . '/06WebApi/Transaction/Laporan/LaporanController.php',
        ];

        foreach ($files as $file) {
            if (file_exists($file)) require_once $file;
        }

        // Services
        $this->bind(
            \Client\Master\User\UserService::class,
            fn() => new \Client\Master\User\UserService($db)
        );
        $this->bind(
            \Client\Master\Armada\ArmadaService::class,
            fn() => new \Client\Master\Armada\ArmadaService($db)
        );
        $this->bind(
            \Client\Transaction\Tiket\TiketService::class,
            fn() => new \Client\Transaction\Tiket\TiketService($db)
        );
        $this->bind(
            \Client\Transaction\Pemesanan\PemesananService::class,
            fn() => new \Client\Transaction\Pemesanan\PemesananService($db)
        );
        $this->bind(
            \Client\Transaction\Laporan\LaporanService::class,
            fn() => new \Client\Transaction\Laporan\LaporanService($db)
        );

        // User Commands & Queries
        $this->bind(
            \Application\Master\User\Commands\Create\CreateUserCommand::class,
            fn() => new \Application\Master\User\Commands\Create\CreateUserCommand($this->make(\Client\Master\User\UserService::class))
        );
        $this->bind(
            \Application\Master\User\Commands\Update\UpdateUserCommand::class,
            fn() => new \Application\Master\User\Commands\Update\UpdateUserCommand($this->make(\Client\Master\User\UserService::class))
        );
        $this->bind(
            \Application\Master\User\Commands\Delete\DeleteUserCommand::class,
            fn() => new \Application\Master\User\Commands\Delete\DeleteUserCommand($this->make(\Client\Master\User\UserService::class))
        );
        $this->bind(
            \Application\Master\User\Queries\GetUserByIdQuery::class,
            fn() => new \Application\Master\User\Queries\GetUserByIdQuery($this->make(\Client\Master\User\UserService::class))
        );
        $this->bind(
            \Application\Master\User\Queries\GetUserByListQuery::class,
            fn() => new \Application\Master\User\Queries\GetUserByListQuery($this->make(\Client\Master\User\UserService::class))
        );

        // Armada
        $this->bind(
            \Application\Master\Armada\Commands\Create\CreateArmadaCommand::class,
            fn() => new \Application\Master\Armada\Commands\Create\CreateArmadaCommand($this->make(\Client\Master\Armada\ArmadaService::class))
        );
        $this->bind(
            \Application\Master\Armada\Commands\Update\UpdateArmadaCommand::class,
            fn() => new \Application\Master\Armada\Commands\Update\UpdateArmadaCommand($this->make(\Client\Master\Armada\ArmadaService::class))
        );
        $this->bind(
            \Application\Master\Armada\Commands\Delete\DeleteArmadaCommand::class,
            fn() => new \Application\Master\Armada\Commands\Delete\DeleteArmadaCommand($this->make(\Client\Master\Armada\ArmadaService::class))
        );
        $this->bind(
            \Application\Master\Armada\Queries\GetArmadaByIdQuery::class,
            fn() => new \Application\Master\Armada\Queries\GetArmadaByIdQuery($this->make(\Client\Master\Armada\ArmadaService::class))
        );
        $this->bind(
            \Application\Master\Armada\Queries\GetArmadaByListQuery::class,
            fn() => new \Application\Master\Armada\Queries\GetArmadaByListQuery($this->make(\Client\Master\Armada\ArmadaService::class))
        );

        // Tiket
        $this->bind(
            \Application\Transaction\Tiket\Commands\Create\CreateTiketCommand::class,
            fn() => new \Application\Transaction\Tiket\Commands\Create\CreateTiketCommand($this->make(\Client\Transaction\Tiket\TiketService::class))
        );
        $this->bind(
            \Application\Transaction\Tiket\Commands\Update\UpdateTiketCommand::class,
            fn() => new \Application\Transaction\Tiket\Commands\Update\UpdateTiketCommand($this->make(\Client\Transaction\Tiket\TiketService::class))
        );
        $this->bind(
            \Application\Transaction\Tiket\Commands\Delete\DeleteTiketCommand::class,
            fn() => new \Application\Transaction\Tiket\Commands\Delete\DeleteTiketCommand($this->make(\Client\Transaction\Tiket\TiketService::class))
        );
        $this->bind(
            \Application\Transaction\Tiket\Queries\GetTiketByIdQuery::class,
            fn() => new \Application\Transaction\Tiket\Queries\GetTiketByIdQuery($this->make(\Client\Transaction\Tiket\TiketService::class))
        );
        $this->bind(
            \Application\Transaction\Tiket\Queries\GetTiketByListQuery::class,
            fn() => new \Application\Transaction\Tiket\Queries\GetTiketByListQuery($this->make(\Client\Transaction\Tiket\TiketService::class))
        );

        // Pemesanan
        $this->bind(
            \Application\Transaction\Pemesanan\Commands\Create\CreatePemesananCommand::class,
            fn() => new \Application\Transaction\Pemesanan\Commands\Create\CreatePemesananCommand(
                $this->make(\Client\Transaction\Pemesanan\PemesananService::class),
                $this->make(\Client\Transaction\Tiket\TiketService::class)
            )
        );
        $this->bind(
            \Application\Transaction\Pemesanan\Queries\GetPemesananByIdQuery::class,
            fn() => new \Application\Transaction\Pemesanan\Queries\GetPemesananByIdQuery($this->make(\Client\Transaction\Pemesanan\PemesananService::class))
        );
        $this->bind(
            \Application\Transaction\Pemesanan\Queries\GetPemesananByListQuery::class,
            fn() => new \Application\Transaction\Pemesanan\Queries\GetPemesananByListQuery($this->make(\Client\Transaction\Pemesanan\PemesananService::class))
        );

        // Laporan
        $this->bind(
            \Application\Transaction\Laporan\Queries\GetLaporanQuery::class,
            fn() => new \Application\Transaction\Laporan\Queries\GetLaporanQuery($this->make(\Client\Transaction\Laporan\LaporanService::class))
        );

        // Controllers
        $this->bind(
            \WebApi\AuthController::class,
            fn() => new \WebApi\AuthController($this->make(\Client\Master\User\UserService::class))
        );
        $this->bind(
            \WebApi\DashboardController::class,
            fn() => new \WebApi\DashboardController(
                $this->make(\Client\Master\User\UserService::class),
                $this->make(\Client\Transaction\Tiket\TiketService::class),
                $this->make(\Client\Transaction\Pemesanan\PemesananService::class)
            )
        );
        $this->bind(
            \WebApi\ProfileController::class,
            fn() => new \WebApi\ProfileController(
                $this->make(\Application\Master\User\Commands\Update\UpdateUserCommand::class),
                $this->make(\Application\Master\User\Queries\GetUserByIdQuery::class)
            )
        );
        $this->bind(
            \WebApi\Master\Armada\ArmadaController::class,
            fn() => new \WebApi\Master\Armada\ArmadaController(
                $this->make(\Application\Master\Armada\Commands\Create\CreateArmadaCommand::class),
                $this->make(\Application\Master\Armada\Commands\Update\UpdateArmadaCommand::class),
                $this->make(\Application\Master\Armada\Commands\Delete\DeleteArmadaCommand::class),
                $this->make(\Application\Master\Armada\Queries\GetArmadaByIdQuery::class),
                $this->make(\Application\Master\Armada\Queries\GetArmadaByListQuery::class)
            )
        );
        $this->bind(
            \WebApi\Master\User\UserController::class,
            fn() => new \WebApi\Master\User\UserController(
                $this->make(\Application\Master\User\Commands\Create\CreateUserCommand::class),
                $this->make(\Application\Master\User\Commands\Update\UpdateUserCommand::class),
                $this->make(\Application\Master\User\Commands\Delete\DeleteUserCommand::class),
                $this->make(\Application\Master\User\Queries\GetUserByIdQuery::class),
                $this->make(\Application\Master\User\Queries\GetUserByListQuery::class)
            )
        );
        $this->bind(
            \WebApi\Transaction\Tiket\TiketController::class,
            fn() => new \WebApi\Transaction\Tiket\TiketController(
                $this->make(\Application\Transaction\Tiket\Commands\Create\CreateTiketCommand::class),
                $this->make(\Application\Transaction\Tiket\Commands\Update\UpdateTiketCommand::class),
                $this->make(\Application\Transaction\Tiket\Commands\Delete\DeleteTiketCommand::class),
                $this->make(\Application\Transaction\Tiket\Queries\GetTiketByIdQuery::class),
                $this->make(\Application\Transaction\Tiket\Queries\GetTiketByListQuery::class),
                $this->make(\Application\Master\Armada\Queries\GetArmadaByListQuery::class),
                $this->make(\Client\Transaction\Tiket\TiketService::class)
            )
        );
        $this->bind(
            \WebApi\Transaction\Pemesanan\PemesananController::class,
            fn() => new \WebApi\Transaction\Pemesanan\PemesananController(
                $this->make(\Application\Transaction\Pemesanan\Commands\Create\CreatePemesananCommand::class),
                $this->make(\Application\Transaction\Pemesanan\Queries\GetPemesananByIdQuery::class),
                $this->make(\Application\Transaction\Pemesanan\Queries\GetPemesananByListQuery::class),
                $this->make(\Application\Transaction\Tiket\Queries\GetTiketByIdQuery::class),
                $this->make(\Client\Transaction\Pemesanan\PemesananService::class)
            )
        );
        $this->bind(
            \WebApi\Transaction\Laporan\LaporanController::class,
            fn() => new \WebApi\Transaction\Laporan\LaporanController(
                $this->make(\Application\Transaction\Laporan\Queries\GetLaporanQuery::class)
            )
        );
    }

    public function bind(string $abstract, \Closure $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function make(string $abstract): mixed
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        if (isset($this->bindings[$abstract])) {
            $instance = ($this->bindings[$abstract])();
            $this->instances[$abstract] = $instance;
            return $instance;
        }
        throw new \RuntimeException("No binding found for: {$abstract}");
    }
}
