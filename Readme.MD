EntityManager lib
==============
EntityManager is used to as a simple, independent data-persist layer for working with
tiny entities without its associations to the persistence layer. 
It is useful for OOAD and allows to distinguish a problem domain from a technical one.

A public interface for using EntityManager:
```php
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
    public function getList($criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
}
```

Example:
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

Configuration
------------------------


Tests
------------------------
```shell
php vendor/bin/phpunit tests
```
