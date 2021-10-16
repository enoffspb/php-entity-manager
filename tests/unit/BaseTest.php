<?php

namespace EnoffSpb\EntityManager\Tests\Unit;

use EnoffSpb\EntityManager\Driver\InMemoryDriver;
use EnoffSpb\EntityManager\EntityManager;
use EnoffSpb\EntityManager\Interfaces\EntityManagerInterface;
use EnoffSpb\EntityManager\Tests\Entity\Example;
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
