<?php

class database {

    private static ?PDO $pdo = null;
    private static $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    public static function connect(): PDO {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                        'mysql:host=sql105.infinityfree.com;dbname=if0_41133576_market_trader;charset=utf8mb4',
                        'if0_41133576',
                        'FreeTradePass66',
                        self::$options
                );
            } catch (Exception $ex) {
                throw new \PDOException($ex->getMessage(), (int) $ex->getCode());
            }
        }

        return self::$pdo;
    }

    public static function fetch($statement, $array): mixed {
	$pdo = self::connect();

	$stmt = $pdo->prepare($statement);
	$stmt->execute($array);

	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	return $result;
    }

    public static function fetchall($statement, $array): mixed {
	$pdo = self::connect();

	$stmt = $pdo->prepare($statement);
	$stmt->execute($array);

	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $result;
    }

    public static function execute($statement): bool {
	$pdo = self::connect();
	$stmt = $pdo->prepare($statement);
	return $stmt->execute();
    }

    public static function execute_arr($statement, $array): bool {
	$pdo = self::connect();
	$stmt = $pdo->prepare($statement);

	if (!$stmt->execute($array)) {
	    var_dump($stmt->errorInfo());
	}

	return true;
    }

    public static function beginTransaction(): void {
	$pdo = self::connect();
	$pdo->beginTransaction();
    }

    public static function commit(): void {
	$pdo = self::connect();
	$pdo->commit();
    }

    public static function rollback(): void {
	$pdo = self::connect();
	$pdo->rollBack();
    }
}
