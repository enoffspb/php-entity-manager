<?php

namespace enoffspb\EntityManager\Tests\Unit;

use enoffspb\EntityManager\Driver\InMemoryDriver;
use enoffspb\EntityManager\EntityManager;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;
use enoffspb\EntityManager\Tests\Entity\Example;

use PHPUnit\Framework\TestCase;

class EntityManagerTest extends TestCase
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

        $repository->detach($entity);

        $otherInstanceOfEntity = $repository->getByPk($newEntityId);
        $this->assertEquals($entity->id, $otherInstanceOfEntity->id);

        $this->assertEquals('Test entity', $otherInstanceOfEntity->name);

        return $otherInstanceOfEntity;
    }

    /**
     * @depends testGetEntity
     */
    public function testUpdateEntity(Example $entity)
    {
        $entity->name = 'New name';

        $res = $this->getEntityManager()->update($entity);
        $this->assertTrue($res);

        $entityId = $entity->id;

        $repo = $this->getEntityManager()->getRepository(Example::class);
        $repo->detach($entity);

        $entity = $repo->getByPk($entityId);
        $this->assertEquals($entity->name, 'New name');
    }

}
