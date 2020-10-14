# Migrations Bundle
Symfony framework integration for davedevelopment/phpmig

## Setup
```php
# config/bundles.php

return [
    DosFarma\MigrationsBundle\DosFarmaMigrationsBundle::class => ['dev' => true, 'test' => true],
];
```
```yaml
# services.yaml

parameters:
  dos_farma.migrations.migrations_directory: '%kernel.project_dir%/migrations/postgresql/'
  dos_farma.migrations.migration_template: '%kernel.project_dir%/vendor/dosfarma/migrations-bundle/src/Resources/templates/dbalSql.php.twig'
  dos_farma.migrations.control_table: 'serviceschema.migrations'

services:
  DosFarma\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter\Adapter:
    class: DosFarma\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter\DbalAdapter
    public: false
    autoconfigure: true
    arguments:
      $connection: '@connection.dbal.myservice' # Doctrine DBAL Connection
      $tableName: '%dos_farma.migrations.control_table%'

  DosFarma\MigrationsBundle\Infrastructure\Service\Phpmig\ConfigurationContainer:
    public: true
    class: DosFarma\MigrationsBundle\Infrastructure\Service\Phpmig\ConfigurationContainer
    autoconfigure: true
    factory: DosFarma\MigrationsBundle\Infrastructure\Service\Phpmig\ConfigurationContainer::from
    arguments:
      $adapter: '@DosFarma\MigrationsBundle\Infrastructure\Service\Phpmig\Adapter\Adapter'
      $directory: '%dos_farma.migrations.migrations_directory%'
      $template: '%dos_farma.migrations.migration_template%'
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
