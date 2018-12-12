<?php

namespace Rajmasha\SimpleCrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CrudGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:all
    {model} {--fields=} {--fillable=} {--guarded=} {--table=} {--primarykey=} {--paginate=} {--validate=} {--middleware=} {--template=bootstrap4}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to generate Model, Migration, Controller, Views';

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
        $perPage = $this->option('paginate');
        $validate = $this->option('validate');
        $middleware = $this->option('middleware');
        $template = $this->option('template');

        $fillable = $fillable ? $this->getSplittedFields($fillable) : $this->getSplittedFields($fields);

        foreach ($fillable as $key => $value)
        {
            $fields_arr[] = explode(':', $value, 2)[0];
        }

        $fieldsForModel = implode('; ', $fields_arr);
        $fieldsForController = implode('; ', $fields_arr);

        $modelCommand = Artisan::call('crud:model', ['model' => $model, '--fields' => $fieldsForModel, '--fillable' => $fieldsForModel, '--guarded' => $guarded, '--table' => $table, '--primarykey' => $primaryKey]);

        $controllerCommand = Artisan::call('crud:controller', ['model' => $model, '--fields' => $fieldsForController, '--paginate' => $perPage, '--validate' => $validate, '--middleware' => $middleware]);

        $migrationCommand = Artisan::call('crud:migration', ['model' => $model, '--fields' => $fields]);

        $viewCommand = Artisan::call('crud:view', ['model' => $model, '--fields' => $fields, '--template' => $template]);

        $this->info('Model, Migration, Controller and Views generated successfully.');
    }


    protected function getSplittedFields($schema)
    {
        return preg_split('/;\s?(?![^()]*\))/', $schema);
    }


}
