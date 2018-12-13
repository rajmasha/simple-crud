<?php

namespace Rajmasha\SimpleCrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrationGeneratorCommand extends Command
{

    protected $typeMapping = [
        'password' => 'string',
        'select' => 'string',
        'email' => 'string',
        'number' => 'integer',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:migration
    {model} {--fields=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migration Generator';

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
        $schema = $this->option('fields');

        $fields = $this->getSplittedFields($schema);

        $migrationSection = $this->generateMigrationSection($fields);

        $migrationFileName = $this->generateMigrationFileName($model);

        $this->migration($model, $migrationSection, $migrationFileName);

        $this->info('Migration generated successfully.');
    }


    protected function getSplittedFields($schema)
    {
        return preg_split('/;\s?(?![^()]*\))/', $schema);
    }


    protected function generateMigrationSection($fields)
    {
        $migrationSection = '';

        foreach ($fields as $field)
        {
            $segments = explode(':', $field);

            $migrationSection .= $this->parseSegments($segments);
        }

        return $migrationSection;
    }


    protected function parseSegments($segments)
    {
        $name = array_shift($segments);
        $type = array_shift($segments);

        if (preg_match('/([a-zA-Z]+)\(([^)]+)\)/', $type, $matches))
        {
            $type = $matches[1];

            if (array_key_exists($type, $this->typeMapping))
            {
                $newType = $this->typeMapping[$type];
            }
            else
                $newType = $type;

            $field = "\$table->$newType('$name', $matches[2])";
        }
        else
        {
            if (array_key_exists($type, $this->typeMapping))
            {
                $newType = $this->typeMapping[$type];
            }
            else
                $newType = $type;

            $field = "\$table->$newType('$name')";
        }


        foreach ($segments as $key => $value)
        {
            // Remove options={} segment if present
            if (preg_match("/options={[\s\S]*}/", $value))
                continue;

            $field .= "->$value";
        }

        return $field .= ";\n\t\t\t";

    }


    protected function generateMigrationFileName($model)
    {
        $tableName = snake_case(str_plural($model));

        return date('Y_m_d_His').'_create_'.$tableName.'_table.php';
    }


    protected function getStub($type)
    {
        return file_get_contents(resource_path("simple-crud/stubs/$type.stub"));
    }


    protected function migration($model, $migrationSection, $migrationFileName)
    {

        $migrationTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNameSnakeCasePlural}}',
                '{{migrationSection}}'
            ],
            [
                $model,
                str_plural($model),
                snake_case(str_plural($model)),
                $migrationSection
            ],
            $this->getStub('migration')
        );

        file_put_contents(database_path("/migrations/".$migrationFileName), $migrationTemplate);
    }

}
