<?php

namespace EnoffSpb\EntityManager\Tests\Unit;

use EnoffSpb\EntityManager\Interfaces\DriverInterface;
use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;
use EnoffSpb\EntityManager\Tests\Entity\Example;

class EntityManagerTest extends BaseTest
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::createEntityManager();
    }

    public function testGetDriver()
    {
        $this->assertInstanceOf(DriverInterface::class, $this->getEntityManager()->getDriver());
    }

    public function testSaveNewEntity()
    {
        $entity = new Example();
        $entity->name = 'Test entity';

        $res = $this->getEntityManager()->save($entity);

        $this->assertTrue($res);
        $this->assertNotNull($entity->getId());

        return $entity->getId();
    }

    public function testGetRepository()
    {
        $repository = $this->getEntityManager()->getRepository(Example::class);
        $this->assertInstanceOf(RepositoryInterface::class, $repository);
//        $entity = $repository->getById(1); $entity->
    }

    /**
     * @depends testSaveNewEntity
     */
    public function testGetEntity(int $newEntityId)
    {
        $repository = $this->getEntityManager()->getRepository(Example::class);

        $entity = $repository->getById($newEntityId);
        $this->assertInstanceOf(Example::class, $entity);

        $this->assertEquals($newEntityId, $entity->getId());

        $repository->detach($entity);

        $otherInstanceOfEntity = $repository->getById($newEntityId);
        $this->assertEquals($entity->getId(), $otherInstanceOfEntity->getId());

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

        $entityId = $entity->getId();

        $repo = $this->getEntityManager()->getRepository(Example::class);
        $repo->detach($entity);

        $entity = $repo->getById($entityId);
        $this->assertEquals($entity->name, 'New name');

        return $entity;
    }

    /**
     * @depends testUpdateEntity
     */
    public function testDeleteEntity(Example $entity)
    {
        $entityId = $entity->getId();

        $this->getEntityManager()->delete($entity);
        unset($entity);

        $entity = $this->getEntityManager()->getRepository(Example::class)->getById($entityId);

        $this->assertNull($entity);
    }
}
