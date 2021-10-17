
Unit tests
------------------------
```shell
php vendor/bin/phpunit tests/unit
```

Driver testing
------------------------
For testing a driver you should run EntityManagerTest.php and pass the following params to phpunit:
```
-- --driver DriverClass \
--dsn 'db:host=localhost;dbname=db' \
--user 'username' \
--password 'pwd'
``` 
or set it by environment variables:
```
PHP_EM_DRIVER=DriverClass
PHP_EM_DSN=db:host=localhost;dbname=db
PHP_EM_USER=username
PHP_EM_PASSWORD=pwd
```
Finally, a command should be like that:

```shell
php vendor/bin/phpunit tests/unit/EntityManagerTest.php -- \
--driver MySql\\MySqlDriver \
--dsn 'mysql:host=localhost;dbname=test_db' \
--user 'username' \
--password 'pwd'
```
