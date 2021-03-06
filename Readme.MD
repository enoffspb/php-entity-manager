Entity Manager for PHP
==============

![Testing](https://github.com/enoffspb/php-entity-manager/actions/workflows/testing.yml/badge.svg)
![PHPStan Level 7](https://img.shields.io/badge/PHPStan-level%207-brightgreen)
![Docs](https://enoffspb.github.io/images/docs-badge.svg)

```
! WORK IN PROGRESS !
```
EntityManager is a PHP library is used to as a simple, independent data persistence layer for working with
tiny entities without its associations to the persistence layer. 
It is useful for OOAD and allows to distinguish a problem domain from a technical one.

Drivers are implemented for **MySql**, **PostgreSql**, **InMemory** (for auto testing);

A public interface for using EntityManager:
```php
namespace EnoffSpb\EntityManager\Interfaces;

interface EntityManagerInterface
{
    public function save(object $entity): bool;
    public function update(object $entity): bool;
    public function delete(object $entity): bool;

    public function getRepository(string $entityClass): RepositoryInterface;

    public function getDriver(): DriverInterface;
}

interface RepositoryInterface
{
    public function getById($id): ?object;
    public function getList(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
}
```

An overview of usage:
```php
$entityManager = new EntityManager(/** pass the params */);
/** Configure $entityManager as described later */

$myEntity = new MyEntity(); // Any custom class
$myEntity->name = 'name';
$myEntity->setAnotherField('value');

$entityManager->save($myEntity); // save an entity

// If MyEntity has auto-generated primary key it will be available in PK field after saving
$entityId = $myEntity->getId();
// or $myEntity->id or $myEntity->customPk or otherwise else depends on an configuration 

$myEntity->name = 'New name';
$entityManager->update($myEntity); // update an exists entity

$entityManager->delete($myEntity); // delete an exists entity

// using Repository
$repository = $entityManager->getRepository(MyEntity::class);

$existsEntity = $repository->getById(1);
$list = $repository->getList([
    'name' => 'search value'
]);
foreach($list as $entity) {
    // $entity is an instance of MyEntity
}
```

Creating instance of EntityManager
------------------------

Create a driver and an entities config and pass it to EntityManager. 
```php
use EnoffSpb\EntityManager\EntityManager;
use EnoffSpb\EntityManager\Driver\MySql\MysqlDriver;

// Create a driver for DB, e.g. MySqlDriver
$driver = new MySqlDriver($dsn = 'mysql:host=localhost;dbname=db', $user = 'user', $password = 'pwd');

// Describe an entities config (see full format below) 
$entitiesConfig = [
    MyEntity::class => [/** config is here */],
    AnotherEntity::class => []
];

// Create an instance of EntityManager
$entityManager = new EntityManager($driver, $entitiesConfig);
```

An entities config:
```php
$entitiesConfig = [
    MyEntity::class => [
        'primaryKey' => 'id', // by default
        'tableName' => 'table_name',
        'repositoryClass' => MyEntityRepository::class,
        'mapping' => [ // a list of columns
            'columnName' => [
                'attribute' => 'attrName',
                'getter' => 'getterName',
                'setter' => 'setterName',
                'type' => Column::TYPE_VARCHAR,
                'length' => '',
                'nullable' => '',
            ],
            // another columns are here
        ]
    ],
    AnotherEntity::class => [
        /* An AnotherEntity config is here */ 
    ]
];
```
