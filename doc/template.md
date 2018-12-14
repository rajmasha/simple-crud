
# Custom Template

The default template (**bootstrap4**), located at */resources/simple-crud/stubs/views/*, is used to generate views and fields.

In order to use your custom template, create a folder at */resources/simple-crud/stubs/views/* with a name eg. **mytemplate** and create all the required stubs that are stored in the default **bootstrap4** directory.

It is recommended that you copy all the files from **bootstrap4** directory into your new template directory and then modify the files according to your need.

To generate views using your template, use the **--template** option of the **View command** as follows:

```
php artisan crud:view Post --fields='title:string; body:text; author:string;' --template=mytemplate
```

So, now the views will be generated as per the **mytemplate** template.
