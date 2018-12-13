<?php

namespace Rajmasha\SimpleCrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ControllerGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:controller {model} {--fields=} {--paginate=} {--validate=} {--middleware=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Controller Generator';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = $this->argument('model');
        $fields = $this->option('fields');
        $perPage = $this->option('paginate') ?? 15;
        $validate = $this->option('validate');
        $middleware = $this->option('middleware');

        $fields_arr = $fields==null ? null : $this->getSplittedFields($fields);

        $middlewareSection = $middleware ? $this->generateMiddlewareSection($middleware) : null;

        $queryString = $this->generateQueryString($model, $fields_arr, $perPage);

        $validation_arr = ($validate == null) ? null : $this->getSplittedFields($validate);

        $validationSection = $validation_arr ? $this->generateValidationSection($validation_arr) : null;


        $this->controller($model, $middlewareSection, $queryString, $perPage, $validationSection);


        File::append(base_path('routes/web.php'), "\n".'Route::resource(\'' . strtolower(str_plural($model)) . "', '{$model}Controller');");

        $this->info('Controller generated successfully.');

    }


    protected function getSplittedFields($fields)
    {
        return preg_split('/;\s?(?![^()]*\))/', $fields);
    }


    protected function getSplittedParts($fields)
    {
        return preg_split('/,\s?(?![^()]*\))/', $fields);
    }


    protected function getStub($type)
    {
        return file_get_contents(resource_path("simple-crud/stubs/$type.stub"));
    }


    protected function generateMiddlewareSection($middleware)
    {
        $middleware = $this->getSplittedParts($middleware);

        $middlewareString = "'" . implode("', '", $middleware) . "'";

        return "public function __construct()\n\t{\n\t\t\$this->middleware([$middlewareString]);\n\t}\n";
    }


    protected function generateQueryString($model, $fields_arr, $perPage)
    {

        if ($fields_arr == null)
        {
            return '$'.strtolower(str_plural($model)).' = '.$model.'::latest()->paginate($perPage);';
        }

        $queryString = "$".strtolower(str_plural($model))." = ".$model."::where(function (\$query) use(\$keyword){\n\t\t\t\$query";


        foreach ($fields_arr as $index => $item)
        {
            $queryString .= ($index == 0) ? "->where('$item', 'LIKE', \"%\$keyword%\")" : "\n\t\t\t\t->orWhere('$item', 'LIKE', \"%\$keyword%\")";

        }

        $queryString .= ";\n\t\t})->latest()->paginate(\$perPage);";

        return $queryString;
    }


    protected function generateValidationSection($validation_arr)
    {

        $validationSection = "\$this->validate(\$request, [\n";

        // Break validation rule into two parts with the first occurence of : symbol
        foreach ($validation_arr as $key => $value)
        {
            $rule = preg_split('/:/', $value, 2);

            $validationSection .= "\t\t\t'".$rule[0]."' => '".$rule[1]."',\n";
        }

        $validationSection .= "\t\t]);\n";

        return $validationSection;

    }


    protected function controller($model, $middlewareSection, $queryString, $perPage, $validationSection)
    {

        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{middlewareSection}}',
                '{{perPage}}',
                '{{querySection}}',
                '{{validationSection}}'
            ],
            [
                $model,
                strtolower(str_plural($model)),
                strtolower($model),
                $middlewareSection,
                $perPage,
                $queryString,
                $validationSection
            ],
            $this->getStub('controller')
        );


        file_put_contents(app_path("/Http/Controllers/{$model}Controller.php"), $controllerTemplate);
    }


}
