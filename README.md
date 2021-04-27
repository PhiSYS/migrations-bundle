# Migrations Bundle
Symfony framework integration for davedevelopment/phpmig

## Setup
```php
# config/bundles.php

return [
    PhiSYS\MigrationsBundle\PhiSYSMigrationsBundle::class => ['dev' => true, 'test' => true],
];
```
```yaml
# services.yaml

parameters:
  phisys.migrations.migrations_directory: '%kernel.project_dir%/migrations/postgresql/'
  phisys.migrations.migration_template: '%kernel.project_dir%/vendor/phisys/migrations-bundle/src/Resources/templates/dbalSql.php.twig'
  phisys.migrations.control_table: 'serviceschema.migrations'

services:
  PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter\Adapter:
    class: PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter\DbalAdapter
    public: false
    autoconfigure: true
    arguments:
      $connection: '@connection.dbal.myservice' # Doctrine DBAL Connection
      $tableName: '%phisys.migrations.control_table%'

  PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\ConfigurationContainer:
    public: true
    class: PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\ConfigurationContainer
    autoconfigure: true
    factory: PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\ConfigurationContainer::from
    arguments:
      $adapter: '@PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter\Adapter'
      $directory: '%phisys.migrations.migrations_directory%'
      $template: '%phisys.migrations.migration_template%'
```

## Usage
- Initialize the control table (needed just once):
  ```
  $ console migrations:init
  ```
- View current migrations status:
  ```
  $ console migrations:status
  ```
  To use in batch scripts requiring non-zero return values on error, use:
  ```
  $ console migrations:check
  ```
- Generate a new migration from template:
  ```
  $ console migrations:generate ThisIsTheMigrationSubject
  ```
- Do pending migrations:
  ```
  $ console migrations:migrate
  ```
  To do migrations up to (stop in) specified migration id:
  ```
  $ console migrations:migrate --target 20201014114643
  ```
- Rollback the last migration:
  ```
  $ console migrations:rollback
  ```
  To rollback down to specified migration id, use:
  ```
  $ console migrations:rollback --target 20201014114643
  ```
- Up a migration id:
  ```
  $ console migrations:up 20201014114643
  ```
- Down a migration id:
  ```
  $ console migrations:down 20201014114643
  ```
  To Re-Do (down and up) an already migrated id, you may use a single command:
  ```
  $ console migrations:redo 20201014114643
  ```
