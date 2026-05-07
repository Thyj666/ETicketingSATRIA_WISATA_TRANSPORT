<?php

declare(strict_types=1);

namespace Infrastructure;

class AppDbContext
{
    private static ?self $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $host   = getenv('DB_HOST')   ?: 'localhost';
        $port   = getenv('DB_PORT')   ?: '3306';
        $dbname = getenv('DB_NAME')   ?: 'test_auto_migration';
        $user   = getenv('DB_USER')   ?: 'root';
        $pass   = getenv('DB_PASS')   ?: '';

        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";

        try {
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$dbname}`");

            $this->pdo = $pdo;
        } catch (\PDOException $e) {
            die('<div style="font-family:monospace;background:#fee;padding:20px;border:2px solid red;margin:20px">
                <h2>Database Connection Error</h2>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Pastikan MySQL/MariaDB berjalan dan konfigurasi database sudah benar.</p>
            </div>');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            // Ubah jadi DomainException agar bisa ditangkap layer atas
            throw new \DomainException($this->friendlyError($e), (int)$e->getCode(), $e);
        }
    }

    private function friendlyError(\PDOException $e): string
    {
        $code = $e->errorInfo[1] ?? 0;

        return match ((int)$code) {
            1062 => 'Data sudah ada, tidak boleh duplikat.',
            1452 => 'Data referensi tidak ditemukan.',
            1451 => 'Data tidak bisa dihapus karena masih digunakan.',
            default => 'Terjadi kesalahan pada database: ' . $e->getMessage(),
        };
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row ?: null;
    }

    public function execute(string $sql, array $params = []): bool
    {
        return $this->query($sql, $params)->rowCount() > 0;
    }

    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }
    public function commit(): void
    {
        $this->pdo->commit();
    }
    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
