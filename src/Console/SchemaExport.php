<?php

namespace Pmurkin\MongoSchemaDumper\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SchemaExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:export 
        {--databases= : List of databases for export} 
        {--dump= : list of collections for dump}
        {--file=./schema.json : file with exported data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export schemas mongo databases to file';

    /**
     * SchemaExport constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $result = [];

        $databases = $this->option('databases');
        if (!$databases) {
            $this->warn('Not defined databases');
            return;
        }

        $databases = explode(',', $databases);
        if (count($databases) === 0) {
            $this->warn('Not defined databases');
            return;
        }

        $dump = $this->option('dump');
        $file = $this->option('file');

        foreach ($databases as $database) {
            $result[$database] = $this->getInfo($database);
        }

        if ($dump) {
            $dump = explode(',', $dump);
            foreach ($dump as $collection) {
                $collection = explode('.', $collection);
                $data = DB::getMongoClient()->selectCollection($collection[0], $collection[1])->find()->toArray();

                foreach ($data as $idx => $item) {
                    $data[$idx]['_id'] = (string)$item['_id'];
                }

                $result[$collection[0]][$collection[1]]['data'] = $data;
            }
        }

        file_put_contents($file, json_encode($result, JSON_PRETTY_PRINT));

        $this->info('Export is done');
    }

    /**
     * @param $database
     * @return array
     */
    private function getInfo($database)
    {
        $result = [];

        $collections = DB::getMongoClient()->selectDatabase($database)->listCollections();
        foreach($collections as $collection) {
            $collectionName = $collection->getName();
            $result[$collectionName] = [
                'indexes' => [],
                'options' => $collection->getOptions(),
            ];

            $indexes = DB::getMongoClient()->selectCollection($database, $collectionName)->listIndexes();
            foreach($indexes as $index) {
                $idx = [
                    'name' => $index->getName(),
                    'ns' => $index->getNamespace(),
                    'key' => $index->getKey(),
                    'v' => $index->getVersion(),
                ];

                if ($index->isUnique()) {
                    $idx['unique'] = true;
                }

                if ($index->isSparse()) {
                    $idx['sparse'] = true;
                }

                $result[$collectionName]['indexes'][] = $idx;
            }
        }

        return $result;
    }
}
