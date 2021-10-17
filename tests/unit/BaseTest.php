<?php

namespace EnoffSpb\EntityManager\Tests\Unit;

use EnoffSpb\EntityManager\Driver\InMemory\InMemoryDriver;
use EnoffSpb\EntityManager\EntityManager;
use EnoffSpb\EntityManager\Interfaces\DriverInterface;
use EnoffSpb\EntityManager\Tests\Entity\Example;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected static EntityManager $entityManager;
    protected static DriverInterface $driver;

    protected static ?string $driverName = null;
    protected static ?string $dsn = null;
    protected static ?string $user = null;
    protected static ?string $password = null;

    protected static array $entitiesConfig = [
        Example::class => [
            'tableName' => 'example',
            'mapping' => [
                'id' => [
                    'getter' => 'getId',
                    'setter' => 'setId'
                ],
                'name' => [],
                'custom' => [
                    'getter' => 'getCustom',
                    'setter' => 'setCustom'
                ]
            ]
        ]
    ];

    public static function setUpBeforeClass(): void
    {
        global $argv;

        $driverOptKey = array_search('--driver', $argv);
        $driverName = null;
        if($driverOptKey !== false) {
            $driverName = $argv[$driverOptKey + 1] ?? null;
        }

        if($driverName !== null) {
            self::$driverName = $driverName;
        } else if(($envDriverName = getenv('PHP_EM_DRIVER')) !== false) {
            self::$driverName = $envDriverName;
        }

        $dsnOptKey = array_search('--dsn', $argv);
        $dsn = null;
        if($dsnOptKey !== false) {
            $dsn = $argv[$dsnOptKey + 1] ?? null;
        }
        if($dsn !== null) {
            self::$dsn = $dsn;
        } else if(($envDsn = getenv('PHP_EM_DSN')) !== false) {
            self::$dsn = $envDsn;
        }

        $userOptKey = array_search('--user', $argv);
        $user = null;
        if($userOptKey !== false) {
            $user = $argv[$userOptKey + 1] ?? null;
        }
        if($user !== null) {
            self::$user = $user;
        } else if(($envUser = getenv('PHP_EM_USER')) !== false) {
            self::$user = $envUser;
        }

        $passwordOptKey = array_search('--password', $argv);
        $password = null;
        if($passwordOptKey !== false) {
            $password = $argv[$passwordOptKey + 1] ?? null;
        }
        if($password !== null) {
            self::$password = $password;
        } else if(($envPassword = getenv('PHP_EM_PASSWORD')) !== false) {
            self::$password = $envPassword;
        }
    }

    protected static function createEntityManager()
    {
        $driver = self::createDriver();

        self::$entityManager = new EntityManager($driver, self::$entitiesConfig);
    }

    protected static function createDriver(): DriverInterface
    {
        $driver = null;
        $driverName = self::$driverName;
        if($driverName === null) {
            $driver = new InMemoryDriver();
        } else {
            $driverClass = self::$driverName;

            $isNamespaceFromRoot = substr($driverName,0, 1) === '\\';
            if(!$isNamespaceFromRoot) {
                $rootNamespace = '\\EnoffSpb\\EntityManager\\Driver';
                $driverClass = $rootNamespace . '\\' . $driverClass;
            }

            $driver = new $driverClass(self::$dsn, self::$user, self::$password);
        }

        return $driver;
    }

    protected function getEntityManager(): EntityManager
    {
        return self::$entityManager;
    }
}
