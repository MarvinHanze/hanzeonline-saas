<?php
declare(strict_types=1);

namespace Core;

class Database
{
    private static ?\PDO $pdo = null;
    private static ?int $tenantId = null;

    public static function connect(): \PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
            self::$pdo = new \PDO($dsn, $config['user'], $config['pass'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }

    public static function setTenant(int $tenantId): void
    {
        self::$tenantId = $tenantId;
    }

    public static function getTenantId(): ?int
    {
        return self::$tenantId;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        return self::query($sql, $params)->fetch() ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        self::query($sql, array_values($data));
        return (int) self::connect()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): void
    {
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $sql = "UPDATE $table SET $set WHERE $where";
        self::query($sql, array_merge(array_values($data), $whereParams));
    }

    public static function delete(string $table, string $where, array $params = []): void
    {
        self::query("DELETE FROM $table WHERE $where", $params);
    }

    public static function count(string $table, string $where = '1=1', array $params = []): int
    {
        return (int) self::fetch("SELECT COUNT(*) as c FROM $table WHERE $where", $params)['c'];
    }

    public static function initSchema(): void
    {
        $pdo = self::connect();

        $pdo->exec("CREATE TABLE IF NOT EXISTS tenants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            plan ENUM('starter','professional','enterprise','custom') DEFAULT 'starter',
            settings JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            role ENUM('owner','admin','user') DEFAULT 'user',
            avatar VARCHAR(500),
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_email_tenant (email, tenant_id),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            plan VARCHAR(50) NOT NULL,
            status ENUM('active','trialing','past_due','cancelled') DEFAULT 'active',
            started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NULL,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
}
