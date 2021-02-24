<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSearchIndex extends Command
{
    protected $models = [
        '\App\Article',
        '\App\News',
        '\App\Company',
        '\App\Interview',
        '\App\Event',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'searchindex:generate {model?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate search index';

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
        if ($model = $this->argument('model')) {
            $this->updateIndex($model);

            return;
        }

        $this->deleteIndex();

        foreach ($this->models as $model) {
            $this->updateIndex($model);
        }
    }

    protected function updateIndex($model)
    {
        echo "Updating index in model $model...";

        $elements = $model::all();

        foreach ($elements as $element) {
            $element->updateSearchIndex();
        }

        echo "Done!" . PHP_EOL;
    }

    protected function deleteIndex()
    {
        echo "Deleting search index...";

        \DB::table('search')->truncate();

        echo "Done!" . PHP_EOL;
    }
}
