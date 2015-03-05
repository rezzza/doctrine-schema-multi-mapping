Doctrine Schema Multi Mapping.
==============================

# Installation on Symfony.

Activate the bundle in `AppKernel`:

```php
    if (in_array($this->getEnvironment(), array('test'))) {
        $bundles[] = new Rezzza\DoctrineSchemaMultiMapping\App\Bundle\DoctrineSchemaMultiMappingBundle();
    }

```

# Usage

```
./app/console rezzza:doctrine-multi-mapping:schema:create --em=default  --env=test
./app/console rezzza:doctrine-multi-mapping:schema:update --em=default  --env=test
./app/console rezzza:doctrine-multi-mapping:schema:drop --em=default  --env=test
```

**You should consider this bundle has not to be used in a production environment**.

## Why are we using it ?

In Doctrine2, you cannot define many mappings for one table and use `doctrine:schema:*` commands because Schema cannot and has not to decide focal points between definitions, example:


### Definition 1 of customer in \Acme\User\Domain\User.php

In this bounded context, we need to have user login informations and some others...

```
    - id
    - username
    - password
    - first_name
    - last_name
```

### Definition 2 of customer in \Acme\User\Cart\User.php

In this bounded context, we only have to use user id and it's first name and last name ...

```
    - id
    - first_name
    - last_name
```

***This kind of application in your production environment could work with a shared database and you would have to manually define migrations***

With this example, Doctrine will have 2 times a definition for table `user` and will throw an exception.
This library will merge definitions between 1st and 2nd definition ONLY for `doctrine:schema:*` commands.

This bundle provide by this way 3 commands, to replace `doctrine:schema:*` command, you have to replace namespace by `rezzza:doctrine-multi-mapping:schema:*`.

# Not supported

At this moment, only inheritance type “none“ and “single_table“ are supported, feel free to make a PR to support others.
