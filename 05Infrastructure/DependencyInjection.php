<?php

declare(strict_types=1);

namespace Infrastructure;

class DependencyInjection
{
    private array $bindings = [];
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

        // Require all needed files
        $files = [
            // Repositories
            BASE_PATH . '/07Client/Master/Golongan/GolonganService.php',
            BASE_PATH . '/07Client/Master/Jabatan/JabatanService.php',
            BASE_PATH . '/07Client/Master/User/UserService.php',
            BASE_PATH . '/07Client/Transaction/Absensi/AbsensiService.php',
            BASE_PATH . '/07Client/Transaction/Penggajian/PenggajianService.php',
            BASE_PATH . '/07Client/Transaction/Laporan/LaporanService.php',
            // Application - Golongan
            BASE_PATH . '/04Application/Master/Golongan/Commands/Create/CreateGolonganCommand.php',
            BASE_PATH . '/04Application/Master/Golongan/Commands/Update/UpdateGolonganCommand.php',
            BASE_PATH . '/04Application/Master/Golongan/Commands/Delete/DeleteGolonganCommand.php',
            BASE_PATH . '/04Application/Master/Golongan/Queries/GetGolonganByIdQuery.php',
            BASE_PATH . '/04Application/Master/Golongan/Queries/GetGolonganByListQuery.php',
            // Application - Jabatan
            BASE_PATH . '/04Application/Master/Jabatan/Commands/Create/CreateJabatanCommand.php',
            BASE_PATH . '/04Application/Master/Jabatan/Commands/Update/UpdateJabatanCommand.php',
            BASE_PATH . '/04Application/Master/Jabatan/Commands/Delete/DeleteJabatanCommand.php',
            BASE_PATH . '/04Application/Master/Jabatan/Queries/GetJabatanByIdQuery.php',
            BASE_PATH . '/04Application/Master/Jabatan/Queries/GetJabatanByListQuery.php',
            // Application - User
            BASE_PATH . '/04Application/Master/User/Commands/Create/CreateUserCommand.php',
            BASE_PATH . '/04Application/Master/User/Commands/Update/UpdateUserCommand.php',
            BASE_PATH . '/04Application/Master/User/Commands/Delete/DeleteUserCommand.php',
            BASE_PATH . '/04Application/Master/User/Queries/GetUserByIdQuery.php',
            BASE_PATH . '/04Application/Master/User/Queries/GetUserByListQuery.php',
            // Application - Absensi
            BASE_PATH . '/04Application/Transaction/Absensi/Commands/Create/CreateAbsensiCommand.php',
            BASE_PATH . '/04Application/Transaction/Absensi/Commands/Create/BulkCreateAbsensiCommand.php',
            BASE_PATH . '/04Application/Transaction/Absensi/Commands/Update/UpdateAbsensiCommand.php',
            BASE_PATH . '/04Application/Transaction/Absensi/Commands/Delete/DeleteAbsensiCommand.php',
            BASE_PATH . '/04Application/Transaction/Absensi/Queries/GetAbsensiByIdQuery.php',
            BASE_PATH . '/04Application/Transaction/Absensi/Queries/GetAbsensiByListQuery.php',
            // Application - Penggajian
            BASE_PATH . '/04Application/Transaction/Penggajian/Commands/Create/CreatePenggajianCommand.php',
            BASE_PATH . '/04Application/Transaction/Penggajian/Commands/Create/BulkCreatePenggajianCommand.php',
            BASE_PATH . '/04Application/Transaction/Penggajian/Commands/Update/UpdatePenggajianCommand.php',
            BASE_PATH . '/04Application/Transaction/Penggajian/Commands/Delete/DeletePenggajianCommand.php',
            BASE_PATH . '/04Application/Transaction/Penggajian/Queries/GetPenggajianByIdQuery.php',
            BASE_PATH . '/04Application/Transaction/Penggajian/Queries/GetPenggajianByListQuery.php',
            // Application - Laporan
            BASE_PATH . '/04Application/Transaction/Laporan/Queries/GetLaporanQuery.php',
            // Controllers
            BASE_PATH . '/06WebApi/AuthController.php',
            BASE_PATH . '/06WebApi/DashboardController.php',
            BASE_PATH . '/06WebApi/ProfileController.php',
            BASE_PATH . '/06WebApi/Master/Golongan/GolonganController.php',
            BASE_PATH . '/06WebApi/Master/Jabatan/JabatanController.php',
            BASE_PATH . '/06WebApi/Master/User/UserController.php',
            BASE_PATH . '/06WebApi/Transaction/Absensi/AbsensiController.php',
            BASE_PATH . '/06WebApi/Transaction/Penggajian/PenggajianController.php',
            BASE_PATH . '/06WebApi/Transaction/Laporan/LaporanController.php',
        ];

