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
            BASE_PATH . '/07Client/Master/Admin/AdminService.php',
            BASE_PATH . '/07Client/Master/Pelanggan/PelangganService.php',
            BASE_PATH . '/07Client/Master/Pimpinan/PimpinanService.php',
            // Application - Admin
            BASE_PATH . '/04Application/Master/Admin/Commands/Create/CreateAdminCommand.php',
            BASE_PATH . '/04Application/Master/Admin/Commands/Update/UpdateAdminCommand.php',
            BASE_PATH . '/04Application/Master/Admin/Commands/Delete/DeleteAdminCommand.php',
            BASE_PATH . '/04Application/Master/Admin/Queries/GetAdminByListQuery.php',
            BASE_PATH . '/04Application/Master/Admin/Queries/GetAdminByIdQuery.php',
            // Application - Pelanggan
            BASE_PATH . '/04Application/Master/Pelanggan/Commands/Create/CreatePelangganCommand.php',
            BASE_PATH . '/04Application/Master/Pelanggan/Commands/Update/UpdatePelangganCommand.php',
            BASE_PATH . '/04Application/Master/Pelanggan/Commands/Delete/DeletePelangganCommand.php',
            BASE_PATH . '/04Application/Master/Pelanggan/Queries/GetPelangganByListQuery.php',
            BASE_PATH . '/04Application/Master/Pelanggan/Queries/GetPelangganByIdQuery.php',
            // Application - Pimpinan
            BASE_PATH . '/04Application/Master/Pimpinan/Commands/Create/CreatePimpinanCommand.php',
            BASE_PATH . '/04Application/Master/Pimpinan/Commands/Update/UpdatePimpinanCommand.php',
            BASE_PATH . '/04Application/Master/Pimpinan/Commands/Delete/DeletePimpinanCommand.php',
            BASE_PATH . '/04Application/Master/Pimpinan/Queries/GetPimpinanByListQuery.php',
            BASE_PATH . '/04Application/Master/Pimpinan/Queries/GetPimpinanByIdQuery.php',
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
            BASE_PATH . '/06WebApi/Master/Admin/AdminController.php',
            BASE_PATH . '/06WebApi/Master/Pelanggan/PelangganController.php',
            BASE_PATH . '/06WebApi/Master/Pimpinan/PimpinanController.php',
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

        // Role services
        $this->bind(
            \Client\Master\Admin\AdminService::class,
            fn() => new \Client\Master\Admin\AdminService($db)
        );
        $this->bind(
            \Client\Master\Pelanggan\PelangganService::class,
            fn() => new \Client\Master\Pelanggan\PelangganService($db)
        );
        $this->bind(
            \Client\Master\Pimpinan\PimpinanService::class,
            fn() => new \Client\Master\Pimpinan\PimpinanService($db)
        );

        // Admin Commands
        $this->bind(
            \Application\Master\Admin\Commands\Create\CreateAdminCommand::class,
            fn() => new \Application\Master\Admin\Commands\Create\CreateAdminCommand(
                $this->make(\Client\Master\Admin\AdminService::class),
                $this->make(\Client\Master\User\UserService::class)
            )
        );
        $this->bind(
            \Application\Master\Admin\Commands\Update\UpdateAdminCommand::class,
            fn() => new \Application\Master\Admin\Commands\Update\UpdateAdminCommand(
                $this->make(\Client\Master\Admin\AdminService::class),
                $this->make(\Client\Master\User\UserService::class)
            )
        );
        $this->bind(
            \Application\Master\Admin\Commands\Delete\DeleteAdminCommand::class,
            fn() => new \Application\Master\Admin\Commands\Delete\DeleteAdminCommand($this->make(\Client\Master\Admin\AdminService::class))
        );
        $this->bind(
            \Application\Master\Admin\Queries\GetAdminByListQuery::class,
            fn() => new \Application\Master\Admin\Queries\GetAdminByListQuery($this->make(\Client\Master\Admin\AdminService::class))
        );
        $this->bind(
            \Application\Master\Admin\Queries\GetAdminByIdQuery::class,
            fn() => new \Application\Master\Admin\Queries\GetAdminByIdQuery($this->make(\Client\Master\Admin\AdminService::class))
        );

        // Pelanggan Commands
        $this->bind(
            \Application\Master\Pelanggan\Commands\Create\CreatePelangganCommand::class,
            fn() => new \Application\Master\Pelanggan\Commands\Create\CreatePelangganCommand(
                $this->make(\Client\Master\Pelanggan\PelangganService::class),
                $this->make(\Client\Master\User\UserService::class)
            )
        );
        $this->bind(
            \Application\Master\Pelanggan\Commands\Update\UpdatePelangganCommand::class,
            fn() => new \Application\Master\Pelanggan\Commands\Update\UpdatePelangganCommand(
                $this->make(\Client\Master\Pelanggan\PelangganService::class),
                $this->make(\Client\Master\User\UserService::class)
            )
        );
        $this->bind(
            \Application\Master\Pelanggan\Commands\Delete\DeletePelangganCommand::class,
            fn() => new \Application\Master\Pelanggan\Commands\Delete\DeletePelangganCommand($this->make(\Client\Master\Pelanggan\PelangganService::class))
        );
        $this->bind(
            \Application\Master\Pelanggan\Queries\GetPelangganByListQuery::class,
            fn() => new \Application\Master\Pelanggan\Queries\GetPelangganByListQuery($this->make(\Client\Master\Pelanggan\PelangganService::class))
        );
        $this->bind(
            \Application\Master\Pelanggan\Queries\GetPelangganByIdQuery::class,
            fn() => new \Application\Master\Pelanggan\Queries\GetPelangganByIdQuery($this->make(\Client\Master\Pelanggan\PelangganService::class))
        );

        // Pimpinan Commands
        $this->bind(
            \Application\Master\Pimpinan\Commands\Create\CreatePimpinanCommand::class,
            fn() => new \Application\Master\Pimpinan\Commands\Create\CreatePimpinanCommand(
                $this->make(\Client\Master\Pimpinan\PimpinanService::class),
                $this->make(\Client\Master\User\UserService::class)
            )
        );
        $this->bind(
            \Application\Master\Pimpinan\Commands\Update\UpdatePimpinanCommand::class,
            fn() => new \Application\Master\Pimpinan\Commands\Update\UpdatePimpinanCommand(
                $this->make(\Client\Master\Pimpinan\PimpinanService::class),
                $this->make(\Client\Master\User\UserService::class)
            )
        );
        $this->bind(
            \Application\Master\Pimpinan\Commands\Delete\DeletePimpinanCommand::class,
            fn() => new \Application\Master\Pimpinan\Commands\Delete\DeletePimpinanCommand($this->make(\Client\Master\Pimpinan\PimpinanService::class))
        );
        $this->bind(
            \Application\Master\Pimpinan\Queries\GetPimpinanByListQuery::class,
            fn() => new \Application\Master\Pimpinan\Queries\GetPimpinanByListQuery($this->make(\Client\Master\Pimpinan\PimpinanService::class))
        );
        $this->bind(
            \Application\Master\Pimpinan\Queries\GetPimpinanByIdQuery::class,
            fn() => new \Application\Master\Pimpinan\Queries\GetPimpinanByIdQuery($this->make(\Client\Master\Pimpinan\PimpinanService::class))
        );

        // Role Controllers
        $this->bind(
            \WebApi\Master\Admin\AdminController::class,
            fn() => new \WebApi\Master\Admin\AdminController(
                $this->make(\Application\Master\Admin\Commands\Create\CreateAdminCommand::class),
                $this->make(\Application\Master\Admin\Commands\Update\UpdateAdminCommand::class),
                $this->make(\Application\Master\Admin\Commands\Delete\DeleteAdminCommand::class),
                $this->make(\Application\Master\Admin\Queries\GetAdminByListQuery::class),
                $this->make(\Application\Master\Admin\Queries\GetAdminByIdQuery::class),
            )
        );
        $this->bind(
            \WebApi\Master\Pelanggan\PelangganController::class,
            fn() => new \WebApi\Master\Pelanggan\PelangganController(
                $this->make(\Application\Master\Pelanggan\Commands\Create\CreatePelangganCommand::class),
                $this->make(\Application\Master\Pelanggan\Commands\Update\UpdatePelangganCommand::class),
                $this->make(\Application\Master\Pelanggan\Commands\Delete\DeletePelangganCommand::class),
                $this->make(\Application\Master\Pelanggan\Queries\GetPelangganByListQuery::class),
                $this->make(\Application\Master\Pelanggan\Queries\GetPelangganByIdQuery::class),
            )
        );
        $this->bind(
            \WebApi\Master\Pimpinan\PimpinanController::class,
            fn() => new \WebApi\Master\Pimpinan\PimpinanController(
                $this->make(\Application\Master\Pimpinan\Commands\Create\CreatePimpinanCommand::class),
                $this->make(\Application\Master\Pimpinan\Commands\Update\UpdatePimpinanCommand::class),
                $this->make(\Application\Master\Pimpinan\Commands\Delete\DeletePimpinanCommand::class),
                $this->make(\Application\Master\Pimpinan\Queries\GetPimpinanByListQuery::class),
                $this->make(\Application\Master\Pimpinan\Queries\GetPimpinanByIdQuery::class),
            )
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
                $this->make(\Client\Master\User\UserService::class),
                $this->make(\Client\Master\Admin\AdminService::class),
                $this->make(\Client\Master\Pelanggan\PelangganService::class),
                $this->make(\Client\Master\Pimpinan\PimpinanService::class),
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
