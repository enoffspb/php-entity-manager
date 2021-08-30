<?php

namespace enoffspb\EntityManager\Tests\Unit;

use enoffspb\EntityManager\InMemoryEntityManager;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;
use enoffspb\EntityManager\Tests\Entity\Example;
use PHPUnit\Framework\TestCase;

class InMemoryEntityManagerTest extends TestCase
{
    private EntityManagerInterface  $entityManager;

    public function setUp(): void
    {
        $this->entityManager = new InMemoryEntityManager();
    }

    public function testSaveNewEntity()
    {
        $entity = new Example();
        $entity->name = 'Test entity';

        $res = $this->entityManager->save($entity);

        $this->assertTrue($res);
        $this->assertNotNull($entity->id);
    }

}