        foreach ($files as $file) {
            if (file_exists($file)) require_once $file;
        }

        // Register bindings
        $this->bind(\Client\Master\Golongan\GolonganService::class,     fn() => new \Client\Master\Golongan\GolonganService($db));
        $this->bind(\Client\Master\Jabatan\JabatanService::class,       fn() => new \Client\Master\Jabatan\JabatanService($db));
        $this->bind(\Client\Master\User\UserService::class,             fn() => new \Client\Master\User\UserService($db));
        $this->bind(\Client\Transaction\Absensi\AbsensiService::class,  fn() => new \Client\Transaction\Absensi\AbsensiService($db));
        $this->bind(\Client\Transaction\Penggajian\PenggajianService::class, fn() => new \Client\Transaction\Penggajian\PenggajianService($db));
        $this->bind(\Client\Transaction\Laporan\LaporanService::class,  fn() => new \Client\Transaction\Laporan\LaporanService($db));

        // Queries & Commands - Golongan
        $this->bind(\Application\Master\Golongan\Queries\GetGolonganByIdQuery::class,   fn() => new \Application\Master\Golongan\Queries\GetGolonganByIdQuery($this->make(\Client\Master\Golongan\GolonganService::class)));
        $this->bind(\Application\Master\Golongan\Queries\GetGolonganByListQuery::class, fn() => new \Application\Master\Golongan\Queries\GetGolonganByListQuery($this->make(\Client\Master\Golongan\GolonganService::class)));
        $this->bind(\Application\Master\Golongan\Commands\Create\CreateGolonganCommand::class, fn() => new \Application\Master\Golongan\Commands\Create\CreateGolonganCommand($this->make(\Client\Master\Golongan\GolonganService::class)));
        $this->bind(\Application\Master\Golongan\Commands\Update\UpdateGolonganCommand::class, fn() => new \Application\Master\Golongan\Commands\Update\UpdateGolonganCommand($this->make(\Client\Master\Golongan\GolonganService::class)));
        $this->bind(\Application\Master\Golongan\Commands\Delete\DeleteGolonganCommand::class, fn() => new \Application\Master\Golongan\Commands\Delete\DeleteGolonganCommand($this->make(\Client\Master\Golongan\GolonganService::class)));

        // Queries & Commands - Jabatan
        $this->bind(\Application\Master\Jabatan\Queries\GetJabatanByIdQuery::class,   fn() => new \Application\Master\Jabatan\Queries\GetJabatanByIdQuery($this->make(\Client\Master\Jabatan\JabatanService::class)));
        $this->bind(\Application\Master\Jabatan\Queries\GetJabatanByListQuery::class, fn() => new \Application\Master\Jabatan\Queries\GetJabatanByListQuery($this->make(\Client\Master\Jabatan\JabatanService::class)));
        $this->bind(\Application\Master\Jabatan\Commands\Create\CreateJabatanCommand::class, fn() => new \Application\Master\Jabatan\Commands\Create\CreateJabatanCommand($this->make(\Client\Master\Jabatan\JabatanService::class)));
        $this->bind(\Application\Master\Jabatan\Commands\Update\UpdateJabatanCommand::class, fn() => new \Application\Master\Jabatan\Commands\Update\UpdateJabatanCommand($this->make(\Client\Master\Jabatan\JabatanService::class)));
        $this->bind(\Application\Master\Jabatan\Commands\Delete\DeleteJabatanCommand::class, fn() => new \Application\Master\Jabatan\Commands\Delete\DeleteJabatanCommand($this->make(\Client\Master\Jabatan\JabatanService::class)));

        // Queries & Commands - User
        $this->bind(\Application\Master\User\Queries\GetUserByIdQuery::class,   fn() => new \Application\Master\User\Queries\GetUserByIdQuery($this->make(\Client\Master\User\UserService::class)));
        $this->bind(\Application\Master\User\Queries\GetUserByListQuery::class, fn() => new \Application\Master\User\Queries\GetUserByListQuery($this->make(\Client\Master\User\UserService::class)));
        $this->bind(\Application\Master\User\Commands\Create\CreateUserCommand::class, fn() => new \Application\Master\User\Commands\Create\CreateUserCommand($this->make(\Client\Master\User\UserService::class)));
        $this->bind(\Application\Master\User\Commands\Update\UpdateUserCommand::class, fn() => new \Application\Master\User\Commands\Update\UpdateUserCommand($this->make(\Client\Master\User\UserService::class)));
        $this->bind(\Application\Master\User\Commands\Delete\DeleteUserCommand::class, fn() => new \Application\Master\User\Commands\Delete\DeleteUserCommand($this->make(\Client\Master\User\UserService::class)));

