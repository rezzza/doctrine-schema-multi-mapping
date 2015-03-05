Doctrine Schema Multi Mapping
=============================

# Installation on Symfony.

Activate the bundle in `AppKernel`:

```php
    if (in_array($this->getEnvironment(), array('test'))) {
        $bundles[] = new Rezzza\DoctrineSchemaMultiMapping\App\Bundle\DoctrineSchemaMultiMappingBundle();
    }

```

# Usage

**You should consider this bundle has not to be used in a production environment**.

## Why are we using it ?

In Doctrine2, if you define multiple mappings for the same entity then `doctrine:schema:*` commands will fail because it will try to create 2 SQL tables with the same name instead of combining all entities mappings representing the same entity from various points of view.

## Example: Acme e-commerce application with many User entities

We want to express a User model in different bounded contexts. The main

### Definition of User entity in Account bounded context

We have a \Acme\User\Account\User.php entity.

In this bounded context, we need to manage User authentication & signin so a (simple) mapping could be:

```
    - id
    - username
    - password
    - first_name
    - last_name
```

### Definition of User entity Cart bounded context

 We have a \Acme\User\Cart\User.php entity.

In this bounded context, we don't have to bother with autentication concerns, we just need to know user's e-commerce relative informations.

```
    - id
    - first_name
    - last_name
    - is_first_order
```

***This kind of application in your production environment could work with a shared database and you would have to manually define migrations***

With this example, Doctrine will have 2 times a definition for table `user` and will throw an exception.

**This library merge the 2 entities mappings ONLY for `doctrine:schema:*` commands**.

This bundle provide by this way 3 commands, to replace `doctrine:schema:*` command, you have to replace namespace by `rezzza:doctrine-multi-mapping:schema:*`.

# Which doctrine mapping features are not supported?

At the moment, only inheritance type “none“ and “single_table“ are supported, feel free to make a PR to support others.
