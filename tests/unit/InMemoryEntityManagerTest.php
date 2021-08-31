<?php

namespace enoffspb\EntityManager\Tests\Unit;

use enoffspb\EntityManager\Driver\InMemoryDriver;
use enoffspb\EntityManager\EntityManager;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;
use enoffspb\EntityManager\Tests\Entity\Example;
use PHPUnit\Framework\TestCase;

class InMemoryEntityManagerTest extends TestCase
{
    private static EntityManagerInterface $entityManager;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $driver = new InMemoryDriver();
        self::$entityManager = new EntityManager($driver);

        $driver->setEntityManager(self::$entityManager);
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::$entityManager;
    }

    public function testSaveNewEntity()
    {
        $entity = new Example();
        $entity->name = 'Test entity';

        $res = $this->getEntityManager()->save($entity);

        $this->assertTrue($res);
        $this->assertNotNull($entity->id);

        return $entity->id;
    }

    /**
     * @depends testSaveNewEntity
     */
    public function testGetEntity(int $newEntityId)
    {
        $repository = $this->getEntityManager()->getRepository(Example::class);

        $entity = $repository->getByPk($newEntityId);
        $this->assertInstanceOf(Example::class, $entity);

        $this->assertEquals($newEntityId, $entity->id);
    }

}
