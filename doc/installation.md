
# Installation

Require this package in composer.json of your Laravel project:

```
composer require rajmasha/simple-crud
```

Once installed, publish the assets using `vendor:publish` artisan command:

```
php artisan vendor:publish --provider="Rajmasha\SimpleCrud\SimpleCrudServiceProvider"
```

This will create:
1. **simple-crud/** in the **resources** folder of your project.

**simple-crud** directory stores all the stubs required for generation.

2. **layouts/** and **partials/** in the path **/resources/views/**

**layouts/** stores the **app.blade.php** file which is the main layout file.

**partials/** stores the *.blade* files for components such as searchbox, menu etc.

