# Mongo Schema Dumper for Laravel

Import and export mongodb schema without copying all the data:
* Collections
* Indexes
* Cap sizes

Also you can dump all data from required collections.

This primary use case is when you have developed an application that uses mongodb, and want to setup a new instance with database layout.
You can run the console command and create json file with you databases schema looks like something this:
```
{
    "data": {
        "products": {
            "indexes": [
                {
                    "name": "_id_",
                    "ns": "data.products",
                    "key": {
                        "_id": 1
                    },
                    "v": 2
                }
            ],
            "options": []
        },
        "users: {
            "indexes": [
                {
                    "name": "_id_",
                    "ns": "data.users",
                    "key": {
                        "_id": 1
                    },
                    "v": 2
                }
            ],
            "options": [],
            "data": [
                {
                    "_id": "5858d847e632ed712a9f5c04",
                    "name": "admin"
                },
                {
                    "_id": "5858d872e632ed712a9f5c05",
                    "name": "manager"
                }
            ]
        }
    }
}
```

After that you can run the console commands to import the schema into the database.

## Install
Require this package with composer using the following command:
```
$ composer require pmurkin/mongo-schema-dumper
```
After updating composer, add the service provider to the providers array in config/app.php
```
Pmurkin\MongoSchemaDumper\MongoSchemaDumperServiceProvider::class,
```

## Usage

Export schema to file:
```
$ php artisan schema:export --databases=data,users --dump=users.roles --file=./schema.json
```
databases - list of databases for export

dump - list of collections for dump

file - file for save (not required, by default - ./schema.json)

Import schema from file:
```
$ php artisan schema:import --file=./schema.json
```

file - file for save (not required, by default - ./schema.json)