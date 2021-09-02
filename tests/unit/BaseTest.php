<?php

namespace enoffspb\EntityManager\Tests\Unit;

use enoffspb\EntityManager\Driver\InMemoryDriver;
use enoffspb\EntityManager\EntityManager;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected static EntityManagerInterface $entityManager;

    protected static function createEntityManager()
    {
        $driver = new InMemoryDriver();
        self::$entityManager = new EntityManager($driver);

        $driver->setEntityManager(self::$entityManager);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$entityManager;
    }
}
