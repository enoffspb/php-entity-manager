<?php

namespace enoffspb\EntityManager\Tests\Unit;

use enoffspb\EntityManager\Driver\InMemoryDriver;
use enoffspb\EntityManager\EntityManager;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;
use enoffspb\EntityManager\Interfaces\RepositoryInterface;
use enoffspb\EntityManager\Repository\InMemoryGenericRepository;
use enoffspb\EntityManager\Tests\Entity\Example;
use PHPStan\Testing\TestCase;

class RepositoryTest extends BaseTest
{
    private static ?RepositoryInterface $repository = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::createEntityManager();

        $data = [
            ['name' => '1st entity'],
            ['name' => '2nd entity'],
            ['name' => '3rd entity'],
        ];

        foreach($data as $entityData) {
            $entity = new Example();
            foreach($data as $k => $v) {
                $entity->$k = $v;
            }
            self::$entityManager->save($entity);
        }
    }

    private function getRepository(): RepositoryInterface
    {
        if(self::$repository === null) {
            $metadata = $this->getEntityManager()->getDriver()->createMetadata(Example::class, []);
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

        $entities = $repository->getList([
            /** empty criteria */
        ]);

        $this->assertNotEmpty($entities);
    }
}
