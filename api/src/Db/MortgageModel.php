<?php

namespace Calculator\Db;

use Exception;
use PDO;
use PDOException;

class MortgageModel
{
    public const TYPE_USUAL = 0;
    public const TYPE_FAMILY = 1;
    public const TYPE_MILITARY = 2;
    private static ?PDO $connection = null;

    public function __construct()
    {
    }

    /**
     * @return array|null
     * @throws PDOException
     */
    public static function getTypes(): ?array
    {
        $query = self::getInstance()->query("SELECT * FROM types");
        if (!$query->execute()) {
            throw new Exception(json_encode($query->errorInfo()));
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return PDO
     * @throws PDOException
     */
    public static function getInstance(): PDO
    {
        if (self::$connection === null) {
            return self::$connection = new PDO($_ENV['HOST']);
        }
        return self::$connection;
    }

    /**
     * @param int $typeId
     *
     * @return array|null
     * @throws Exception
     */
    public static function getIntertypes(int $typeId): array
    {
        $query = self::getInstance()->query("SELECT  intertype FROM options WHERE  type_id=:typeId ");
        if (!$query->execute([':typeId' => $typeId])) {
            throw new Exception(json_encode($query->errorInfo()));
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $typeId
     * @param int $interType
     *
     * @return array
     * @throws Exception
     */
    public static function getOption(int $typeId, int $interType = 0): array
    {
        $query = self::getInstance()->prepare(
            'SELECT * FROM options WHERE intertype=:intertype AND type_id=:typeid'
        );
        if (!$query->execute([':intertype' => $interType, ':typeid' => $typeId])) {
            throw new Exception($query->errorInfo());
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}