        // Queries & Commands - Absensi
        $this->bind(\Application\Transaction\Absensi\Queries\GetAbsensiByIdQuery::class,   fn() => new \Application\Transaction\Absensi\Queries\GetAbsensiByIdQuery($this->make(\Client\Transaction\Absensi\AbsensiService::class)));
        $this->bind(\Application\Transaction\Absensi\Queries\GetAbsensiByListQuery::class, fn() => new \Application\Transaction\Absensi\Queries\GetAbsensiByListQuery($this->make(\Client\Transaction\Absensi\AbsensiService::class)));
        $this->bind(\Application\Transaction\Absensi\Commands\Create\CreateAbsensiCommand::class, fn() => new \Application\Transaction\Absensi\Commands\Create\CreateAbsensiCommand($this->make(\Client\Transaction\Absensi\AbsensiService::class)));
        $this->bind(\Application\Transaction\Absensi\Commands\BulkCreate\BulkCreateAbsensiCommand::class, fn() => new \Application\Transaction\Absensi\Commands\BulkCreate\BulkCreateAbsensiCommand($this->make(\Client\Transaction\Absensi\AbsensiService::class)));
        $this->bind(\Application\Transaction\Absensi\Commands\Update\UpdateAbsensiCommand::class, fn() => new \Application\Transaction\Absensi\Commands\Update\UpdateAbsensiCommand($this->make(\Client\Transaction\Absensi\AbsensiService::class)));
        $this->bind(\Application\Transaction\Absensi\Commands\Delete\DeleteAbsensiCommand::class, fn() => new \Application\Transaction\Absensi\Commands\Delete\DeleteAbsensiCommand($this->make(\Client\Transaction\Absensi\AbsensiService::class)));

        // Queries & Commands - Penggajian
        $this->bind(\Application\Transaction\Penggajian\Queries\GetPenggajianByIdQuery::class,   fn() => new \Application\Transaction\Penggajian\Queries\GetPenggajianByIdQuery($this->make(\Client\Transaction\Penggajian\PenggajianService::class)));
        $this->bind(\Application\Transaction\Penggajian\Queries\GetPenggajianByListQuery::class, fn() => new \Application\Transaction\Penggajian\Queries\GetPenggajianByListQuery($this->make(\Client\Transaction\Penggajian\PenggajianService::class)));
        $this->bind(\Application\Transaction\Penggajian\Commands\Create\CreatePenggajianCommand::class, fn() => new \Application\Transaction\Penggajian\Commands\Create\CreatePenggajianCommand($this->make(\Client\Transaction\Penggajian\PenggajianService::class), $this->make(\Client\Transaction\Absensi\AbsensiService::class)));
        $this->bind(\Application\Transaction\Penggajian\Commands\BulkCreate\BulkCreatePenggajianCommand::class, fn() => new \Application\Transaction\Penggajian\Commands\BulkCreate\BulkCreatePenggajianCommand($this->make(\Client\Transaction\Penggajian\PenggajianService::class), $this->make(\Client\Transaction\Absensi\AbsensiService::class)));
        $this->bind(\Application\Transaction\Penggajian\Commands\Update\UpdatePenggajianCommand::class, fn() => new \Application\Transaction\Penggajian\Commands\Update\UpdatePenggajianCommand($this->make(\Client\Transaction\Penggajian\PenggajianService::class)));
        $this->bind(\Application\Transaction\Penggajian\Commands\Delete\DeletePenggajianCommand::class, fn() => new \Application\Transaction\Penggajian\Commands\Delete\DeletePenggajianCommand($this->make(\Client\Transaction\Penggajian\PenggajianService::class)));

        // Laporan
        $this->bind(\Application\Transaction\Laporan\Queries\GetLaporanQuery::class, fn() => new \Application\Transaction\Laporan\Queries\GetLaporanQuery($this->make(\Client\Transaction\Laporan\LaporanService::class)));

