<?php

namespace enoffspb\EntityManager\Tests\Unit;

use enoffspb\EntityManager\Driver\InMemoryDriver;
use enoffspb\EntityManager\EntityManager;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;
use enoffspb\EntityManager\Tests\Entity\Example;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected static EntityManager $entityManager;

    protected static array $entitiesConfig = [
        Example::class => [
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

    protected static function createEntityManager()
    {
        $driver = new InMemoryDriver();

        self::$entityManager = new EntityManager($driver, self::$entitiesConfig);
    }

    protected function getEntityManager(): EntityManager
    {
        return self::$entityManager;
    }
}
