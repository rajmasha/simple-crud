# simple-crud
A simple Laravel package for generating CRUD for your Laravel Applications.

You can easily generate model, migration, controller and views(index, create, edit, show) using a single command or generate them individually using separate commands.


## Installation

Require this package in composer.json of your Laravel project:

```
composer require rajmasha/simple-crud
```

Once installed, publish the assets using `vendor:publish` artisan command:

```
php artisan vendor:publish --provider="Rajmasha\SimpleCrud\SimpleCrudServiceProvider"
```


## Commands

There are 5 commands available in the package:

|Command   |Syntax         |Description                                   |
|----------|---------------|----------------------------------------------|
|Model     |crud:model     |Creates a Model                               |
|Migration |crud:migration |Creates a Migration                           |
|Controller|crud:controller|Creates a Controller                          |
|Views     |crud:view      |Creates all views                             |
|All       |crud:all       |Creates Model, Migration, Controller and Views|


### Model Command

```
php artisan crud:model {Model} --fields --fillable --guarded --table --primarykey
```

For example, to create a Post model:

```
php artisan crud:model Post --fields='title; body; author; category'
```

> Note that the fields must be separated by a semicolon ( ; )


### Migration Command

```
php artisan crud:migraton {Model} --fields
```

For example, to create a Post migration:

```
php artisan crud:migration Post --fields='title:string(50):unique(); body:text(100); author:string(20):nullable(); category:string(20):default("article")'
```

> Note that the fields must be separated by a semicolon ( ; ) and the options must be separated by a color ( : )


### Controller Command

```
php artisan crud:controller {Model} --fields --paginate --validate --middleware
```

For example, to create a Post Controller:

```
php artisan crud:controller Post --fields='title; body; author; category' --validate='title:required|string|min:3; body:required|string|min:50; author:nullable; category:required|exists:category,name'
```

> Note that the fields must be separated by a semicolon ( ; )


### View Command

```
php artisan crud:view {Model} --fields --template
```

For example, to create Post views(index, create, edit, show):

```
php artisan crud:view Post --fields='title:string; body:text; author:string; category:select:options={article, program, news}'
```

> Note that the fields must be separated by a semicolon ( ; )


### "All" Command

```
php artisan crud:all {Model} --fields --fillable --guarded --table --primarykey --paginate --validate --middleware --template
```

For example, to create a complete CRUD for Post including Model, Migration, Controller and Views:

```
php artisan crud:all Post --fields='title:string(50):unique(); body:text(100); author:string(20):nullable(); category:select(20):options={article, program, news}:default("article")'
```

## License

The package is open source and licensed under the MIT license.
