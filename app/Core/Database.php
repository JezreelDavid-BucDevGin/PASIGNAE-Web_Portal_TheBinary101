<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(array $config): void
    {
        if (self::$connection !== null) {
            return;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            self::$connection = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            if (config('debug')) {
                throw new PDOException('Database connection failed: ' . $e->getMessage());
            }
            throw new PDOException('Database connection failed. Please check your configuration.');
        }
    }

    public static function pdo(): PDO
    {
        if (self::$connection === null) {
            throw new PDOException('Database not connected.');
        }
        return self::$connection;
    }

    public static function beginTransaction(): void
    {
        self::pdo()->beginTransaction();
    }

    public static function commit(): void
    {
        self::pdo()->commit();
    }

    public static function rollBack(): void
    {
        if (self::pdo()->inTransaction()) {
            self::pdo()->rollBack();
        }
    }
}
