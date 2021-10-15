<?php

namespace enoffspb\EntityManager\Tests\Unit;

use enoffspb\EntityManager\Interfaces\RepositoryInterface;
use enoffspb\EntityManager\Repository\InMemoryGenericRepository;
use enoffspb\EntityManager\Tests\Entity\Example;

class RepositoryTest extends BaseTest
{
    private static ?RepositoryInterface $repository = null;

    private static array $entitiesData = [
        ['name' => '1st entity'],
        ['name' => '2nd entity'],
        ['name' => '3rd entity'],
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::createEntityManager();

        foreach(self::$entitiesData as $entityData) {
            $entity = new Example();
            foreach($entityData as $k => $v) {
                $entity->$k = $v;
            }
            self::$entityManager->save($entity);
        }
    }

    private function getRepository(): RepositoryInterface
    {
        if(self::$repository === null) {
            $metadata = $this->getEntityManager()->getDriver()->createMetadata(Example::class, self::$entitiesConfig[Example::class]);
            self::$repository = new InMemoryGenericRepository($metadata, $this->getEntityManager()->getDriver());
        }

        return self::$repository;
    }

    public function testGetById()
    {
        $repository = $this->getRepository();
        $entity = $repository->getById(1);
        $this->assertNotNull($entity);
        $this->assertInstanceOf(Example::class, $entity);
    }

    public function testGetList()
    {
        $repository = $this->getRepository();

        $allEntities = $repository->getList([/* empty criteria */]);
        $this->assertCount(count(self::$entitiesData), $allEntities);

        $entities = $repository->getList([
            'name' => '1st entity'
        ]);

        $this->assertCount(1, $entities);

        /**
         * @var Example $entity
         */
        $entity = $entities[0];
        $this->assertEquals('1st entity', $entity->name);
    }
}
