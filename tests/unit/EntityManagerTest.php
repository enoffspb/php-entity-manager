<?php

namespace enoffspb\EntityManager\Tests\Unit;

use enoffspb\EntityManager\Interfaces\DriverInterface;
use enoffspb\EntityManager\Tests\Entity\Example;

class EntityManagerTest extends BaseTest
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::createEntityManager();

        $entityManager = self::getEntityManager();
        $entitiesConfig = [
            Example::class => [
                'mapping' => [
                    'id' => ['getId', 'setId'],
                    'custom' => ['getCustom', 'setCustom']
                ]
            ]
        ];
        $entityManager->setEntitiesConfig($entitiesConfig);
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

    /**
     * @depends testSaveNewEntity
     */
    public function testGetEntity(int $newEntityId)
    {
        $repository = $this->getEntityManager()->getRepository(Example::class);

        $entity = $repository->getById($newEntityId);
        $this->assertInstanceOf(Example::class, $entity);

        $this->assertEquals($newEntityId, $entity->id);

        $repository->detach($entity);

        $otherInstanceOfEntity = $repository->getById($newEntityId);
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

        $entity = $repo->getById($entityId);
        $this->assertEquals($entity->name, 'New name');

        return $entity;
    }

    /**
     * @depends testUpdateEntity
     */
    public function testDeleteEntity(Example $entity)
    {
        $entityId = $entity->id;

        $this->getEntityManager()->delete($entity);
        unset($entity);

        $entity = $this->getEntityManager()->getRepository(Example::class)->getById($entityId);

        $this->assertNull($entity);
    }
}
