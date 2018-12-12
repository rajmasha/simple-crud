<?php

namespace Rajmasha\SimpleCrud\Commands;

use Illuminate\Console\Command;

class ViewGeneratorCommand extends Command
{
    protected $template;

    protected $typeMapping = [
        'number' => 'number',
        'biginteger' => 'number',
        'integer' => 'number',
        'mediuminteger' => 'number',
        'smallinteger' => 'number',
        'tinyinteger' => 'number',
        'unsignedbiginteger' => 'number',
        'unsignedinteger' => 'number',
        'unsignedmediuminteger' => 'number',
        'unsignedsmallinteger' => 'number',
        'unsignedtinyinteger' => 'number',
        'float' => 'number',
        'decimal' => 'number',
        'unsigneddecimal' => 'number',
        'double' => 'number',

        'string' => 'text',
        'char' => 'text',

        'text' => 'textarea',
        'longtext' => 'textarea',
        'mediumtext' => 'textarea',

        'json' => 'textarea',
        'jsonb' => 'textarea',
        'binary' => 'textarea',
        'password' => 'password',
        'email' => 'email',

        'date' => 'date',

        'datetime' => 'datetime-local',
        'datetimetz' => 'datetime-local',
        'timestamp' => 'datetime-local',
        'timestamptz' => 'datetime-local',

        'time' => 'time',
        'timetz' => 'time',

        'boolean' => 'checkbox',
        'select' => 'select',
        'enum' => 'select',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:view
    {model} {--fields=} {--template=bootstrap4}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View Generator';

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

        $this->template = $this->option('template');

        $fields_arr = $fields==null ? null : $this->getSplittedFields($fields);

        $this->view($model, $fields_arr);

        $this->info('Views generated successfully.');
    }


    protected function getSplittedFields($fields)
    {
        return preg_split('/;\s?(?![^()]*\))/', $fields);
    }


    protected function view($model, $fields_arr)
    {
        $this->indexView($model, $fields_arr);
        $this->showView($model, $fields_arr);
        $this->formView($model, $fields_arr);
        $this->createView($model, $fields_arr);
        $this->editView($model, $fields_arr);
    }


    protected function indexView($model, $fields_arr)
    {
        $tableHeaders = '';
        $tableValues = '';

        foreach ($fields_arr as $key => $value)
        {
            list($name, $type) = explode(':', $value);

            $tableHeaders .= "<th>".ucfirst($name)."</th>\n\t\t\t\t\t\t\t";

            $tableValues .= "<td>{{ $".strtolower($model)."->".$name." }}</td>\n\t\t\t\t\t\t\t";
        }


        $indexViewTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{tableHeaders}}',
                '{{tableValues}}'
            ],
            [
                $model,
                str_plural($model),
                strtolower(str_plural($model)),
                strtolower($model),
                $tableHeaders,
                $tableValues
            ],
            $this->getStub('index')
        );

        $this->generateViewFile($model, $indexViewTemplate, 'index');
    }



    protected function formView($model, $fields_arr)
    {
        $formFieldTemplate = '';

        foreach ($fields_arr as $key => $value)
        {
            $segments = explode(':', $value);

            $name = array_shift($segments);
            $type = array_shift($segments);

            if (preg_match('/([a-zA-Z]+)\(([^)]+)\)/', $type, $matches))
            {
                $type = $matches[1];
            }

            if (array_key_exists($type, $this->typeMapping))
            {
                $type = $this->typeMapping[$type];
            }

            $optionsSection = '';

            if ($type == "select")
            {
                if (preg_match("/options={([a-zA-Z\W]*)}/", array_shift($segments), $matches))
                {
                    $options = preg_split("/,\s?/", $matches[1]);

                    foreach ($options as $key => $value)
                    {
                        $optionsSection .= "<option value='".$value."'

            @if(old('".$name."'))
                {{ old('".$name."') == $".$value." ?
            'selected' : '' }}

            @elseif(isset($".strtolower($model)."))
                {{ $".strtolower($model)."->".$name." == '".$value."' ? 'selected' : '' }}
            @endif

            >".ucfirst($value)."</option>\n\n\t\t";
                    }
                }
                else
                {
                    $optionsSection = '{{-- Options --}}';
                }
            }

            $formFieldTemplate .= str_replace(
                [
                    '{{name}}',
                    '{{upperName}}',
                    '{{type}}',
                    '{{modelNameSingularLowerCase}}',
                    '{{fieldNamePluralLowerCase}}',
                    '{{optionsSection}}',
                ],
                [
                    $name,
                    ucfirst($name),
                    $type,
                    strtolower($model),
                    strtolower(str_plural($name)),
                    $optionsSection,
                ],
                $this->getFieldStub($type)
            );

            $formFieldTemplate .= "\n";

        }

        $formViewTemplate = str_replace(
            [
                '{{fields}}',
            ],
            [
                $formFieldTemplate,
            ],
            $this->getStub('form')
        );

        $this->generateViewFile($model, $formViewTemplate, 'form');
    }


    protected function createView($model, $fields_arr)
    {

        $createViewTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
            ],
            [
                $model,
                str_plural($model),
                strtolower(str_plural($model)),
                strtolower($model),
            ],
            $this->getStub('create')
        );

        $this->generateViewFile($model, $createViewTemplate, 'create');
    }


    protected function editView($model, $fields_arr)
    {

        $editViewTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
            ],
            [
                $model,
                str_plural($model),
                strtolower(str_plural($model)),
                strtolower($model),
            ],
            $this->getStub('edit')
        );

        $this->generateViewFile($model, $editViewTemplate, 'edit');
    }


    protected function showView($model, $fields_arr)
    {
        $fieldSection = "\n\t\t\t\t\t\t";

        foreach ($fields_arr as $key => $value)
        {
            list($value, $type) = explode(':', $value);

            $fieldSection .= "<tr>\n\t\t\t\t\t\t\t<th>".ucfirst($value)."</th>\n\t\t\t\t\t\t\t<td>{{ $".strtolower($model)."->".$value." }}</td>\n\t\t\t\t\t\t</tr>\n\n\t\t\t\t\t\t";
        }


        $showViewTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{fieldSection}}'
            ],
            [
                $model,
                str_plural($model),
                strtolower(str_plural($model)),
                strtolower($model),
                $fieldSection
            ],
            $this->getStub('show')
        );

        $this->generateViewFile($model, $showViewTemplate, 'show');
    }


    protected function getStub($type)
    {
        return file_get_contents(resource_path("simple-crud/stubs/views/$this->template/$type.stub"));
    }


    protected function getFieldStub($type)
    {
        switch ($type)
        {
            case 'password':
                $fieldType = 'password';
                break;

            case 'textarea':
                $fieldType = 'textarea';
                break;

            case 'select':
                $fieldType = 'select';
                break;

            case 'checkbox':
                $fieldType = 'checkbox';
                break;

            default:
                $fieldType = 'default';
                break;
        }

        return file_get_contents(resource_path("simple-crud/stubs/views/$this->template/fields/$fieldType.stub"));
    }


    protected function generateViewFile($model, $viewTemplate, $viewType)
    {

        if(!is_dir(resource_path("/views/".strtolower(str_plural($model))."/")))
        {
            mkdir(resource_path("/views/".strtolower(str_plural($model))."/"));
        }

        file_put_contents(resource_path("/views/".strtolower(str_plural($model))."/".$viewType.".blade.php"), $viewTemplate);
    }

}
