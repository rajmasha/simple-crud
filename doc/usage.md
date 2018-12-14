
# Usage

|Command   |Syntax         |Description                                   |
|----------|---------------|----------------------------------------------|
|Model     |crud:model     |Creates a Model                               |
|Migration |crud:migration |Creates a Migration                           |
|Controller|crud:controller|Creates a Controller                          |
|Views     |crud:view      |Creates all views                             |
|All       |crud:all       |Creates Model, Migration, Controller and Views|


## Model Command

Model command is used to create a model.

```
php artisan crud:model {Model} {--fields} {--fillable} {--guarded} {--table} {--primarykey}
```

For example, to create a Post model:

```
php artisan crud:model Post --fields='title; body; author; category'
```

#### Options

 - --fields='field1; field2;....fieldn'

 Provide the list of fields that you want in the model to appear in the fillable array. This is same as fillable option.

 > Note that the fields must be separated by a semicolon ( ; )

 - --fillable='field1; field2;....fieldn'

 Provide the list of fields that you want in the model to appear in the fillable array.

 - --guarded='field1; field2;....fieldn'

 Provide the list of fields that you want in the model to appear in the guarded array.

 - --table=tablename

 Provide the table name that you want to use for the model. By default, Laravel will use the lowercase plural of the Model name as the table name.

 - --primarykey=key

 Provide the table column that you want to use as the primary key. Default is id.


## Migration Command

Migration command is used to create a migration file.

```
php artisan crud:migraton {Model} {--fields}
```

For example, to create a Post migration:

```
php artisan crud:migration Post --fields='title:string(50):unique(); body:text(100); author:string(20):nullable(); category:string(20):default("article")'
```

#### Options

 - --fields='field1:type1:options; field2:type2:options;....'

 Provide the list of fields along with its type and options.

 > Note that the fields must be separated by a semicolon ( ; )

 #### Syntax:

 name : type : option1 : option2...

 Example:

 ```
 title:string(50):unique():nullable():default("Title")
 ```

 Output:

 ```
 $table->string('title', 50)->unique()->nullable()->default("Title");
 ```



## Controller Command

Controller command is used to create a resource controller.

```
php artisan crud:controller {Model} {--fields} {--paginate} {--validate} {--middleware}
```

For example, to create a Post Controller:

```
php artisan crud:controller Post --fields='title; body; author; category' --validate='title:required|string|min:3; body:required|string|min:50; author:nullable; category:required|exists:category,name'
```


#### Options

 - --fields='field1; field2;...fieldn'

 Provide the list of fields.

 > Note that the fields must be separated by a semicolon ( ; )

 - --paginate

 Provide a number eg. 10 which will be used to paginate the result on the index view. Default value is 15.

 - --validate='field1:rule1|rule2; field2:rule3|rule4;...'

 Provide field name along with the validation rules (separated with |) for each field.

 > Note that the fields must be separated by a semicolon ( ; ) and the rules must be separated by |

 #### Syntax:

 name : rule1 | rule2 |...

 Example:

 ```
 category:required|string|exists:category,name
 ```

 Output:
In the **store** and **update** method of the Controller, the following will be generated:

 ```
 $this->validate($request, [
	...
	'category' => 'required|string|exists:category,name',
	...
 ]);
 ```

 - --middleware='middleware1, middleware2'

 Provide the list of middleware you want to use in the controller. This will create a **contructor** in the controller and add the middlewares.

 Example:

 ```
 --middleware='auth, check'
 ```

 Output:

 ```
 public function __construct()
 {
    $this->middleware(['auth', 'check']);
 }
 ```


## View Command

View command is used to create all the views such as index, create, edit and show.

```
php artisan crud:view {Model} --fields --template
```

For example, to create Post views(index, create, edit, show):

```
php artisan crud:view Post --fields='title:string; body:text; author:string; category:select:options={article, program, news}'
```


#### Options

 - --fields='field1:type1; field2:type2;....fieldn:typen'

 Provide the list of fields that you want in the views to appear.

 > Note that the fields must be separated by a semicolon ( ; )

 - --template=customtemplate

 You can optionally provide a template name that you want to use instead of the default - **bootstrap4**. This is discussed in detail in the [Custom Template](template.md) page.


## "All" Command

The "All" command is used to generate model, migration, controller and views with a single command.

```
php artisan crud:all {Model} --fields --fillable --guarded --table --primarykey --paginate --validate --middleware --template
```

For example, to create a complete CRUD for Post including Model, Migration, Controller and Views:

```
php artisan crud:all Post --fields='title:string(50):unique(); body:text(100); author:string(20):nullable(); category:select(20):options={article, program, news}:default("article")'
```

This will generate Post:
 - Model - Post
 - migration file - create_posts_table
 - Controller - PostController
 - Views (index, create, edit, show) - posts/

