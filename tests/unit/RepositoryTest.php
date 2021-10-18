<?php

namespace EnoffSpb\EntityManager\Tests\Unit;

use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;
use EnoffSpb\EntityManager\Driver\InMemory\InMemoryGenericRepository;
use EnoffSpb\EntityManager\Tests\Entity\Example;

/**
 * @TODO
 */
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
            $repositoryClass = $this->getEntityManager()->getDriver()->getGenericRepositoryClass();
            self::$repository = new $repositoryClass($metadata, $this->getEntityManager()->getDriver());
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

    public function testGetNonExistsEntity()
    {
        $repository = $this->getRepository();
        $entity = $repository->getById(-1);
        $this->assertNull($entity);
    }

    public function testGetList()
    {
        $repository = $this->getRepository();

        $entities = $repository->getList([
            'name' => '1st entity'
        ]);

        $this->assertGreaterThan(0, $entities);

        /**
         * @var Example $entity
         */
        $entity = $entities[0];
        $this->assertEquals('1st entity', $entity->name);
    }
}
