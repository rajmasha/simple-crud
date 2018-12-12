<?php

namespace Rajmasha\SimpleCrud\Commands;

use Illuminate\Console\Command;

class ModelGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:model
    {model} {--fields=} {--fillable=} {--guarded=} {--table=} {--primarykey=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Model Generator';

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
        $fillable = $this->option('fillable');
        $guarded = $this->option('guarded');
        $table = $this->option('table');
        $primaryKey = $this->option('primarykey');

        $fillable = $fillable ?? $fields;

        $fillableFields = $fillable ? $this->formatFields($fillable, 'fillable') : null;

        $guardedFields = $guarded ? $this->formatFields($guarded, 'guarded') : null;

        $table = $table ? $this->formatTable($table) : null;
        $primaryKey = $primaryKey ? $this->formatPrimaryKey($primaryKey) : null;

        $this->generateModel($model, $fillableFields, $guardedFields, $table, $primaryKey);

        $this->info('Model generated successfully.');
    }


    protected function formatFields($fields, $type)
    {
        $fields_arr = $this->getSplittedFields($fields);

        $fieldsFormat = "['" . implode("', '", $fields_arr) . "']";


        if ($type == 'fillable')
        {
            $syntax = "/**\n\t* Attributes that can be mass assigned.\n\t*\n\t* @var array\n\t*/\n\t";
        }
        else
        {
            $syntax = "/**\n\t* Attributes that are guarded from mass assignment.\n\t*\n\t* @var array\n\t*/\n\t";
        }

        $syntax .= "protected \$".$type." = ".$fieldsFormat.";\n";

        return $syntax;
    }


    protected function formatTable($tableName)
    {
        return $tableName = "/**\n\t* Table used by the model\n\t*\n\t* @var string\n\t*/\n\tprotected \$table = '".$tableName."';\n";
    }


    protected function formatPrimaryKey($primaryKey)
    {
        return $primaryKey = "/**\n\t* Primary key of the table\n\t*\n\t* @var string\n\t*/\n\tprotected \$primaryKey = '".$primaryKey."';\n";
    }


    protected function getSplittedFields($fields)
    {
        return preg_split('/;\s?(?![^()]*\))/', $fields);
    }


    protected function getStub($type)
    {
        return file_get_contents(resource_path("simple-crud/stubs/$type.stub"));
    }


    protected function generateModel($model, $fillableFields, $guardedFields, $tableName, $primaryKey)
    {

        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{tableName}}',
                '{{primaryKey}}',
                '{{fillableFields}}',
                '{{guardedFields}}',
            ],
            [
                $model,
                $tableName,
                $primaryKey,
                $fillableFields,
                $guardedFields,
            ],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/{$model}.php"), $modelTemplate);
    }


}
