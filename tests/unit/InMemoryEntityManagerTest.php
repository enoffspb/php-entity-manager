<?php

namespace enoffspb\EntityManager\Tests\Unit;

use enoffspb\EntityManager\Driver\InMemoryDriver;
use enoffspb\EntityManager\EntityManager;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;
use enoffspb\EntityManager\Tests\Entity\Example;
use PHPUnit\Framework\TestCase;

class InMemoryEntityManagerTest extends TestCase
{
    private EntityManagerInterface  $entityManager;

    public function setUp(): void
    {
        $driver = new InMemoryDriver();
        $this->entityManager = new EntityManager($driver);
    }

    public function testSaveNewEntity()
    {
        $entity = new Example();
        $entity->name = 'Test entity';

        $res = $this->entityManager->save($entity);

        $this->assertTrue($res);
        $this->assertNotNull($entity->id);

        return $entity->id;
    }

    /**
     * @depends testSaveNewEntity
     */
    public function testGetEntity(int $newEntityId)
    {
        $repository = $this->entityManager->getRepository(Example::class);

        $entity = $repository->getByPk($newEntityId);
        $this->assertInstanceOf(Example::class, $entity);

        $this->assertEquals($newEntityId, $entity->id);
    }

}
