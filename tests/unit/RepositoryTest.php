<?php

namespace EnoffSpb\EntityManager\Tests\Unit;

use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;
use EnoffSpb\EntityManager\Tests\Entity\Example;

/**
 * @TODO
 */
class RepositoryTest extends BaseTest
{
    private static ?RepositoryInterface $repository = null;

    private static array $entitiesData = [
        ['name' => '1st entity', 'setOrder' => 1],
        ['name' => '2nd entity', 'setOrder' => 2],
        ['name' => '3rd entity', 'setOrder' => 3],
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::createEntityManager();

        foreach(self::$entitiesData as $entityData) {
            $entity = new Example();
            foreach($entityData as $k => $v) {
                if(method_exists($entity, $k)) {
                    $entity->$k($v);
                } else {
                    $entity->$k = $v;
                }
            }
            self::$entityManager->save($entity);
        }
    }

    /**
     * @return RepositoryInterface<Example>
     */
    private function getRepository(): RepositoryInterface
    {
        if(self::$repository === null) {
            $metadata = $this->getEntityManager()->getDriver()->createMetadata(Example::class, self::$entitiesConfig[Example::class]);
            $repositoryClass = $this->getEntityManager()->getDriver()->getGenericRepositoryClass();
            self::$repository = new $repositoryClass($metadata, $this->getEntityManager()->getDriver());
        }

        return self::$repository;
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

        $repository->detach($entity);

        return $entity->getId();
    }

    /**
     * @depends testGetList
     */
    public function testGetById(int $id)
    {
        $repository = $this->getRepository();
        $entity = $repository->getById($id);
        $this->assertNotNull($entity);
        $this->assertInstanceOf(Example::class, $entity);

        $sameInstance = $repository->getById($id);
        $this->assertSame($entity, $sameInstance);
    }

    public function testGetNonExistsEntity()
    {
        $repository = $this->getRepository();
        $entity = $repository->getById(-1);
        $this->assertNull($entity);
    }

    public function testOrderAndLimit()
    {
        $repository = $this->getRepository();

        $descBatch = $repository->getList([], [
            'order' => SORT_DESC
        ], 2);

        $this->assertLessThan(3, count($descBatch));

        $ascBatch = $repository->getList([], [
            'order' => SORT_ASC
        ]);

        $this->assertNotEmpty($descBatch);
        $this->assertNotEmpty($ascBatch);

        $firstEntity = $ascBatch[0];
        $lastEntity = $descBatch[0];

        $this->assertGreaterThan($firstEntity->getOrder(), $lastEntity->getOrder());

        $this->assertCount(2, $descBatch);
    }

    public function testOffset()
    {
        $repository = $this->getRepository();

        $firstBatch = $repository->getList([], [
            'id' => SORT_DESC
        ], 2, 0);

        $this->assertLessThan(3, count($firstBatch));

        $secondBatch = $repository->getList([], [
            'id' => SORT_DESC
        ], 2, 1);

        $this->assertLessThan(3, count($secondBatch));

        $this->assertEquals($firstBatch[1]->getId(), $secondBatch[0]->getId());
    }

    public function testGetCachedEntities()
    {
        $repository = $this->getRepository();

        $list1 = $repository->getList([], [
            'id' => SORT_DESC
        ], 1);
        $this->assertNotEmpty($list1);

        $firstEntity = $list1[0];

        $list2 = $repository->getList([
            'id' => $firstEntity->getId()
        ], null, 1);
        $this->assertNotEmpty($list2);

        $secondEntity = $list2[0];

        $this->assertSame($firstEntity, $secondEntity);
    }
}