        // Controllers
        $this->bind(\WebApi\AuthController::class, fn() => new \WebApi\AuthController($this->make(\Client\Master\User\UserService::class)));
        $this->bind(\WebApi\DashboardController::class, fn() => new \WebApi\DashboardController($this->make(\Client\Master\User\UserService::class), $this->make(\Client\Transaction\Absensi\AbsensiService::class), $this->make(\Client\Transaction\Penggajian\PenggajianService::class)));
        $this->bind(\WebApi\ProfileController::class, fn() => new \WebApi\ProfileController($this->make(\Application\Master\User\Commands\Update\UpdateUserCommand::class), $this->make(\Application\Master\User\Queries\GetUserByIdQuery::class)));

        $this->bind(\WebApi\Master\Golongan\GolonganController::class, fn() => new \WebApi\Master\Golongan\GolonganController(
            $this->make(\Application\Master\Golongan\Commands\Create\CreateGolonganCommand::class),
            $this->make(\Application\Master\Golongan\Commands\Update\UpdateGolonganCommand::class),
            $this->make(\Application\Master\Golongan\Commands\Delete\DeleteGolonganCommand::class),
            $this->make(\Application\Master\Golongan\Queries\GetGolonganByIdQuery::class),
            $this->make(\Application\Master\Golongan\Queries\GetGolonganByListQuery::class)
        ));

        $this->bind(\WebApi\Master\Jabatan\JabatanController::class, fn() => new \WebApi\Master\Jabatan\JabatanController(
            $this->make(\Application\Master\Jabatan\Commands\Create\CreateJabatanCommand::class),
            $this->make(\Application\Master\Jabatan\Commands\Update\UpdateJabatanCommand::class),
            $this->make(\Application\Master\Jabatan\Commands\Delete\DeleteJabatanCommand::class),
            $this->make(\Application\Master\Jabatan\Queries\GetJabatanByIdQuery::class),
            $this->make(\Application\Master\Jabatan\Queries\GetJabatanByListQuery::class),
            $this->make(\Application\Master\Golongan\Queries\GetGolonganByListQuery::class)
        ));

        $this->bind(\WebApi\Master\User\UserController::class, fn() => new \WebApi\Master\User\UserController(
            $this->make(\Application\Master\User\Commands\Create\CreateUserCommand::class),
            $this->make(\Application\Master\User\Commands\Update\UpdateUserCommand::class),
            $this->make(\Application\Master\User\Commands\Delete\DeleteUserCommand::class),
            $this->make(\Application\Master\User\Queries\GetUserByIdQuery::class),
            $this->make(\Application\Master\User\Queries\GetUserByListQuery::class),
            $this->make(\Application\Master\Jabatan\Queries\GetJabatanByListQuery::class)
        ));

        $this->bind(\WebApi\Transaction\Absensi\AbsensiController::class, fn() => new \WebApi\Transaction\Absensi\AbsensiController(
            $this->make(\Application\Transaction\Absensi\Commands\Create\CreateAbsensiCommand::class),
            $this->make(\Application\Transaction\Absensi\Commands\BulkCreate\BulkCreateAbsensiCommand::class),
            $this->make(\Application\Transaction\Absensi\Commands\Update\UpdateAbsensiCommand::class),
            $this->make(\Application\Transaction\Absensi\Commands\Delete\DeleteAbsensiCommand::class),
            $this->make(\Application\Transaction\Absensi\Queries\GetAbsensiByIdQuery::class),
            $this->make(\Application\Transaction\Absensi\Queries\GetAbsensiByListQuery::class),
            $this->make(\Application\Master\User\Queries\GetUserByListQuery::class)
        ));

        $this->bind(\WebApi\Transaction\Penggajian\PenggajianController::class, fn() => new \WebApi\Transaction\Penggajian\PenggajianController(
            $this->make(\Application\Transaction\Penggajian\Commands\Create\CreatePenggajianCommand::class),
            $this->make(\Application\Transaction\Penggajian\Commands\BulkCreate\BulkCreatePenggajianCommand::class),
            $this->make(\Application\Transaction\Penggajian\Commands\Update\UpdatePenggajianCommand::class),
            $this->make(\Application\Transaction\Penggajian\Commands\Delete\DeletePenggajianCommand::class),
            $this->make(\Application\Transaction\Penggajian\Queries\GetPenggajianByIdQuery::class),
            $this->make(\Application\Transaction\Penggajian\Queries\GetPenggajianByListQuery::class),
            $this->make(\Application\Master\User\Queries\GetUserByListQuery::class)
        ));

        $this->bind(\WebApi\Transaction\Laporan\LaporanController::class, fn() => new \WebApi\Transaction\Laporan\LaporanController(
            $this->make(\Application\Transaction\Laporan\Queries\GetLaporanQuery::class)
        ));
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
